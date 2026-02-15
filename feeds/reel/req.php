<?php
include __DIR__."/../../main-function.php";

class reels extends Main {
    public function __construct($conn) { 
        parent::__construct($conn); 
    }

    public function getMusic($music= 'music', $limit =0){
    $smt = $this->conn->prepare("select * from feeds where status=? order by id desc limit ?, 25");
    $smt->bind_param('si', $music, $limit);
    $smt->execute();
    return $smt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}