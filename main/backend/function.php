<?php 
   include __DIR__."/../../main-function.php";
   class codium extends Main{
    public function __construct($conn) { 
            parent::__construct($conn); 
        }
    //   =================friends package=====================================
    public function friends($limit = 0) {
    $user_id = $this->getUserData()['data']['id'] ?? 1;
    // Get other users
    $stmt = $this->conn->prepare(
        "SELECT * FROM users WHERE id != ? ORDER BY id DESC LIMIT ?, 25"
    );
    $stmt->bind_param('ii', $user_id, $limit);
    $stmt->execute();
    $user_res = $stmt->get_result();

    $friends = [];
    while ($row = $user_res->fetch_assoc()) {
        // Check if current user is following this user
        $stmt_following = $this->conn->prepare(
            "SELECT id FROM flows WHERE user_id = ? AND flow = ?"
        );
        $stmt_following->bind_param('ii', $user_id, $row['id']);
        $stmt_following->execute();
        $is_following = $stmt_following->get_result()->num_rows > 0;

        // Check if this user is following current user
        $stmt_follower = $this->conn->prepare(
            "SELECT id FROM flows WHERE user_id = ? AND flow = ?"
        );
        $stmt_follower->bind_param('ii', $row['id'], $user_id);
        $stmt_follower->execute();
        $is_follower = $stmt_follower->get_result()->num_rows > 0;

        // Determine relationship status
        if ($is_following && $is_follower) {
            $row['flow_status'] = 'followback'; // mutual
        } elseif ($is_following) {
            $row['flow_status'] = 'following';
        } elseif ($is_follower) {
            $row['flow_status'] = 'follower';
        } else {
            $row['flow_status'] = 'none';
        }

        $friends[] = $row;
    }

    return $friends;
}



    //   =================feeds programs=====================================

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




    //   public function friends(){
    //     return [];
    //   }
   }