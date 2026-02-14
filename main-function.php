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
    }

    public function isLoggedIn() {
        if($this->getUserData()['success']) return true;
        return false;
    }
}
