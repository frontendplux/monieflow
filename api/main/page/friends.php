  
  <?php
  class Friends{
    public function __construct($conn) {
        $this->conn = $conn;
    }
     
public function getFriends($data){
    $user_id = explode('-', $data['user'])[1] ?? '';
    $limit=$limit['limit'] ?? 4;
    $offset=$offset['offset'] ?? 0;
    $sql = "
        SELECT 
            U.id,
            U.username,
            CASE 
                WHEN f1.follower_id IS NOT NULL THEN 1 
                ELSE 0 
            END AS isFollowing,
            CASE 
                WHEN f2.user_id IS NOT NULL THEN 1 
                ELSE 0 
            END AS isFollowedBack
        FROM users U
        LEFT JOIN followers f1 
            ON f1.user_id = U.id AND f1.follower_id = ?
        LEFT JOIN followers f2 
            ON f2.follower_id = U.id AND f2.user_id = ?
        WHERE U.id != ? limit $offset, $limit
    ";

    $stmt = $this->conn->prepare($sql);

    // bind parameters (3 integers)
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);

    $stmt->execute();

    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

}

?>