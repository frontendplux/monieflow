<?php
// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__."/config/conn.php"; // include your DB connection

$user_id = $_SESSION['id'] ?? null;
$uids    = $_SESSION['uids'] ?? null;
$uki=md5(md5(time().rand(8,9999999999).$user_id.$uids));
if ($user_id && $uids) {
    // Clear the token in DB
    $stmt = $conn->prepare("UPDATE users SET uids=? WHERE id=? AND uids=? LIMIT 1");
    $stmt->bind_param("sis", $uki, $user_id, $uids);
    $stmt->execute();
}

// Clear session variables
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Redirect to login page
header("Location:/");
exit;
