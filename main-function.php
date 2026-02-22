<?php 
include __DIR__."/conn/conn.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Main {
    public $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getUserData() {
        $id  = $_SESSION['id'] ?? 0;
        $uid = $_SESSION['uid'] ?? '';

        $smt = $this->conn->prepare("SELECT * FROM users WHERE id=? AND uid=? LIMIT 1");
        $smt->bind_param('is', $id, $uid);
        $smt->execute();
        $res = $smt->get_result();

        if ($res->num_rows > 0) {
            return [
                "success" => true,
                "data"    => $res->fetch_assoc()
            ];
        }
        return ["success" => false];
        // return ["success" => false];
    }

    public function isLoggedIn() {
        return $this->getUserData()['success'];
    }

    // Fetch feeds from multiple sources
    // public function getFeeds($limit = 20) {
    //     $sql = "
    //         SELECT f.*,
    //                CASE 
    //                    WHEN f.source_type = 'user' THEN u.username
    //                    WHEN f.source_type = 'page' THEN p.title
    //                    WHEN f.source_type = 'group' THEN g.name
    //                    WHEN f.source_type = 'event' THEN e.title
    //                    WHEN f.source_type = 'market' THEN m.title
    //                END AS source_name,
    //                CASE 
    //                    WHEN f.source_type = 'user' THEN u.profile_pic
    //                    ELSE NULL
    //                END AS source_pic
    //         FROM feeders f
    //         LEFT JOIN users u ON f.source_type='user' AND f.source_id=u.id
    //         LEFT JOIN pages p ON f.source_type='page' AND f.source_id=p.id
    //         LEFT JOIN groups g ON f.source_type='group' AND f.source_id=g.id
    //         LEFT JOIN events e ON f.source_type='event' AND f.source_id=e.id
    //         LEFT JOIN market_listings m ON f.source_type='market' AND f.source_id=m.id
    //         WHERE f.is_active = 1
    //         ORDER BY f.created_at DESC
    //         LIMIT ?
    //     ";
    //     $smt = $this->conn->prepare($sql);
    //     $smt->bind_param('i', $limit);
    //     $smt->execute();
    //     $res = $smt->get_result();

    //     $feeds = [];
    //     while ($row = $res->fetch_assoc()) {
    //         $feeds[] = $row;
    //     }
    //     return $feeds;
    // }



public function getFeedsBySource($sourceType, $limit = 10) { 

$sql="SELECT f.*,
       CASE 
           WHEN f.source_type = 'user' THEN JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.username'))
           WHEN f.source_type = 'page' THEN p.title
           WHEN f.source_type = 'group' THEN g.name
           WHEN f.source_type = 'event' THEN e.title
           WHEN f.source_type = 'market' THEN m.title
       END AS source_name,
       CASE 
           WHEN f.source_type = 'user' THEN JSON_UNQUOTE(JSON_EXTRACT(u.profile, '$.profile_pic'))
           ELSE NULL
       END AS source_pic,
       -- Accurate counts
       (SELECT COUNT(*) FROM feed_likes fl WHERE fl.feed_id = f.id) AS likes_count,
       (SELECT COUNT(*) FROM feed_comments fc WHERE fc.feed_id = f.id) AS comments_count
FROM feeders f
LEFT JOIN users u ON f.source_type='user' AND f.source_id=u.id
LEFT JOIN pages p ON f.source_type='page' AND f.source_id=p.id
LEFT JOIN groups g ON f.source_type='group' AND f.source_id=g.id
LEFT JOIN events e ON f.source_type='event' AND f.source_id=e.id
LEFT JOIN market_listings m ON f.source_type='market' AND f.source_id=m.id
WHERE f.is_active = 1 AND f.source_type = ?
ORDER BY f.created_at DESC
LIMIT ?
";

    $smt = $this->conn->prepare($sql); 
    $smt->bind_param('si', $sourceType, $limit); 
    $smt->execute(); 
    $res = $smt->get_result(); 
    $feeds = []; 
    while ($row = $res->fetch_assoc()){ 
        $feeds[] = $row;
    } 
    return $feeds; 
}

}
