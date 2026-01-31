<?php 
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); 
header("Access-Control-Allow-Headers: *");
include __DIR__.'/../config/conn.php';
session_start();
function timeAgo($dateTime)
{
    // Convert string to timestamp
    $timestamp = strtotime($dateTime);
    $seconds = time() - $timestamp;

    if ($seconds < 60) {
        return $seconds . 's ago';
    }

    $minutes = floor($seconds / 60);
    if ($minutes < 60) {
        return $minutes . ' min' . ($minutes > 1 ? 's' : '') . ' ago';
    }

    $hours = floor($minutes / 60);
    if ($hours < 24) {
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    }

    $days = floor($hours / 24);
    if ($days < 30) {
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    }

    $months = floor($days / 30);
    if ($months < 12) {
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    }

    $years = floor($months / 12);
    return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
}

class Main {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function userData() {
        $_user_id = $_SESSION['id'] ?? null; 
        $_uids    = $_SESSION['uid'] ?? null;

        if(empty($_user_id) || empty($_uids)) {
            return ['success' => false, 'message' => "unable to validate"];
        }

        $smt = $this->conn->prepare("SELECT * FROM users WHERE id=? AND uids=? LIMIT 1");
        $smt->bind_param('is', $_user_id, $_uids);
        $smt->execute();
        $result = $smt->get_result();

        if($result->num_rows > 0) {
            return ['success' => true, 'message' => 'successfully fetched', 'data' => $result->fetch_assoc()];
        }

        return ['success' => false, 'message' => "unable to validate"];
    }

    public function isLoggedin() {
        return $this->userData()['success'];
    }
public function login($data) {
    $identifier = trim($data[0] ?? ''); // email or phone
    $pass       = $data[1] ?? '';

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

public function register($data){
    $identifier = trim($data['email'] ?? '');
    $pass = $data['password'] ?? '';
    unset($data['password']);
    unset($data['action']);
    $profile = json_encode($data);

    if ($identifier === '' || $pass === '') {
        return ['success' => false, 'message' => 'Enter all fields'];
    }

    if (strlen($pass) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters'];
    }

    // Detect email or phone
    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $email = $identifier;
        $phone = null;
        $checkSql = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $checkParam = $email;
    } else {
        $phone = preg_replace('/\D/', '', $identifier);
        if (strlen($phone) < 8 || strlen($phone) > 15) {
            return ['success' => false, 'message' => 'Enter a valid phone number'];
        }
        $email = null;
        $checkSql = "SELECT id FROM users WHERE phone = ? LIMIT 1";
        $checkParam = $phone;
    }

    // Duplicate check
    $stmt = $this->conn->prepare($checkSql);
    $stmt->bind_param('s', $checkParam);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return ['success' => false, 'message' => 'Email or phone already registered'];
    }

    $passwordHash = password_hash($pass, PASSWORD_DEFAULT);
    $uid = bin2hex(random_bytes(16));

    $stmt = $this->conn->prepare(
        "INSERT INTO users (uids, email, phone, password_hash, profile, status)
         VALUES (?, ?, ?, ?, ?, 'active')"
    );
    $stmt->bind_param('sssss', $uid, $email, $phone, $passwordHash, $profile);

    if ($stmt->execute()) {
        $_SESSION['uid'] = $uid;
        $_SESSION['id']  = $stmt->insert_id;
        return ['success' => true, 'message' => 'Account created successfully'];
    }

    return ['success' => false, 'message' => 'Error creating account'];
}

 public function forgetPassword($data){
    $identifier = trim($data ?? '');

    if ($identifier === '') {
        return ['success' => false, 'message' => "Email address or phone number is required"];
    }

    // Detect email or phone
    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $email = $identifier;
        $phone = null;
        $sql = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $param = $email;
    } else {
        $phone = preg_replace('/\D/', '', $identifier);
        if (strlen($phone) < 8 || strlen($phone) > 15) {
            return ['success' => false, 'message' => "Enter a valid phone number"];
        }
        $email = null;
        $sql = "SELECT id FROM users WHERE phone = ? LIMIT 1";
        $param = $phone;
    }

    // Check if user exists
    $smt = $this->conn->prepare($sql);
    $smt->bind_param('s', $param);
    $smt->execute();
    $res = $smt->get_result();

    if ($res->num_rows === 0) {
        return ['success' => false, 'message' => "Not found with this email address or phone number"];
    }

    $row = $res->fetch_assoc();

    // Generate 6-digit reset code
    $resetCode = random_int(100000, 999999);

    // Expiry time (15 minutes)
    $expiresAt = time() + (15 * 60);

    // Save reset code & expiry inside profile JSON
    $smt = $this->conn->prepare(
        "UPDATE users 
         SET profile = JSON_SET(
             IFNULL(profile, '{}'),
             '$.reset_code', ?,
             '$.reset_expires', ?
         )
         WHERE id = ?"
    );
    $smt->bind_param('sii', $resetCode, $expiresAt, $row['id']);
    $smt->execute();

    // Store identifier in session (your approach)
    $_SESSION['email'] = $identifier;

    // TODO: Send reset code via email or SMS
    // mail($email, "Password Reset Code", "Your reset code is: $resetCode");
    return ['success' => true, 'message' => "Reset code sent to your email"];
}




public function verifyCode($data){
    $code = trim($data ?? '');
    $identifier = trim($_SESSION['email'] ?? '');

    if ($identifier === '' || $code === '') {
        return ['success' => false, 'message' => "Email/phone and code are required"];
    }

    if (!preg_match('/^\d{6}$/', $code)) {
        return ['success' => false, 'message' => "Invalid code format"];
    }

    // Detect email or phone (same logic as forgetPassword)
    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $sql = "SELECT id, uids, profile FROM users WHERE email = ? LIMIT 1";
        $param = $identifier;
    } else {
        $phone = preg_replace('/\D/', '', $identifier);
        $sql = "SELECT id, uid, profile FROM users WHERE phone = ? LIMIT 1";
        $param = $phone;
    }

    $smt = $this->conn->prepare($sql);
    $smt->bind_param('s', $param);
    $smt->execute();
    $res = $smt->get_result();

    if ($res->num_rows === 0) {
        return ['success' => false, 'message' => "No account found"];
    }

    $row = $res->fetch_assoc();
    $profile = json_decode($row['profile'], true) ?? [];

    // Check reset code exists
    if (!isset($profile['reset_code'], $profile['reset_expires'])) {
        return ['success' => false, 'message' => "No reset code found. Request a new one."];
    }

    // Check expiry
    if (time() > (int)$profile['reset_expires']) {
        return ['success' => false, 'message' => "Reset code has expired"];
    }

    // Compare safely
    if ((string)$profile['reset_code'] !== (string)$code) {
        return ['success' => false, 'message' => "Invalid reset code"];
    }

    // Clear reset data
    unset($profile['reset_code'], $profile['reset_expires']);
    $newProfile = json_encode($profile);

    $smt = $this->conn->prepare("UPDATE users SET profile = ? WHERE id = ?");
    $smt->bind_param('si', $newProfile, $row['id']);
    $smt->execute();

    // Allow password reset
    $_SESSION['reset_user_id'] = $row['id'];
    $_SESSION['uid'] = $row['uid'];
    unset($_SESSION['email']);

    return ['success' => true, 'message' => "Code verified. You may now reset your password."];
}



// public function getComments($feed_id, $reply_id = null) {
//     global $conn;
//     // Check if the current user liked the comment
//     $viewer_id = $this->userData()['data']['id'] ?? 0;

//     $sql = "SELECT c.*, u.username, u.profile,
//             (SELECT COUNT(*) FROM likes WHERE comment_id = c.id) as total_likes,
//             EXISTS(SELECT 1 FROM likes WHERE comment_id = c.id AND user_id = ?) as is_liked
//             FROM comments c 
//             JOIN users u ON c.user_id = u.id 
//             WHERE c.feed_id = ? AND c.status = 'active' ";
    
//     if ($reply_id === null) {
//         $sql .= "AND c.reply_id IS NULL ";
//     } else {
//         $sql .= "AND c.reply_id = ? ";
//     }
//     $sql .= "ORDER BY c.created_at ASC";

//     $stmt = $conn->prepare($sql);
//     if ($reply_id === null) {
//         $stmt->bind_param("ii", $viewer_id, $feed_id);
//     } else {
//         $stmt->bind_param("iii", $viewer_id, $feed_id, $reply_id);
//     }
    
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $html = "";

//     while ($row = $result->fetch_assoc()) {
//         $profile = json_decode($row['profile'], true);
//         $img = $profile['img'] ?? '/img/default-user.png';
//         $likedClass = $row['is_liked'] ? 'text-primary fw-bold' : 'text-muted';

//         $html .= '
//         <div class="comment-item mt-3" id="comment-'.$row['id'].'">
//             <div class="d-flex gap-2">
//                 <img src="'.$img.'" class="rounded-circle" style="width:35px; height:35px; object-fit:cover;">
//                 <div class="flex-grow-1">
//                     <div class="bg-light p-2 rounded-3">
//                         <h6 class="mb-0 small fw-bold">'.$row['username'].'</h6>
//                         <p class="mb-0 small">'.htmlspecialchars($row['content']).'</p>
//                     </div>
//                     <div class="d-flex gap-3 mt-1 ms-2 small" style="font-size:11px;">
//                         <a href="javascript:void(0)" class="text-decoration-none '.$likedClass.'" onclick="likeComment('.$row['id'].', this)">Like (<span>'.$row['total_likes'].'</span>)</a>
//                         <a href="javascript:void(0)" class="text-decoration-none text-muted" onclick="setReply('.$row['id'].', \''.$row['username'].'\')">Reply</a>
//                         <span class="text-muted">'.timeAgo($row['created_at']).'</span>
//                     </div>
                    
//                     <div class="reply-container border-start ms-2 ps-3">
//                         '.$this->getComments($feed_id, $row['id']).'
//                     </div>
//                 </div>
//             </div>
//         </div>';
//     }
//     return $html;
// }



public function getComments($feed_id, $reply_id = null) {
    global $conn;
    $viewer_id = $this->userData()['data']['id'] ?? 0;

    // Query: Get comments, count likes, and check if current user liked it
    $sql = "SELECT c.*, u.username, u.profile,
            (SELECT COUNT(*) FROM likes WHERE comment_id = c.id) as total_likes,
            EXISTS(SELECT 1 FROM likes WHERE comment_id = c.id AND user_id = ?) as is_liked
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.feed_id = ? AND c.status = 'active' ";
    
    $sql .= ($reply_id === null) ? "AND c.reply_id IS NULL " : "AND c.reply_id = ? ";
    $sql .= "ORDER BY c.created_at ASC";

    $stmt = $conn->prepare($sql);
    if ($reply_id === null) $stmt->bind_param("ii", $viewer_id, $feed_id);
    else $stmt->bind_param("iii", $viewer_id, $feed_id, $reply_id);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $html = "";

    while ($row = $result->fetch_assoc()) {
        $likedClass = $row['is_liked'] ? 'text-primary fw-bold' : 'text-muted';
        // $html .= '
        // <div class="comment-item mt-3">
        //     <div class="d-flex gap-2">
        //         <img src="'.$row['profile'].'" class="rounded-circle" width="35" height="35" style="object-fit:cover;">
        //         <div class="flex-grow-1">
        //             <div class="bg-white p-2 rounded-3 shadow-sm border">
        //                 <h6 class="mb-0 small fw-bold">'.$row['username'].'</h6>
        //                 <p class="mb-0 small text-secondary">'.htmlspecialchars($row['content']).'</p>
        //             </div>
        //             <div class="d-flex gap-3 mt-1 ms-2" style="font-size: 11px;">
        //                 <a href="javascript:void(0)" class="text-decoration-none '.$likedClass.'" 
        //                    onclick="likeComment('.$row['id'].', this)">
        //                    <i class="ri-thumb-up-fill"></i> <span class="l-cnt">'.$row['total_likes'].'</span> Likes
        //                 </a>
        //                 <a href="javascript:void(0)" class="text-decoration-none text-muted fw-bold" 
        //                    onclick="prepareReply('.$row['id'].', \''.$row['username'].'\')">Reply</a>
        //                 <span class="text-muted">'.timeAgo($row['created_at']).'</span>
        //             </div>
                    
        //             <div class="ms-2 ps-3 border-start mt-2">
        //                 '.$this->getComments($feed_id, $row['id']).'
        //             </div>
        //         </div>
        //     </div>
        // </div>';
        // Inside your while loop in getComments()
$isOwner = ($row['user_id'] == $viewer_id);
$deleteBtn = $isOwner ? '<a href="javascript:void(0)" class="text-danger ms-2 text-decoration-none" onclick="deleteComment('.$row['id'].', this)"><i class="ri-delete-bin-line"></i> Delete</a>' : '';

$html .= '
<div class="comment-item mt-3" id="comment-block-'.$row['id'].'">
    <div class="d-flex gap-2">
        <img src="'.$row['profile'].'" class="rounded-circle" width="35" height="35" style="object-fit:cover;">
        <div class="flex-grow-1">
            <div class="bg-white p-2 rounded-3 position-relative">
                <h6 class="mb-0 small fw-bold">'.$row['username'].'</h6>
                <p class="mb-0 small text-secondary">'.htmlspecialchars($row['content']).'</p>
            </div>
            <div class="d-flex gap-3 mt-1 ms-2" style="font-size: 11px;">
                <a href="javascript:void(0)" class="text-decoration-none '.$likedClass.'" onclick="likeComment('.$row['id'].', this)">
                   <i class="ri-thumb-up-fill"></i> <span class="l-cnt">'.$row['total_likes'].'</span>
                </a>
                <a href="javascript:void(0)" class="text-decoration-none text-muted fw-bold" onclick="prepareReply('.$row['id'].', \''.$row['username'].'\')">Reply</a>
                '.$deleteBtn.'
                <span class="text-muted">'.timeAgo($row['updated_at']).'</span>
            </div>
            <div class="ms-2 ps-3 border-start mt-2">
                '.$this->getComments($feed_id, $row['id']).'
            </div>
        </div>
    </div>
</div>';
    }
    return $html;
}





public function createProduct($data = []) {

    // ===== BASIC INPUTS =====
    $name          = trim($_POST['product_name'] ?? '');
    $description   = trim($_POST['description'] ?? '');
    $category      = trim($_POST['category'] ?? '');
    $location      = trim($_POST['location'] ?? '');
    $color         = trim($_POST['color'] ?? '');
    $size          = trim($_POST['size'] ?? '');
    $quantity      = intval($_POST['quantity'] ?? 0);
    $price         = floatval($_POST['selling_price'] ?? 0);
    $market_price  = floatval($_POST['market_price'] ?? 0);

    if (!$this->isLoggedin()) {
        return ['success' => false, 'message' => 'You must be logged in'];
    }

    if (empty($name) || empty($price) || empty($location)) {
        return ['success' => false, 'message' => 'Required fields missing'];
    }

    // ===== HANDLE IMAGES (SAME STYLE AS createFeed) =====
    $uploadedFiles = [];
    if (!empty($_FILES['images']['name'][0])) {

        $uploadDir = __DIR__ . "/../uploads/products/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['images']['name'] as $key => $nameFile) {
            $tmpName = $_FILES['images']['tmp_name'][$key];
            $error   = $_FILES['images']['error'][$key];

            if ($error === UPLOAD_ERR_OK) {
                $ext = pathinfo($nameFile, PATHINFO_EXTENSION);
                $newName = uniqid("prd_", true) . "." . $ext;
                $targetPath = $uploadDir . $newName;

                if (move_uploaded_file($tmpName, $targetPath)) {
                    $uploadedFiles[] = $newName;
                }
            }
        }
    }

    // ===== JSON FIELDS =====
    $mediaJson = json_encode($uploadedFiles);
    $variantsJson = json_encode([
        'size'  => $size,
        'color' => $color
    ]);

    // ===== USER =====
    $userId = $this->userData()['data']['id'] ?? null;
    if (!$userId) {
        return ['success' => false, 'message' => 'You must be logged in'];
    }

    // ===== INSERT PRODUCT =====
    $stmt = $this->conn->prepare("
        INSERT INTO products
        (user_id, name, description, location, price, market_price, stock, variants, media)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssddiss",
        $userId,
        $name,
        $description,
        $location,
        $price,
        $market_price,
        $quantity,
        $variantsJson,
        $mediaJson
    );

    if ($stmt->execute()) {

        // OPTIONAL: CREATE FEED ENTRY FOR PRODUCT
        $productId = $stmt->insert_id;

        $feedStmt = $this->conn->prepare("
            INSERT INTO feeds (user_id, content, media, type)
            VALUES (?, ?, ?, 'product')
        ");

        $feedContent = $name . " - ₦" . number_format($price, 2);
        $feedStmt->bind_param("iss", $userId, $feedContent, $mediaJson);
        $feedStmt->execute();

        return [
            'success' => true,
            'message' => 'Product created successfully',
            'product_id' => $productId
        ];

    } else {
        return [
            'success' => false,
            'message' => 'Database error: ' . $stmt->error
        ];
    }
}



public function fetchProducts() {
    $stmt = $this->conn->prepare("
        SELECT 
            p.id,
            p.name,
            p.description,
            p.price,
            p.off_price,
            p.media,
            p.varient,
            p.user_id,
            c.name AS category_name,
            c.keyword AS category_keyword,
            c.img AS category_img
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'active'
        ORDER BY p.id DESC
        LIMIT 50
    ");

    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}




}
