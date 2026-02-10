<?php
include __DIR__."/../conn/conn.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Decode JSON payload if sent as raw JSON
$hook = json_decode(file_get_contents('php://input'), true);
$action = $hook['action'] ?? ($_POST['action'] ?? '');

switch ($action) {
    case 'feeds':
        // Pagination: offset and limit
        $offset = $hook['limit'] ?? 0;
        $user_id = intval($_SESSION['id'] ?? 0);

        $stmt = $conn->prepare("
            SELECT 
                f.id, f.user_id, f.images, f.audio, f.video, f.text, f.created_at, f.updated_at,
                u.uid, u.email, u.profile,
                (SELECT COUNT(*) FROM likes l WHERE l.feed_id = f.id) AS like_count,
                (SELECT COUNT(*) FROM comments c WHERE c.feed_id = f.id) AS comment_count,
                EXISTS(SELECT 1 FROM likes l WHERE l.feed_id = f.id AND l.user_id = ?) AS user_liked
            FROM feeds f
            JOIN users u ON f.user_id = u.id
            ORDER BY f.id DESC
            LIMIT ?, 20
        ");
        $stmt->bind_param("ii", $user_id, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];

        while ($row = $result->fetch_assoc()) {
            $profile = json_decode($row['profile'], true) ?? [];
            $row['first_name']   = $profile['first_name'] ?? '';
            $row['last_name']    = $profile['last_name'] ?? '';
            $row['username']     = $profile['username'] ?? '';
            $row['profile_pic']  = $profile['profile_pic'] ?? '';
            $rows[] = $row;
        }

        echo json_encode($rows);
        break;

    case 'upload':
        $text = $_POST['text'] ?? '';

        $uploadDirImages = __DIR__ . "/../uploads/images/";
        $uploadDirAudio  = __DIR__ . "/../uploads/audio/";

        if (!is_dir($uploadDirImages)) mkdir($uploadDirImages, 0777, true);
        if (!is_dir($uploadDirAudio)) mkdir($uploadDirAudio, 0777, true);

        // Handle images
        $uploadedImages = [];
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
                $filename = time() . "_" . basename($_FILES['images']['name'][$index]);
                $target   = $uploadDirImages . $filename;
                if (move_uploaded_file($tmpName, $target)) {
                    $uploadedImages[] = $filename;
                }
            }
        }

        // Handle audio
        $audioFile = "";
        if (!empty($_FILES['audio']['name'])) {
            $filename = time() . "_" . basename($_FILES['audio']['name']);
            $target   = $uploadDirAudio . $filename;
            if (move_uploaded_file($_FILES['audio']['tmp_name'], $target)) {
                $audioFile = $filename;
            }
        }

        // Save to DB
        $stmt = $conn->prepare("INSERT INTO feeds (user_id, text, images, audio) VALUES (?, ?, ?, ?)");
        $userId    = $_SESSION['id'] ?? 0;
        $imagesJson = json_encode($uploadedImages);
        $stmt->bind_param("isss", $userId, $text, $imagesJson, $audioFile);
        $success = $stmt->execute();

        echo json_encode([
            "success" => $success,
            "message" => $success ? "Post uploaded successfully" : "Failed to upload post"
        ]);
        break;

    case 'like':
        $feed_id = intval($hook['feed_id'] ?? $_POST['feed_id'] ?? 0);
        $user_id = intval($_SESSION['id'] ?? 0);

        if ($feed_id && $user_id) {
            // Check if already liked
            $stmt = $conn->prepare("SELECT id FROM likes WHERE feed_id=? AND user_id=?");
            $stmt->bind_param("ii", $feed_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Unlike
                $del = $conn->prepare("DELETE FROM likes WHERE feed_id=? AND user_id=?");
                $del->bind_param("ii", $feed_id, $user_id);
                $del->execute();
                $status = "unliked";
            } else {
                // Like
                $ins = $conn->prepare("INSERT INTO likes (feed_id, user_id, parent_id) VALUES (?, ?, 0)");
                $ins->bind_param("ii", $feed_id, $user_id);
                $ins->execute();
                $status = "liked";
            }

            // Always calculate updated count
            $countStmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM likes WHERE feed_id=?");
            $countStmt->bind_param("i", $feed_id);
            $countStmt->execute();
            $countResult = $countStmt->get_result()->fetch_assoc();
            $count = $countResult['cnt'] ?? 0;

            // Build JSON payload
            $eventData = json_encode([
                "feed_id" => $feed_id,
                "count"   => $count
            ]);

            // Upsert event
            $checkEvt = $conn->prepare("SELECT id FROM events_triger WHERE event_type='like' AND JSON_EXTRACT(data,'$.feed_id')=?");
            $checkEvt->bind_param("i", $feed_id);
            $checkEvt->execute();
            $evtResult = $checkEvt->get_result();

            if ($evtResult->num_rows > 0) {
                $row = $evtResult->fetch_assoc();
                $updateEvt = $conn->prepare("UPDATE events_triger SET data=?, updated_at=NOW() WHERE id=?");
                $updateEvt->bind_param("si", $eventData, $row['id']);
                $updateEvt->execute();
            } else {
                $evt = $conn->prepare("INSERT INTO events_triger (event_type, data) VALUES ('like', ?)");
                $evt->bind_param("s", $eventData);
                $evt->execute();
            }

            echo json_encode(["status" => $status, "count" => $count]);
        } else {
            echo json_encode(["error" => "Invalid feed or user"]);
        }
        break;

    case 'comment':
        $feed_id = intval($hook['feed_id'] ?? $_POST['feed_id'] ?? 0);
        $user_id = intval($_SESSION['id'] ?? 0);
        $text    = trim($hook['comment_text'] ?? $_POST['comment_text'] ?? '');

        if ($feed_id && $user_id && $text !== '') {
            $ins = $conn->prepare("INSERT INTO comments (feed_id, user_id, parent_id, comment_text) VALUES (?, ?, 0, ?)");
            $ins->bind_param("iis", $feed_id, $user_id, $text);
            $ins->execute();
            $status = "commented";

            $countStmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM comments WHERE feed_id=?");
            $countStmt->bind_param("i", $feed_id);
            $countStmt->execute();
            $count = $countStmt->get_result()->fetch_assoc()['cnt'] ?? 0;

            $eventData = json_encode([
                "feed_id"       => $feed_id,
                "comment_count" => $count
            ]);

            $checkEvt = $conn->prepare("SELECT id FROM events_triger WHERE event_type='comment' AND JSON_EXTRACT(data,'$.feed_id')=?");
            $checkEvt->bind_param("i", $feed_id);
            $checkEvt->execute();
            $evtResult = $checkEvt->get_result();

            if ($evtResult->num_rows > 0) {
                $row = $evtResult->fetch_assoc();
                $updateEvt = $conn->prepare("UPDATE events_triger SET data=?, updated_at=NOW() WHERE id=?");
                $updateEvt->bind_param("si", $eventData, $row['id']);
                $updateEvt->execute();
            } else {
                $evt = $conn->prepare("INSERT INTO events_triger (event_type, data) VALUES ('comment', ?)");
                $evt->bind_param("s", $eventData);
                $evt->execute();
            }

            echo json_encode(["status" => $status, "comment_count" => $count]);
        } else {
            echo json_encode(["error" => "Invalid feed, user, or empty comment"]);
        }
        break;

    case 'comment_like':
        $comment_id = intval($hook['comment_id'] ?? $_POST['comment_id'] ?? 0);
        $user_id    = intval($_SESSION['id'] ?? 0);

        if ($comment_id <= 0 || $user_id <= 0) {
            echo json_encode(["error" => "Invalid comment or user not logged in"]);
            break;
        }

        // Check if already liked
        $stmt = $conn->prepare("SELECT id FROM comments_lyk WHERE parent_id=? AND user_id=?");
        $stmt->bind_param("ii", $comment_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Unlike
            $del = $conn->prepare("DELETE FROM comments_lyk WHERE parent_id=? AND user_id=?");
            $del->bind_param("ii", $comment_id, $user_id);
            $del->execute();
            $status = "unliked";
        } else {
            // Like
            $ins = $conn->prepare("INSERT INTO comments_lyk (feed_id, user_id, parent_id, comment_text) VALUES (0, ?, ?, '')");
            $ins->bind_param("ii", $user_id, $comment_id);
            $ins->execute();
            $status = "liked";
        }

        // Get updated like count
        $countStmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM comments_lyk WHERE parent_id=?");
        $countStmt->bind_param("i", $comment_id);
        $countStmt->execute();
        $count = $countStmt->get_result()->fetch_assoc()['cnt'] ?? 0;

        // Trigger real-time event
        $eventData = json_encode([
            "comment_id" => $comment_id,
            "count"      => $count
        ]);

        $checkEvt = $conn->prepare("
            SELECT id FROM events_triger 
            WHERE event_type = 'comment_like' 
            AND JSON_EXTRACT(data, '$.comment_id') = ?
        ");
        $checkEvt->bind_param("i", $comment_id);
        $checkEvt->execute();
        $evtResult = $checkEvt->get_result();

        if ($evtResult->num_rows > 0) {
            $row = $evtResult->fetch_assoc();
            $updateEvt = $conn->prepare("UPDATE events_triger SET data=?, updated_at=NOW() WHERE id=?");
            $updateEvt->bind_param("si", $eventData, $row['id']);
            $updateEvt->execute();
        } else {
            $evt = $conn->prepare("INSERT INTO events_triger (event_type, data) VALUES ('comment_like', ?)");
            $evt->bind_param("s", $eventData);
            $evt->execute();
        }

        echo json_encode([
            "status"     => $status,
            "like_count" => $count
        ]);
        break;

    // case 'comments_list':
    //     $feed_id = intval($hook['feed_id'] ?? $_POST['feed_id'] ?? 0);

    //     if ($feed_id) {
    //         $stmt = $conn->prepare("
    //             SELECT c.id, c.comment_text, c.created_at,
    //                    u.id AS user_id, u.profile, u.uid
    //             FROM comments c
    //             JOIN users u ON c.user_id = u.id
    //             WHERE c.feed_id=?
    //             ORDER BY c.id DESC
    //             LIMIT 50
    //         ");
    //         $stmt->bind_param("i", $feed_id);
    //         $stmt->execute();
    //         $result = $stmt->get_result();
    //         $rows = [];

    //         while ($row = $result->fetch_assoc()) {
    //             $profile = json_decode($row['profile'], true) ?? [];
    //             $row['username']    = $profile['username'] ?? $row['uid'];
    //             $row['profile_pic'] = $profile['profile_pic'] ?? "default.png";
    //             $row['text']        = $row['comment_text'];
    //             unset($row['comment_text']);
    //             $rows[] = $row;
    //         }

    //         echo json_encode($rows);
    //     } else {
    //         echo json_encode(["error" => "Invalid feed ID"]);
    //     }
    //     break;

    // case 'comments_list':
    // $feed_id = intval($hook['feed_id'] ?? $_POST['feed_id'] ?? 0);
    // $user_id = intval($_SESSION['id'] ?? 0);

    // if ($feed_id) {
    //     $stmt = $conn->prepare("
    //         SELECT 
    //             c.id, c.comment_text AS text, c.created_at, c.parent_id,
    //             u.id AS user_id, u.profile, u.uid,
    //             (SELECT COUNT(*) FROM comments_lyk WHERE parent_id = c.id) AS like_count,
    //             EXISTS(SELECT 1 FROM comments_lyk WHERE parent_id = c.id AND user_id = ?) AS user_liked_comment
    //         FROM comments c
    //         JOIN users u ON c.user_id = u.id
    //         WHERE c.feed_id = ?
    //         ORDER BY c.id ASC  -- Changed to ASC so replies appear in order
    //         LIMIT 100
    //     ");
    //     $stmt->bind_param("ii", $user_id, $feed_id);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     $rows = $result->fetch_all(MYSQLI_ASSOC);
    //     echo json_encode($rows);
    // } else {
    //     echo json_encode(["error" => "Invalid feed"]);
    // }
    // break;


    case 'comments_list':
    $feed_id = intval($hook['feed_id'] ?? $_POST['feed_id'] ?? 0);
    $user_id = intval($_SESSION['id'] ?? 0);

    if ($feed_id) {
        $stmt = $conn->prepare("
            SELECT 
                c.id, c.comment_text AS text, c.created_at, c.parent_id,
                u.id AS user_id, u.profile, u.uid,
                (SELECT COUNT(*) FROM comments_lyk WHERE parent_id = c.id) AS like_count,
                EXISTS(SELECT 1 FROM comments_lyk WHERE parent_id = c.id AND user_id = ?) AS user_liked_comment
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.feed_id = ?
            ORDER BY c.id ASC
            LIMIT 100
        ");
        $stmt->bind_param("ii", $user_id, $feed_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        foreach ($rows as &$row) {
            $profile = json_decode($row['profile'], true) ?? [];
            $row['username']    = $profile['username'] ?? $row['uid'];
            $row['profile_pic'] = $profile['profile_pic'] ?? "default.png";
        }

        echo json_encode($rows);
    } else {
        echo json_encode(["error" => "Invalid feed"]);
    }
    break;

    //     case 'reply':
    // $parent_comment_id = intval($hook['comment_id'] ?? 0);
    // $user_id = intval($_SESSION['id'] ?? 0);
    // $text = trim($hook['reply_text'] ?? '');

    // if ($parent_comment_id && $user_id && $text) {
    //     // Get the original feed_id from parent comment
    //     $parentStmt = $conn->prepare("SELECT feed_id FROM comments WHERE id = ?");
    //     $parentStmt->bind_param("i", $parent_comment_id);
    //     $parentStmt->execute();
    //     $feed_id = $parentStmt->get_result()->fetch_assoc()['feed_id'] ?? 0;

    //     if (!$feed_id) {
    //         echo json_encode(["error" => "Parent comment not found"]);
    //         break;
    //     }

    //     $ins = $conn->prepare("INSERT INTO comments (feed_id, user_id, parent_id, comment_text) VALUES (?, ?, ?, ?)");
    //     $ins->bind_param("iiis", $feed_id, $user_id, $parent_comment_id, $text);
    //     $ins->execute();

    //     // Update comment count (same as regular comment)
    //     $countStmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM comments WHERE feed_id=?");
    //     $countStmt->bind_param("i", $feed_id);
    //     $countStmt->execute();
    //     $count = $countStmt->get_result()->fetch_assoc()['cnt'] ?? 0;

    //     // Trigger event (reuse 'comment' type or make new 'reply')
    //     $eventData = json_encode([
    //         "feed_id" => $feed_id,
    //         "comment_count" => $count
    //     ]);

    //     // ... same upsert logic as in 'comment' case ...

    //     echo json_encode(["status" => "replied", "comment_count" => $count]);
    // } else {
    //     echo json_encode(["error" => "Invalid reply data"]);
    // }
    // break;

    case 'reply':
    $parent_id = intval($hook['parent_id'] ?? 0);
    $user_id = intval($_SESSION['id'] ?? 0);
    $text = trim($hook['reply_text'] ?? '');

    if ($parent_id <= 0 || $user_id <= 0 || $text === '') {
        echo json_encode(["error" => "Invalid reply data"]);
        break;
    }

    // Get feed_id from parent comment
    $pStmt = $conn->prepare("SELECT feed_id FROM comments WHERE id = ?");
    $pStmt->bind_param("i", $parent_id);
    $pStmt->execute();
    $feed_id = $pStmt->get_result()->fetch_assoc()['feed_id'] ?? 0;

    if ($feed_id === 0) {
        echo json_encode(["error" => "Parent comment not found"]);
        break;
    }

    $ins = $conn->prepare("INSERT INTO comments (feed_id, user_id, parent_id, comment_text) VALUES (?, ?, ?, ?)");
    $ins->bind_param("iiis", $feed_id, $user_id, $parent_id, $text);
    $ins->execute();

    // Update comment count
    $countStmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM comments WHERE feed_id=?");
    $countStmt->bind_param("i", $feed_id);
    $countStmt->execute();
    $count = $countStmt->get_result()->fetch_assoc()['cnt'] ?? 0;

    // Trigger event (using 'reply' type)
    $eventData = json_encode([
        "feed_id" => $feed_id,
        "comment_count" => $count
    ]);

    $checkEvt = $conn->prepare("SELECT id FROM events_triger WHERE event_type='reply' AND JSON_EXTRACT(data,'$.feed_id')=?");
    $checkEvt->bind_param("i", $feed_id);
    $checkEvt->execute();
    $evtResult = $checkEvt->get_result();

    if ($evtResult->num_rows > 0) {
        $row = $evtResult->fetch_assoc();
        $updateEvt = $conn->prepare("UPDATE events_triger SET data=?, updated_at=NOW() WHERE id=?");
        $updateEvt->bind_param("si", $eventData, $row['id']);
        $updateEvt->execute();
    } else {
        $evt = $conn->prepare("INSERT INTO events_triger (event_type, data) VALUES ('reply', ?)");
        $evt->bind_param("s", $eventData);
        $evt->execute();
    }

    echo json_encode(["status" => "replied", "comment_count" => $count]);
    break;

    case 'events':
        // Clean up old events
        $conn->query("DELETE FROM events_triger WHERE created_at < (NOW() - INTERVAL 25 MINUTE)");

        $stmt = $conn->prepare("
            SELECT id, event_type, data, created_at
            FROM events_triger
            WHERE created_at >= (NOW() - INTERVAL 25 MINUTE)
            ORDER BY id DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];

        while ($row = $result->fetch_assoc()) {
            $row['data'] = json_decode($row['data'], true) ?? [];
            $rows[] = $row;
        }

        echo json_encode($rows);
        break;

    default:
        echo json_encode(["error" => "Unknown or missing action"]);
        break;
}

$conn->close();