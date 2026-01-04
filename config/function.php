<?php 
    include __DIR__."/conn.php";
    include __dir__."/mailserver.php";
    if (session_status() === PHP_SESSION_NONE) { session_start(); }

 function currencyIdToSymbols($conn, $id) {
    // Prepare query to fetch currency by its ID
    $smt = $conn->prepare("SELECT icons,name FROM currency WHERE id=? LIMIT 1");
    $smt->bind_param("i", $id);
    $smt->execute();
    $check = $smt->get_result();

    if ($check->num_rows === 0) {
        return null; // No currency found
    }
    $row=$check->fetch_assoc();
    $icon =strtoupper($row['icons']);
    $name = $row['name'];

    switch ($icon) {
        case 'NGN':
            return ['₦',$name]; // Nigerian Naira
        case 'USD':
            return ['$',$name]; // US Dollar
        case 'GHS':
            return ['GH₵',$name]; // Ghanaian Cedi
        case 'MONIEFLOW':
            return ['MF',$name];    
        default:
            return [$icon, $name]; // fallback: return whatever was stored
    }
}

   class main{
        public function __construct($conn) {
            $this->conn = $conn;
        }


        public function login($data) {
        // Validate inputs
            $user = mysqli_real_escape_string($this->conn, trim($data[0] ?? ""));
            $pass = mysqli_real_escape_string($this->conn, trim($data[1] ?? ""));

            if (empty($user) || empty($pass)) {
                return [false,"provide a valid username, email / password"];
                
            }

            // Correct SQL with parentheses + correct bind params
            $sql = "SELECT id, password_hash FROM users 
                    WHERE (user=? OR email=?) AND roles='member' LIMIT 1";

            $smt = $this->conn->prepare($sql);
            $smt->bind_param("ss", $user, $user);
            $smt->execute();

            $result = $smt->get_result();

            if ($result->num_rows == 0) {
                return [false,"invalid username, email / password"];
            }

            $row = $result->fetch_assoc();

            // Verify password
            if (!password_verify($pass, $row["password_hash"])) {
                return[false,"invalid username, email / password"];
            }

            // Generate new session token
            $uids = md5($user . time() . rand(0, 99999));

            // Update token in DB
            $update = $this->conn->prepare("UPDATE users SET uids=? WHERE id=? LIMIT 1");
            $update->bind_param("si", $uids, $row["id"]);
            $update->execute();

            // Save session data
            $_SESSION['uids'] = $uids;
            $_SESSION['id']  = $row['id'];
            return [true,"successfully loggedin"];
        }


public function signup($data) { 
    $username = mysqli_real_escape_string($this->conn, trim($data[0] ?? "")); 
    $email    = mysqli_real_escape_string($this->conn, trim($data[1] ?? "")); 
    $password = trim($data[2] ?? ""); 

    if (empty($username) || empty($email) || empty($password)) {
        return [false, "All fields are required"]; 
    } 

    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        return [false, "Provide a valid email address"]; 
    }

    // Check if user/email already exists 
    $check = $this->conn->prepare("SELECT id, status FROM users WHERE user=? OR email=? LIMIT 1"); 
    $check->bind_param("ss", $username, $email); 
    $check->execute(); 
    $result = $check->get_result(); 

    $uids = rand(100000,999999);

    if ($result->num_rows > 0) { 
        $row = $result->fetch_assoc();

        if ($row['status'] === 'pending') {
            // Always hash password before saving
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $update = $this->conn->prepare(
                "UPDATE users SET uids=?, user=?, email=?, password_hash=? WHERE id=? LIMIT 1"
            ); 
            $update->bind_param("isssi", $uids, $username, $email, $hash, $row['id']); 
            $update->execute(); 

            mailserver($email, $uids);
            $_SESSION['user_save'] = $email;
            return [true, "Account updated, please verify your email"]; 
        }

        return [false, "Username or email already exists"];
    } 

    // Hash password for new account
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user 
    $insert = $this->conn->prepare(
        "INSERT INTO users (uids,user,email,password_hash,roles,status) VALUES (?,?,?,?, 'member','pending')"
    );
    $insert->bind_param("isss", $uids, $username, $email, $hash);

    if ($insert->execute()) { 
        mailserver($email, $uids);
        $_SESSION['user_save'] = $email;
        return [true, "Account created successfully, please verify your email"]; 
    } else {
        return [false, "Error creating account"]; 
    } 
}


public function confirmCode($data) { 
        $email=$_SESSION['user_save'] ?? "";
        $code = mysqli_real_escape_string($this->conn, trim($data[0] ?? "")); 
        if (empty($code) || empty($email)) { return [false, "Confirmation code is required"];} 
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE uids=? and email=? LIMIT 1");
        $stmt->bind_param("is", $code, $email); 
        $stmt->execute(); 
        $result = $stmt->get_result(); 
        if ($result->num_rows === 0) { 
            return [false, "Invalid confirmation code"]; 
        } 
        $uids=md5(md5($email. time(). rand(1000, 999999999999)));
        $row = $result->fetch_assoc();
        $update = $this->conn->prepare("UPDATE users SET uids=?, status='active' WHERE id=? LIMIT 1"); 
        $update->bind_param("si", $uids, $row['id']);
        $update->execute(); 
        $_SESSION['id'] = $row['id']; 
        $_SESSION['uids'] = $uids; 
        unset($_SESSION['user_save']);
        return [true, "Code confirmed, account activated"]; 
    }


    public function isLoggedIn(){
        if($this->getUserData()[0]) return true;
        return false;
    }


    public function getUserData(){
        $user_id=$_SESSION['id'] ?? null;
        $uids=$_SESSION['uids'] ?? null;
        if($user_id === null || $uids === null) return [false, "unable to validate account"];
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id=? and uids=?  LIMIT 1");
        $stmt->bind_param("is", $user_id, $uids); 
        $stmt->execute(); 
        $check=$stmt->get_result();
        if($check->num_rows === 0){
            return [false, "unable to validate account"];
        }
        return[true, 'successfully fetched', 'data' => $check->fetch_assoc()];
    }

    // =====================================products
public function wallet_balance() {
    // Validate user session
    $userData = $this->getUserData();
    if (!$userData[0]) {
        return [];
    }

    $user_id = $userData['data']['id'];

    // Query all wallets for this user
    $stmt = $this->conn->prepare("SELECT id, wallet_balance, cvc, currency_id, created_at, updated_at 
                                  FROM wallets 
                                  WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return [];
    }

    $wallets = [];
    while ($row = $result->fetch_assoc()) {
        $wallets[] = $row;
    }
    return $wallets;
}

public function transferMoney($data) {
    $recipient_id = $data[1] ?? "";
    $wallet_id    = $data[0] ?? "";
    $amount       = floatval($data[2] ?? 0);
    $note         = $data['message'] ?? null;

    if(!$this->isLoggedIn()) {
        return ["success" => false, "message" => "Unable to authenticate transfer"];
    }

    // Get sender wallet
    $stmt = $this->conn->prepare("SELECT * FROM wallets WHERE id=? AND user_id=? LIMIT 1");
    $stmt->bind_param("ii", $wallet_id, $this->getUserData()['data']['id']);
    $stmt->execute();
    $check = $stmt->get_result();

    if($check->num_rows === 0) {
        return ["success" => false, "message" => "Wallet not found"];
    }

    $row = $check->fetch_assoc();

    if($row['wallet_balance'] < $amount) {
        return ["success" => false, "message" => "Insufficient funds"];
    }

    // Get receiver wallet
    $receiver = $this->conn->prepare("SELECT * FROM wallets WHERE id=? AND currency_id=? LIMIT 1");
    $receiver->bind_param("ii", $recipient_id, $row['currency_id']);
    $receiver->execute();
    $receiver_res = $receiver->get_result();

    if($receiver_res->num_rows === 0) {
        return ["success" => false, "message" => "No account associated with this wallet"];
    }

    $receiver_res_row = $receiver_res->fetch_assoc();

    // Start transaction
    $this->conn->begin_transaction();

    try {
        // Deduct from sender
        $newBalance = $row['wallet_balance'] - $amount;
        $stmt = $this->conn->prepare("UPDATE wallets SET wallet_balance=? WHERE id=? LIMIT 1");
        $stmt->bind_param("di", $newBalance, $row['id']);
        $stmt->execute();

        // Credit recipient
        $stmt = $this->conn->prepare("UPDATE wallets SET wallet_balance = wallet_balance + ? WHERE id=? AND currency_id=? LIMIT 1");
        $stmt->bind_param("dii", $amount, $recipient_id, $receiver_res_row['currency_id']);
        $stmt->execute();

        // Get recipient user_id
        $stmt = $this->conn->prepare("SELECT user_id FROM wallets WHERE id=? LIMIT 1");
        $stmt->bind_param("i", $recipient_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $recipientUserId = $res->num_rows > 0 ? $res->fetch_assoc()['user_id'] : null;

        // Log transaction
        $currencyId = $row['currency_id'];
        $stmt = $this->conn->prepare("
            INSERT INTO transactions 
            (sender_wallet_id, recipient_wallet_id, sender_user_id, recipient_user_id, amount, currency_id, transaction_type, status, message) 
            VALUES (?, ?, ?, ?, ?, ?, 'transfer', 'completed', ?)
        ");
        $stmt->bind_param(
            "iiiidds",
            $wallet_id,
            $recipient_id,
            $this->getUserData()['data']['id'],
            $recipientUserId,
            $amount,
            $currencyId,
            $note
        );
        $stmt->execute();
        $transactionId = $this->conn->insert_id;

        // Create notification for recipient
        if ($recipientUserId) {
            $title   = "Funds Received";
            $message = "You received {$amount} from user ".$this->getUserData()['data']['id'];
            $stmt = $this->conn->prepare("
                INSERT INTO notifications (user_id, transaction_id, transaction_type, title, message, status) 
                VALUES (?, ?, 'transfer', ?, ?, 'unread')
            ");
            $stmt->bind_param("iiss", $recipientUserId, $transactionId, $title, $message);
            $stmt->execute();
        }

        // Commit transaction
        $this->conn->commit();

        return [
            "success" => true,
            "message" => "Transfer successful",
            "amount"  => $amount,
            "transaction_id" => $transactionId
        ];
    } catch (Exception $e) {
        $this->conn->rollback();
        return ["success" => false, "message" => "Transfer failed: ".$e->getMessage()];
    }
}



public function fetchtransactionProducts($data=0){

    $userId = $this->getUserData()['data']['id']; 
    $stmt = $this->conn->prepare("SELECT 
                                        t.*, 
                                        c.icons, 
                                        c.name AS currency_name 
                                        FROM transactions t JOIN currency c ON t.currency_id = c.id 
                                        WHERE t.sender_user_id=? OR t.recipient_user_id=? 
                                        ORDER BY t.created_at DESC LIMIT $data,10 "); 
    $stmt->bind_param("ii", $userId, $userId); 
    $stmt->execute(); 
    $result = $stmt->get_result(); 
    $saved=[];
    while ($tx = $result->fetch_assoc()){
        $saved[]=$tx;
    } 
    return $saved;
}

public function getUnreadNotifications() {
    if(!$this->isLoggedIn()) {
        return [false, "Unable to authenticate user", []];
    }

    $userId = $this->getUserData()['data']['id'];

    $stmt = $this->conn->prepare("
        SELECT n.id, n.title, n.message, n.transaction_id, n.transaction_type, 
               n.status, n.created_at, n.updated_at
        FROM notifications n
        WHERE n.user_id=? AND n.status='unread'
        ORDER BY n.created_at DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $notifications = [];
    while($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }

    if(empty($notifications)) {
        return [true, "No unread notifications", []];
    }

    return [true, "Unread notifications fetched successfully", $notifications];
}


public function getAllNotifications() {
    if(!$this->isLoggedIn()) {
        return [false, "Unable to authenticate user", []];
    }

    $userId = $this->getUserData()['data']['id'];

    $stmt = $this->conn->prepare("
        SELECT n.id, n.title, n.message, n.transaction_id, n.transaction_type, 
               n.status, n.created_at, n.updated_at
        FROM notifications n
        WHERE n.user_id=?
        ORDER BY n.created_at DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $notifications = [];
    while($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }

    if(empty($notifications)) {
        return [true, "No notifications found", []];
    }

    return [true, "All notifications fetched successfully", $notifications];
}



public function createAccount($data){
    $userData = $this->getUserData();
    if (!$this->isLoggedIn()) return;
    $smt=$this->conn->prepare("INSERT into wallets (user_id,currency_id) values(?,?)");
    $smt->bind_param('ii',$userData['data']['id'],$data);
    $smt->execute();
    }



    // ===============================================
public function redeemCard($code) {
    if(!$this->isLoggedIn()) {
        return ["success"=>false,"message"=>"Not authenticated"];
    }

    if(empty($code)) {
        return ["success"=>false,"message"=>"No code provided"];
    }

    // Check giftcard_transactions table for a valid purchase
    $stmt = $this->conn->prepare("
        SELECT * FROM giftcard_transactions 
        WHERE card_code=? AND transaction_type='purchase' AND status='pending' 
        LIMIT 1
    ");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows === 0) {
        return ["success"=>false,"message"=>"Invalid or already redeemed code"];
    }

    $card = $res->fetch_assoc();

    // Mark as redeemed
    $stmt = $this->conn->prepare("
        UPDATE giftcard_transactions 
        SET transaction_type='redemption', status='completed', updated_at=NOW() 
        WHERE id=?
    ");
    $stmt->bind_param("i", $card['id']);
    $stmt->execute();

    // Ensure wallet exists
    $stmt = $this->conn->prepare("
        SELECT * FROM wallets 
        WHERE user_id=? AND currency_id=? 
        LIMIT 1
    ");
    $stmt->bind_param("ii", $this->getUserData()['data']['id'], $card['currency_id']);
    $stmt->execute();
    $resulty = $stmt->get_result();

    if($resulty->num_rows < 1){
        $stmt = $this->conn->prepare("
            INSERT INTO wallets (user_id, currency_id, wallet_balance) 
            VALUES (?, ?, 0)
        ");
        $stmt->bind_param("ii", $this->getUserData()['data']['id'], $card['currency_id']);
        $stmt->execute();
    }

    // Credit user wallet
    $stmt = $this->conn->prepare("
        UPDATE wallets 
        SET wallet_balance = wallet_balance + ? 
        WHERE user_id=? AND currency_id=? 
        LIMIT 1
    ");
    $stmt->bind_param("dii", $card['amount'], $this->getUserData()['data']['id'], $card['currency_id']);
    $stmt->execute();

    // Insert notification
    $stmt = $this->conn->prepare("
        INSERT INTO notifications (user_id, transaction_id, transaction_type, title, message, status) 
        VALUES (?, ?, 'redeem', ?, ?, 'unread')
    ");
    $title   = "Gift Card Redeemed";
    $message = "You redeemed {$card['amount']} successfully.";
    $stmt->bind_param("iiss", $this->getUserData()['data']['id'], $card['id'], $title, $message);
    $stmt->execute();

    return [
        "success"=>true,
        "message"=>"Redeemed ".$card['amount']." successfully",
        "amount"=>$card['amount']
    ];
}


    // Fetch feeds with comments
    public function feeds() {
        $sql = "
            SELECT f.id AS feed_id, f.title, f.content, f.created_at,
                   c.id AS comment_id, c.text AS comment_text
            FROM feeds f
            LEFT JOIN comment c ON f.id = c.feed_id
            ORDER BY f.id, c.id
        ";
        $smt = $this->conn->prepare($sql);
        $smt->execute();
        $res = $smt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);

        // Group comments under feeds
        $feeds = [];
        foreach ($rows as $row) {
            $fid = $row['feed_id'];
            if (!isset($feeds[$fid])) {
                $feeds[$fid] = [
                    'id' => $fid,
                    'title' => $row['title'],
                    'content' => $row['content'],
                    'created_at' => $row['created_at'],
                    'comments' => []
                ];
            }
            if (!empty($row['comment_id'])) {
                $feeds[$fid]['comments'][] = [
                    'id' => $row['comment_id'],
                    'text' => $row['comment_text']
                ];
            }
        }
        return $feeds;
    }


}