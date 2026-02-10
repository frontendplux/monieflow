<?php
session_start();
header('Content-Type: application/json');
require __DIR__ . "/conn/conn.php";
// Try to decode JSON body
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// If not JSON, fall back to $_POST
if (!is_array($data)) {
    $data = $_POST;
}

$action = $data['action'] ?? '';

switch ($action) {
    case 'login':

    $email = trim($data['email'] ?? '');
    $pass  = $data['pass'] ?? ($data['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => false, "message" => "Provide a valid email address"]);
        break;
    }

    $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(["status" => false, "message" => "Account not found"]);
        break;
    }

    $user = $res->fetch_assoc();

    if (!password_verify($pass, $user['password'])) {
        echo json_encode(["status" => false, "message" => "Invalid password"]);
        break;
    }

    session_regenerate_id(true);

    $uid = uniqid(explode('@', $email)[0] . '-', true);

    $up = $conn->prepare("UPDATE users SET uid = ? WHERE id = ?");
    $up->bind_param("si", $uid, $user['id']);
    $up->execute();

    $_SESSION['id']  = $user['id'];
    $_SESSION['uid'] = $uid;

    echo json_encode(["status" => true, "message" => "Login successful"]);
    break;

    case 'signup':

    $fname    = trim($data['first_name'] ?? '');
    $lname    = trim($data['last_name'] ?? '');
    $username = trim($data['username'] ?? '');
    $email    = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => false, "message" => "Invalid email"]);
        break;
    }

    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        echo json_encode(["status" => false, "message" => "Invalid username"]);
        break;
    }

    if (strlen($password) < 8) {
        echo json_encode(["status" => false, "message" => "Weak password"]);
        break;
    }

    // Check email OR username
    $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR JSON_EXTRACT(profile,'$.username') = ? LIMIT 1");
    $check->bind_param("ss", $email, $username);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(["status" => false, "message" => "Email or username already exists"]);
        break;
    }

    // Secure image upload
    $profilePic = null;
    if (!empty($_FILES['image']['name'])) {

        if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            echo json_encode(["status" => false, "message" => "Image too large"]);
            break;
        }

        $mime = mime_content_type($_FILES['image']['tmp_name']);
        if (!in_array($mime, ['image/jpeg','image/png','image/webp'])) {
            echo json_encode(["status" => false, "message" => "Invalid image type"]);
            break;
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('pp_', true) . "." . $ext;

        $dir = __DIR__ . "/uploads/";
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        move_uploaded_file($_FILES['image']['tmp_name'], $dir . $fileName);
        $profilePic = "/uploads/" . $fileName;
    }

    session_regenerate_id(true);

    $uid = uniqid($username . '-', true);
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $profile = json_encode([
        "first_name" => $fname,
        "last_name"  => $lname,
        "username"   => $username,
        "profile_pic"=> $fileName
    ]);

    $stmt = $conn->prepare("INSERT INTO users (uid, email, password, profile) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $uid, $email, $hashedPassword, $profile);

    if ($stmt->execute()) {
        $_SESSION['id']  = $stmt->insert_id;
        $_SESSION['uid'] = $uid;
        echo json_encode(["status" => true, "message" => "Signup successful"]);
    } else {
        echo json_encode(["status" => false, "message" => "Signup failed"]);
    }
    break;



   case 'forgot_password':
    $email = trim($data['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "status" => false,
            "message" => "Invalid email address"
        ]);
        break;
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, profile FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        // SECURITY: don't reveal if email exists
        echo json_encode([
            "status" => false,
            "message" => "Email Address not found / do not exists with us"
        ]);
        break;
    }

    $user = $res->fetch_assoc();

    // Generate reset token and expiry
    $token   = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", time() + 3600); // 1 hour

    // Decode existing profile JSON
    $profile = json_decode($user['profile'], true);
    if (!is_array($profile)) {
        $profile = [];
    }

    // Add reset fields
    $profile['reset_token']   = $token;
    $profile['reset_expires'] = $expires;

    $profileJson = json_encode($profile);

    // Update profile JSON in DB
    $up = $conn->prepare("UPDATE users SET profile = ? WHERE id = ?");
    $up->bind_param("si", $profileJson, $user['id']);
    $up->execute();

    // TODO: send email with reset link
    // Example: https://yourdomain.com/reset-password.php?token=$token

    echo json_encode([
        "status" => true,
        "message" => "Reset instructions sent"
    ]);
    break;




    default:
        echo json_encode(["status" => false, "message" => "Unknown action"]);
        break;
}
