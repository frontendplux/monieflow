<?php
include __DIR__."/../main-function.php";

class feeds extends Main {
    public function __construct($conn) { 
        parent::__construct($conn); 
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
            LIMIT ?, 12";

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


}

$new = new feeds($conn);

$hooks = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$hooks2 = $_POST['action'] ?? '';
$route = $hooks['action'] ?? $hooks2;

switch ($route) {
   case 'get_friend_list_to_follow':
      echo json_encode($new->flows($_POST['limit']));
      break;
    case 'feeds':
        break;
         case 'like_post':
          echo  $new->like_post($hooks['feed_id'], $hooks['status']);
         break;


    default:
        break;
}

?>

