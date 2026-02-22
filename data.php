<?php
include __DIR__."/main-function.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header("Access-Control-Allow-Origin: *");

header('Content-Type: application/json');

$main   = new Main($conn);
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'feeds':
        $feeds = [
            "users"  => $main->getFeedsBySource('user', 10),
            "pages"  => $main->getFeedsBySource('page', 10),
            "groups" => $main->getFeedsBySource('group', 10),
            "events" => $main->getFeedsBySource('event', 10),
            "market" => $main->getFeedsBySource('market', 10),
            "member" => $main->getUserData()
        ];
        echo json_encode(["success" => true, "feeds" => $feeds]);
        break;

        case 'like':
            $feedId = intval($_POST['feed_id'] ?? 0);
            $userId = intval($_SESSION['id'] ?? 0);

            if ($feedId > 0 && $userId > 0) {
                // Check if user already liked
                $stmt = $conn->prepare("SELECT 1 FROM feed_likes WHERE feed_id=? AND user_id=? LIMIT 1");
                $stmt->bind_param('ii', $feedId, $userId);
                $stmt->execute();
                $res = $stmt->get_result();

                if ($res->num_rows > 0) {
                    // Already liked → unlike
                    $stmt = $conn->prepare("DELETE FROM feed_likes WHERE feed_id=? AND user_id=?");
                    $stmt->bind_param('ii', $feedId, $userId);
                    $stmt->execute();

                    $stmt = $conn->prepare("UPDATE feeders SET likes_count = likes_count - 1 WHERE id=? AND likes_count > 0");
                    $stmt->bind_param('i', $feedId);
                    $stmt->execute();

                    $action = "unliked";
                } else {
                    // Not liked yet → like
                    $stmt = $conn->prepare("INSERT INTO feed_likes (feed_id, user_id) VALUES (?, ?)");
                    $stmt->bind_param('ii', $feedId, $userId);
                    $stmt->execute();

                    $stmt = $conn->prepare("UPDATE feeders SET likes_count = likes_count + 1 WHERE id=?");
                    $stmt->bind_param('i', $feedId);
                    $stmt->execute();

                    $action = "liked";
                }

                // Fetch updated count
                $stmt = $conn->prepare("SELECT likes_count FROM feeders WHERE id=?");
                $stmt->bind_param('i', $feedId);
                $stmt->execute();
                $res = $stmt->get_result();
                $row = $res->fetch_assoc();

                echo json_encode([
                    "success" => true,
                    "action" => $action,
                    "likes_count" => $row['likes_count']
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Invalid feed or user"
                ]);
            }
    break;

    case 'comment':
        $feedId   = intval($_POST['feed_id'] ?? 0);
    $userId   = intval($_SESSION['id'] ?? 0);
    $parentId = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
    $comment  = trim($_POST['comment'] ?? '');

    if ($feedId > 0 && $userId > 0 && $comment !== '') {
        $stmt = $conn->prepare("INSERT INTO feed_comments (feed_id, user_id, parent_id, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiis', $feedId, $userId, $parentId, $comment);
        $stmt->execute();

        echo json_encode(["success" => true, "comments" => []]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid data"]);
    }
    break;



    
case 'like_comment':
    $commentId = intval($_POST['comment_id'] ?? 0);
    $userId    = intval($_SESSION['id'] ?? 0);

    if ($commentId > 0 && $userId > 0) {
        // Check if already liked
        $stmt = $conn->prepare("SELECT 1 FROM comment_likes WHERE comment_id=? AND user_id=?");
        $stmt->bind_param('ii', $commentId, $userId);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            // Unlike
            $stmt = $conn->prepare("DELETE FROM comment_likes WHERE comment_id=? AND user_id=?");
            $stmt->bind_param('ii', $commentId, $userId);
            $stmt->execute();
            $action = "unliked";
        } else {
            // Like
            $stmt = $conn->prepare("INSERT INTO comment_likes (comment_id, user_id) VALUES (?, ?)");
            $stmt->bind_param('ii', $commentId, $userId);
            $stmt->execute();
            $action = "liked";
        }

        // Count likes
        $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM comment_likes WHERE comment_id=?");
        $stmt->bind_param('i', $commentId);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        echo json_encode([
            "success" => true,
            "action" => $action,
            "likes_count" => $row['cnt']
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid data"]);
    }
    break;


case 'get_comments':
    $feedId = intval($_POST['feed_id'] ?? 0);

    if ($feedId > 0) {
        // Fetch top-level comments
        $stmt = $conn->prepare("
            SELECT fc.id, fc.comment, fc.created_at,
                   (SELECT COUNT(*) FROM feed_comments WHERE parent_id = fc.id) AS reply_count,
                   (SELECT COUNT(*) FROM comment_likes WHERE comment_id = fc.id) AS like_count,
                   JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.username')) AS username,
                   JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.profile_pic')) AS profile_pic
            FROM feed_comments fc
            JOIN users u ON fc.user_id = u.id
            WHERE fc.feed_id = ? AND fc.parent_id IS NULL
            ORDER BY fc.created_at DESC
            LIMIT 10
        ");
        $stmt->bind_param('i', $feedId);
        $stmt->execute();
        $res = $stmt->get_result();

        $comments = [];
        while ($row = $res->fetch_assoc()) {
            // Fetch first 3 replies for each comment
            $stmt2 = $conn->prepare("
                SELECT fc.id, fc.comment, fc.created_at,
                       (SELECT COUNT(*) FROM comment_likes WHERE comment_id = fc.id) AS like_count,
                       JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.username')) AS username,
                       JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.profile_pic')) AS profile_pic
                FROM feed_comments fc
                JOIN users u ON fc.user_id = u.id
                WHERE fc.parent_id = ?
                ORDER BY fc.created_at ASC
                LIMIT 3
            ");
            $stmt2->bind_param('i', $row['id']);
            $stmt2->execute();
            $res2 = $stmt2->get_result();

            $replies = [];
            while ($r = $res2->fetch_assoc()) {
                $replies[] = $r;
            }

            $row['replies'] = $replies;
            $comments[] = $row;
        }

        echo json_encode([
            "success" => true,
            "comments" => $comments
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid feed ID"]);
    }
    break;


case 'createPost':
        $content = $_POST['content'] ?? '';
        $source_id = $_POST['source_id'] ?? 0;
        $source_type = $_POST['source_type'] ?? 'user';
        
        $uploaded_files = [];
        $upload_dir = __DIR__.'/uploads/feed/';

        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // 1. Handle Multiple File Uploads
        if (!empty($_FILES['media']['name'][0])) {
            foreach ($_FILES['media']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['media']['name'][$key];
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                
                // Generate unique name to prevent overwriting
                $new_name = uniqid('post_', true) . '.' . $file_ext;
                $target_path = $upload_dir . $new_name;

                if (move_uploaded_file($tmp_name, $target_path)) {
                    $uploaded_files[] = $target_path;
                }
            }
        }

        // 2. Prepare JSON for the database
        // We store it as {"images": ["path/1.jpg", "path/2.jpg"]}
        $media_url = json_encode(['images' => $uploaded_files]);

        try {
            $sql = "INSERT INTO feeders (source_id, source_type, content, media_url) 
                    VALUES (:source_id, :source_type, :content, :media_url)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':source_id'   => $source_id,
                ':source_type' => $source_type,
                ':content'     => $content,
                ':media_url'   => $media_url
            ]);

            $response['success'] = true;
            $response['message'] = "Post created successfully!";
            
        } catch (PDOException $e) {
            $response['message'] = "Database Error: " . $e->getMessage();
        }
        break;



case 'add_friend':
    $userId = intval($_SESSION['id'] ?? 0);
    $friendId = intval($_POST['friend_id'] ?? 0);

    if ($userId > 0 && $friendId > 0) {
        $stmt = $conn->prepare("INSERT INTO friends (user_id, friend_id, status) VALUES (?, ?, 'pending')
                                ON DUPLICATE KEY UPDATE status='pending'");
        $stmt->bind_param('ii', $userId, $friendId);
        $stmt->execute();
        echo json_encode(["success" => true, "message" => "Friend request sent"]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid data"]);
    }
    break;

case 'accept_friend':
    $userId = intval($_SESSION['id'] ?? 0);
    $friendId = intval($_POST['friend_id'] ?? 0);

    $stmt = $conn->prepare("UPDATE friends SET status='accepted' WHERE user_id=? AND friend_id=?");
    $stmt->bind_param('ii', $friendId, $userId); // reverse because friend initiated
    $stmt->execute();
    echo json_encode(["success" => true, "message" => "Friend request accepted"]);
    break;



case 'get_non_friends':
    $userId = intval($_SESSION['id'] ?? 0);

    $stmt = $conn->prepare("
        SELECT u.id,
               JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.username')) AS username,
               JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.profile_pic')) AS profile_pic,
               (
                 SELECT COUNT(*) 
                 FROM friends f1
                 JOIN friends f2 ON f1.friend_id = f2.friend_id
                 WHERE f1.user_id = u.id AND f2.user_id = ? 
                   AND f1.status='accepted' AND f2.status='accepted'
               ) AS mutual_friends
        FROM users u
        WHERE u.id != ?
          AND u.id NOT IN (
            SELECT friend_id FROM friends WHERE user_id = ?
            UNION
            SELECT user_id FROM friends WHERE friend_id = ?
          )
        LIMIT 20
    ");
    $stmt->bind_param('iiii', $userId, $userId, $userId, $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $users = $res->fetch_all(MYSQLI_ASSOC);

    echo json_encode(["success" => true, "users" => $users]);
    break;





case 'get_non_friends_or_pending':
    $userId = intval($_SESSION['id'] ?? 0);

    $stmt = $conn->prepare("
        SELECT u.id,
               JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.username')) AS username,
               JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.profile_pic')) AS profile_pic,
               COALESCE(f.status, 'none') AS status
        FROM users u
        LEFT JOIN friends f 
          ON ( (f.user_id = u.id AND f.friend_id = ?) 
            OR (f.friend_id = u.id AND f.user_id = ?) )
        WHERE u.id != ?
        LIMIT 20
    ");
    $stmt->bind_param('iii', $userId, $userId, $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $users = $res->fetch_all(MYSQLI_ASSOC);

    echo json_encode(["success" => true, "users" => $users]);
    break;



case 'get_friend_requests':
    $userId = intval($_SESSION['id'] ?? 0);

    $stmt = $conn->prepare("
        SELECT u.id, 
               JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.username')) AS username,
               JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.profile_pic')) AS profile_pic,
               f.created_at
        FROM friends f
        JOIN users u ON f.user_id = u.id
        WHERE f.friend_id = ? AND f.status = 'pending'
    ");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $requests = $res->fetch_all(MYSQLI_ASSOC);

    echo json_encode(["success" => true, "requests" => $requests]);
    break;



// -----------------------------profile page-----------------------------------------------------------------


case 'get_user_profile':
    $userId = intval($_SESSION['id'] ?? 0);

    // If profile_id is provided in POST, use it; otherwise fallback to logged-in user
    $profileId = isset($_POST['profile_id']) && !empty($_POST['profile_id'])
        ? intval($_POST['profile_id'])
        : $userId;

    // Profile
    $stmt = $conn->prepare("SELECT id, uid, email,
        JSON_UNQUOTE(JSON_EXTRACT(profile, '$.username')) AS username,
        JSON_UNQUOTE(JSON_EXTRACT(profile, '$.first_name')) AS fn,
        JSON_UNQUOTE(JSON_EXTRACT(profile, '$.last_name')) AS ln,
        JSON_UNQUOTE(JSON_EXTRACT(profile, '$.profile_pic')) AS profile_pic,
        created_at FROM users WHERE id=?");
    $stmt->bind_param('i', $profileId);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();

    // Posts
    $stmt = $conn->prepare("SELECT id, content, media_url, created_at,
        likes_count, comments_count, shares_count
        FROM feeders WHERE source_id=? AND source_type='user'");
    $stmt->bind_param('i', $profileId);
    $stmt->execute();
    $posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Total friends
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_friends
        FROM friends WHERE (user_id=? OR friend_id=?) AND status='accepted'");
    $stmt->bind_param('ii', $profileId, $profileId);
    $stmt->execute();
    $totalFriends = $stmt->get_result()->fetch_assoc()['total_friends'];

    // Mutual friends count
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS mutual_friends
        FROM friends f1
        JOIN friends f2 ON f1.friend_id = f2.friend_id
        WHERE f1.user_id=? AND f2.user_id=? 
          AND f1.status='accepted' AND f2.status='accepted'");
    $stmt->bind_param('ii', $profileId, $userId);
    $stmt->execute();
    $mutualFriends = $stmt->get_result()->fetch_assoc()['mutual_friends'];

    // Friends list (accepted only)
    $stmt = $conn->prepare("
        SELECT u.id,
               JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.username')) AS username,
               JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.first_name')) AS fn,
               JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.last_name')) AS ln,
               JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.profile_pic')) AS profile_pic
        FROM friends f
        JOIN users u 
          ON u.id = CASE 
                      WHEN f.user_id = ? THEN f.friend_id 
                      ELSE f.user_id 
                    END
        WHERE (f.user_id = ? OR f.friend_id = ?) 
          AND f.status = 'accepted'
    ");
    $stmt->bind_param('iii', $profileId, $profileId, $profileId);
    $stmt->execute();
    $friends = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        "success" => true,
        "profile" => $profile,
        "posts" => $posts,
        "total_friends" => $totalFriends,
        "mutual_friends" => $mutualFriends,
        "friends" => $friends
    ]);
    break;




    default:
        echo json_encode(["success" => false, "message" => "Invalid action"]);
        break;
}
