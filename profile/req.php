<?php
include __DIR__."/../main-function.php";
class profile extends Main {
    public function __construct($conn) { 
        parent::__construct($conn); 
    }

public function profile($id = '')  {
    if(empty($id)) $id=$this->getUserData()['data']['id'];
    $user_id=$id;
    $sql = "select * from users where id=?";
    $smt = $this->conn->prepare($sql);
    $smt->bind_param('i', $user_id);
    $smt->execute();
    // return $smt->get_result()->fetch_all(MYSQLI_ASSOC);
    return $smt->get_result()->fetch_assoc();
}

public function selectCountsfollower_and_following($id) {
    $sql = "
        SELECT 
            (SELECT COUNT(*) FROM flows WHERE user_id = ?) AS following,
            (SELECT COUNT(*) FROM flows WHERE flow = ?) AS followers,
            (SELECT COUNT(*) 
             FROM flows f1 
             JOIN flows f2 
               ON f1.user_id = f2.flow 
              AND f1.flow = f2.user_id
             WHERE f1.user_id = ?) AS friends
    ";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param('iii', $id, $id, $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}





}