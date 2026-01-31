<?php 
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); 
header("Access-Control-Allow-Headers: *");
include __DIR__.'/../config/conn.php';
session_start();
class Main {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }
public function login($data) {
    $identifier = trim($data['email'] ?? ''); // email or phone
    $pass       = $data['password'] ?? '';

    if (empty($identifier) || empty($pass)) {
        return ['success' => false, 'message' => "Enter all fields"];
    }
    // Check for user by email OR phone, only non-pending accounts
    $smt = $this->conn->prepare("
        SELECT * FROM users 
        WHERE (email=? OR phone=?) AND status != 'pending' 
        LIMIT 1
    ");
    $smt->bind_param('ss', $identifier, $identifier);
    $smt->execute();
    $res = $smt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();

        if (password_verify($pass, $row['password_hash'])) {
            // Generate secure session token
            $uids = bin2hex(random_bytes(32));

            $smt = $this->conn->prepare("UPDATE users SET uids=? WHERE id=? LIMIT 1");
            $smt->bind_param('si', $uids, $row['id']);
            $smt->execute();

            $_SESSION['uid'] = $uids;
            $_SESSION['id']  = $row['id'];

            return ['success' => true, 'message' => "Successfully Logged In!"];
        }

        // Password mismatch
        return ['success' => false, 'message' => "Invalid Email/Phone or Password"];
    }

    // User not found
    return ['success' => false, 'message' => "Invalid Email/Phone or Password"];
}
}

$data=json_decode(file_get_contents('php://input'),true);
$main=new Main($conn);
echo json_encode($main->login($data));