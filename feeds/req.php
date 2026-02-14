<?php
include __DIR__."/../main-function.php";

class feeds extends Main {
    public function __construct($conn) { 
        parent::__construct($conn); 
    }

public function feeds($offset = 0, $type = 'feeds') {
    $offset = (int)$offset;

    $sql = "
        SELECT 
            f.id AS feed_id,
            f.data,
            f.keywords,
            f.txt,
            f.created_at,
            f.status,
            u.id AS user_id,
            u.profile,
            COUNT(DISTINCT l.id) AS like_count,
            COUNT(DISTINCT c.id) AS comment_count
        FROM feeds f
        JOIN users u ON f.user_id = u.id
        LEFT JOIN likes l ON l.feed_id = f.id
        LEFT JOIN comments c ON c.feed_id = f.id
        WHERE f.status = ?
        GROUP BY f.id
        ORDER BY f.id DESC
        LIMIT ?, 25
    ";

    $smt = $this->conn->prepare($sql);
    $smt->bind_param('si', $type, $offset);
    $smt->execute();
    return $smt->get_result()->fetch_all(MYSQLI_ASSOC);
}

 
public function flows($limit = 0) {
    $sql = "SELECT u.id, u.uid, u.email, u.profile,
                   CASE
                     WHEN EXISTS (
                       SELECT 1 FROM flows f2
                       WHERE f2.user_id = f1.flow AND f2.flow = f1.user_id
                     ) THEN 'mutual'
                     ELSE 'flow'
                   END AS status,
                   (SELECT COUNT(*) 
                    FROM flows fa
                    JOIN flows fb 
                      ON fa.user_id = fb.flow AND fa.flow = fb.user_id
                    WHERE fa.user_id = f1.user_id) AS mutual_count
            FROM users u
            JOIN flows f1 ON u.id = f1.flow
            WHERE f1.user_id = ?

            UNION

            SELECT u.id, u.uid, u.email, u.profile,
                   'flow-up' AS status,
                   (SELECT COUNT(*) 
                    FROM flows fa
                    JOIN flows fb 
                      ON fa.user_id = fb.flow AND fa.flow = fb.user_id
                    WHERE fa.user_id = ?) AS mutual_count
            FROM users u
            JOIN flows f1 ON u.id = f1.user_id
            WHERE f1.flow = ? 
              AND NOT EXISTS (
                SELECT 1 FROM flows f2
                WHERE f2.user_id = ? AND f2.flow = u.id
              )

            UNION

            SELECT u.id, u.uid, u.email, u.profile,
                   'none' AS status,
                   0 AS mutual_count
            FROM users u
            WHERE u.id != ? 
              AND NOT EXISTS (
                SELECT 1 FROM flows f WHERE f.user_id = ? AND f.flow = u.id
              )
              AND NOT EXISTS (
                SELECT 1 FROM flows f WHERE f.user_id = u.id AND f.flow = ?)

            ORDER BY id DESC
            LIMIT ?, 20";

    $smt = $this->conn->prepare($sql);

    $userId = $this->getUserData()['data']['id'];
    // 8 placeholders → bind 8 variables
    $smt->bind_param(
        'iiiiiiii',
        $userId,  // for flow
        $userId,  // for flow-up count
        $userId,  // for flow-up WHERE f1.flow = ?
        $userId,  // for flow-up NOT EXISTS
        $userId,  // for none WHERE u.id != ?
        $userId,  // for none NOT EXISTS user->flow
        $userId,  // for none NOT EXISTS flow->user
        $limit    // offset for LIMIT
    );

    $smt->execute();
    return $smt->get_result()->fetch_all(MYSQLI_ASSOC);
}



public function like_post($feed_id, $type) {
    $user_id = $this->getUserData()['data']['id'];

    // Check if user already liked this feed
    $smt = $this->conn->prepare("SELECT * FROM likes WHERE feed_id=? AND user_id=? AND parent_id=0 AND status=?");
    $smt->bind_param('iis', $feed_id, $user_id, $type);
    $smt->execute();
    $res = $smt->get_result();

    if ($res->num_rows) {
        // User already liked → remove like
        $row = $res->fetch_assoc();
        $del = $this->conn->prepare("DELETE FROM likes WHERE id=?");
        $del->bind_param('i', $row['id']);
        $del->execute();
    } else {
        // User hasn’t liked yet → insert like
        $ins = $this->conn->prepare("INSERT INTO likes (feed_id, user_id, parent_id, status) VALUES (?, ?, 0, ?)");
        $ins->bind_param('iis', $feed_id, $user_id, $type);
        $ins->execute();
    }

    // Count total likes for this feed
    $countStmt = $this->conn->prepare("SELECT COUNT(*) AS datacount FROM likes WHERE feed_id=? AND status=?");
    $countStmt->bind_param('is', $feed_id, $type);
    $countStmt->execute();
    $countRes = $countStmt->get_result();
    $countRow = $countRes->fetch_assoc();
    $count = $countRow['datacount'];

    // Log event in events_trigger table
    $eventData = json_encode([
        'feed_id' => $feed_id,
        'status'  => $type,
        'count'   => $count
    ]);

    // Trigger any app-specific event handling
    $this->eventTriggered($feed_id, $type);
    return $count;
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

    // if no row was updated, insert new
    if ($upd->affected_rows === 0) {
        $ins = $this->conn->prepare(
            "INSERT INTO events_trigger (feed_id, event_type, data) VALUES (?, ?, ?)"
        );
        $ins->bind_param('iss', $feed_id, $event_type, $data);
        $ins->execute();
        return $this->conn->insert_id;
    }

    return true;
}
    // Create a new feed
public function createFeed() {
    $text = $_POST['content'] ?? '';
    $user_id = $this->getUserData()['data']['id'];

    if (empty($text)) {
        return $this->respond(false, 'Content cannot be empty');
    }

    // Encode text and any images into JSON for feeds.data
    $feedData = ['text' => $text];

    // Ensure uploads directory exists
    $targetDir = __DIR__ . "/../uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $uploaded = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
            $name = basename($_FILES['images']['name'][$i]);
            $target = time() . "_" . $name;
            if (move_uploaded_file($tmpName,  $targetDir . "/" .$target)) {
                $uploaded[] = $target;
            }
        }
    }

    // Add image paths into JSON data
    if (!empty($uploaded)) {
        $feedData['images'] = $uploaded;
    }

    $content = json_encode($feedData, JSON_UNESCAPED_UNICODE);

    // Insert feed into feeds table
    $stmt = $this->conn->prepare("INSERT INTO feeds (user_id, txt, data, status) VALUES (?, ?, ?, 'feed')");
    $stmt->bind_param('iss', $user_id, $text, $content);
    $stmt->execute();
    $feed_id = $stmt->insert_id;

    return $this->respond(true, 'Feed created successfully', [
        'feed_id' => $feed_id,
        'images'  => $uploaded
    ]);
}



    private function respond($success, $message, $extra = []) {
        header('Content-Type: application/json');
        echo json_encode(array_merge([
            'success' => $success,
            'message' => $message
        ], $extra));
        exit;
    }
}

$new = new feeds($conn);

$hooks = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$hooks2 = $_POST['action'] ?? '';
$route = $hooks['action'] ?? $hooks2;

switch ($route) {
   case 'create_feeds':
      $new->createFeed();
      break;
    case 'feeds':
        echo json_encode([
            "feed"   => $new->feeds($hooks['limit'], 'feed'),
            "reels"   => $new->feeds($hooks['limit'], 'reel'),
            "market" => $new->feeds($hooks['limit'], 'market'),
            "groups" => $new->feeds($hooks['limit'], 'groups'),
            "friends"  => $new->flows($hooks['limit']),
        ]);
        break;
        
         case 'like_post':
          echo  $new->like_post($hooks['feed_id'], $hooks['status']);
         break;


    default:
        break;
}

/*
  -- trying to get mutual flows
  -- trying to count if they are in flows so we can have mutual flows count
  -- if followed and is not following flow up, if not at all flow 
$new->flows()



Alright, let’s put everything together into one full code example that handles all cases:

flow → current user follows someone, but they don’t follow back

flow‑up → someone follows current user, but current user doesn’t follow back

mutual → both follow each other

none → no relationship at all (suggested users to follow)

*/

// print_r($new->feeds())
?>

