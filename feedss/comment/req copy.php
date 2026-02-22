<?php
include __DIR__."/../req.php"; 
class comments extends Main {
    public function __construct($conn) { 
        parent::__construct($conn); 
    }

    private function formatDate($datetime) {
        $dt = new DateTime($datetime);
        return $dt->format('F j \a\t g:i A') . " ·";
    }


public function feedWithComments($feed_id = 0) {
    if ($feed_id === 0) return [];

    // Fetch feed
    $sqlFeed = "
        SELECT f.id AS feed_id,
               f.data,
               f.keywords,
               f.created_at AS feed_created,
               f.status,
               u.id AS user_id,
               u.profile
        FROM feeds f
        JOIN users u ON f.user_id = u.id
        WHERE f.id = ?
    ";
    $smtFeed = $this->conn->prepare($sqlFeed);
    $smtFeed->bind_param('i', $feed_id);
    $smtFeed->execute();
    $feed = $smtFeed->get_result()->fetch_assoc();
    if (!$feed) return [];

    $feed['feed_created'] = $this->formatDate($feed['feed_created']);

    // Fetch top-level comments
    $sqlComments = "
        SELECT c.id AS comment_id,
               c.feed_id,
               c.user_id,
               u.profile,
               c.data,
               c.type,
               c.parent_id,
               c.created_at
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.feed_id = ? AND c.parent_id = 0
        ORDER BY c.created_at ASC
    ";
    $smtComments = $this->conn->prepare($sqlComments);
    $smtComments->bind_param('i', $feed_id);
    $smtComments->execute();
    $comments = $smtComments->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($comments as &$c) {
        $c['data'] = json_decode($c['data'], true);
        $c['created_at'] = $c['created_at'];

        // Like count for this comment
        $likeStmt = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM likes WHERE feed_id=? AND parent_id=? AND type='comment'");
        $likeStmt->bind_param('ii', $feed_id, $c['comment_id']);
        $likeStmt->execute();
        $c['like_count'] = $likeStmt->get_result()->fetch_assoc()['cnt'];

        // Replies (limit 3)
        $sqlReplies = "
            SELECT r.id AS reply_id,
                   r.feed_id,
                   r.user_id,
                   u.profile,
                   r.data,
                   r.type,
                   r.parent_id,
                   r.created_at
            FROM comments r
            JOIN users u ON r.user_id = u.id
            WHERE r.feed_id = ? AND r.parent_id = ?
            ORDER BY r.created_at ASC
            LIMIT 3
        ";
        $smtReplies = $this->conn->prepare($sqlReplies);
        $smtReplies->bind_param('ii', $feed_id, $c['comment_id']);
        $smtReplies->execute();
        $replies = $smtReplies->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($replies as &$r) {
            $r['data'] = json_decode($r['data'], true);
            $r['created_at'] = $this->formatDate($r['created_at']);

            // Like count for reply
            $likeStmt = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM likes WHERE feed_id=? AND parent_id=? AND type='reply'");
            $likeStmt->bind_param('ii', $feed_id, $r['reply_id']);
            $likeStmt->execute();
            $r['like_count'] = $likeStmt->get_result()->fetch_assoc()['cnt'];

            // Nested reply count (replies of replies)
            $replyCountStmt = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM comments WHERE feed_id=? AND parent_id=?");
            $replyCountStmt->bind_param('ii', $feed_id, $r['reply_id']);
            $replyCountStmt->execute();
            $r['reply_count'] = $replyCountStmt->get_result()->fetch_assoc()['cnt'];
        }

        // Total reply count for this comment
        $replyCountStmt = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM comments WHERE feed_id=? AND parent_id=?");
        $replyCountStmt->bind_param('ii', $feed_id, $c['comment_id']);
        $replyCountStmt->execute();
        $c['reply_count'] = $replyCountStmt->get_result()->fetch_assoc()['cnt'];

        $c['replies'] = $replies;
    }

    // Feed-level counts
    $sqlLikes = "SELECT COUNT(*) AS like_count FROM likes WHERE feed_id = ? AND parent_id=0";
    $smtLikes = $this->conn->prepare($sqlLikes);
    $smtLikes->bind_param('i', $feed_id);
    $smtLikes->execute();
    $feed['like_count'] = $smtLikes->get_result()->fetch_assoc()['like_count'];

    $sqlCommentCount = "SELECT COUNT(*) AS comment_count FROM comments WHERE feed_id = ? AND parent_id = 0";
    $smtCommentCount = $this->conn->prepare($sqlCommentCount);
    $smtCommentCount->bind_param('i', $feed_id);
    $smtCommentCount->execute();
    $feed['comment_count'] = $smtCommentCount->get_result()->fetch_assoc()['comment_count'];

    $feed['comments'] = $comments;

    return $feed;
}







public function sendComment($feed_id, $text, $parent_id = 0, $status = 'feeds') {
    if (empty($feed_id) || empty($text)) return false;

    $user = $this->getUserData()['data']; 
    $user_id = $user['id'];

    $stmt = $this->conn->prepare(
        "INSERT INTO comments (feed_id, user_id, parent_id, data, status, type) 
         VALUES (?, ?, ?, ?, ?, 'comment')"
    );

    $jsonData = json_encode(['text' => $text]);
    $stmt->bind_param('iiiss', $feed_id, $user_id, $parent_id, $jsonData, $status);
    $stmt->execute();

    // fire event trigger
    // $this->eventTriggered($feed_id, $status);
    return true;
}


public function eventTriggered($feed_id, $event_type) {
    // calculate fresh counts
    $likeStmt = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM likes WHERE feed_id=?");
    $likeStmt->bind_param('i', $feed_id);
    $likeStmt->execute();
    $likeCount = $likeStmt->get_result()->fetch_assoc()['cnt'];

    $commentStmt = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM comments WHERE feed_id=?");
    $commentStmt->bind_param('i', $feed_id);
    $commentStmt->execute();
    $commentCount = $commentStmt->get_result()->fetch_assoc()['cnt'];

    // build JSON payload
    $data = json_encode([
        'like_count'    => $likeCount,
        'comment_count' => $commentCount
    ]);

    // update existing event row
    $upd = $this->conn->prepare(
        "UPDATE events_trigger 
         SET data=?, updated_at=CURRENT_TIMESTAMP 
         WHERE feed_id=? AND event_type=?"
    );
    $upd->bind_param('sis', $data, $feed_id, $event_type);
    $upd->execute();

    if ($upd->affected_rows === 0) {
        // no row updated → insert new
        $ins = $this->conn->prepare(
            "INSERT INTO events_trigger (feed_id, event_type, data) VALUES (?, ?, ?)"
        );
        $ins->bind_param('iss', $feed_id, $event_type, $data);
        $ins->execute();
        $event_id = $this->conn->insert_id;
    } else {
        $event_id = null; // updated existing
    }

    return [
        'success'       => true,
        'feed_id'       => $feed_id,
        'event_type'    => $event_type,
        'like_count'    => $likeCount,
        'comment_count' => $commentCount,
        'event_id'      => $event_id
    ];
}


public function timeAgoSimple($datetime) {
    $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return $diff . "s"; // seconds
    } elseif ($diff < 3600) {
        return floor($diff / 60) . "m"; // minutes
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . "h"; // hours
    } elseif ($diff < 2592000) {
        return floor($diff / 86400) . "d"; // days
    } elseif ($diff < 31536000) {
        return floor($diff / 2592000) . "mo"; // months
    } else {
        return floor($diff / 31536000) . "y"; // years
    }
}

public function like_comment($feed_id, $status, $parent_id, $type) {
    $user_id = $this->getUserData()['data']['id'];

    // Check if user already liked
    $smt = $this->conn->prepare(
        "SELECT * FROM likes WHERE feed_id=? AND user_id=? AND parent_id=? AND status=? AND type=?"
    );
    $smt->bind_param('iiiss', $feed_id, $user_id, $parent_id, $status, $type);
    $smt->execute();
    $res = $smt->get_result();

    if ($res->num_rows) {
        $row = $res->fetch_assoc();
        $del = $this->conn->prepare("DELETE FROM likes WHERE id=?");
        $del->bind_param('i', $row['id']);
        $del->execute();
    } else {
        $ins = $this->conn->prepare(
            "INSERT INTO likes (feed_id, user_id, parent_id, status, type) VALUES (?, ?, ?, ?, ?)"
        );
        $ins->bind_param('iiiss', $feed_id, $user_id, $parent_id, $status, $type);
        $ins->execute();
    }

    // Count total likes for this feed/comment
    $countStmt = $this->conn->prepare(
        "SELECT COUNT(*) AS datacount 
         FROM likes 
         WHERE feed_id=? AND parent_id=? AND status=? AND type=?"
    );
    $countStmt->bind_param('iiss', $feed_id, $parent_id, $status, $type);
    $countStmt->execute();
    $countRes = $countStmt->get_result();
    $countRow = $countRes->fetch_assoc();
    $count = $countRow['datacount'];

    // Optionally trigger event
    // $this->eventTriggered($feed_id, $type);

    return $count;
}


}



$new = new comments($conn);

$hooks = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$hooks2 = $_POST['action'] ?? '';
$route = $hooks['action'] ?? $hooks2;

switch ($route) {

    case 'sendComment':
        print($new->sendComment($hooks['feed_id'], $hooks['text'], $hooks['parent_id'], $hooks['status']));
        break;

         case 'like_post':
          echo  $new->like_post($hooks['feed_id'], $hooks['status']);
         break;

         case 'like_comment':
            echo $new->like_comment($hooks['feed_id'], $hooks['status'], $hooks['parent_id'], $hooks['type']);
            break;

        case 'getReplies':
    $feed_id  = intval($hooks['feed_id']);
    $parent_id = intval($hooks['parent_id']);
    // fetch replies for this comment
    $stmt = $conn->prepare("
        SELECT c.id AS reply_id, c.feed_id, c.user_id, u.profile,
               c.data, c.type, c.parent_id, c.created_at
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.feed_id=? AND c.parent_id=?
        ORDER BY c.created_at ASC
    ");
    $stmt->bind_param('ii', $feed_id, $parent_id);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($rows as &$r) {
        $profile = json_decode($r['profile'], true);
        $r['username']   = $profile['username'];
        $r['first_name'] = $profile['first_name'];
        $r['last_name']  = $profile['last_name'];
        $r['profile_pic']= $profile['profile_pic'];
        $r['data']       = json_decode($r['data'], true);
        $r['text']       = $r['data']['text'];
        $r['created_at'] = $new->timeAgoSimple($r['created_at']);
        // like_count and reply_count can be added with extra queries if needed
    }

    echo json_encode(['replies' => $rows]);
    break;


    default:
        break;
}
?>
