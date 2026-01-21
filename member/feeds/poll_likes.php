<?php
header('Content-Type: application/json');
include __DIR__."/../../config/function.php";

$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;
$max_wait = 25; // Seconds to hold the connection
$start_time = time();

while ((time() - $start_time) < $max_wait) {
    // Check for a like newer than what the user last saw
    $stmt = $conn->prepare("SELECT id, feed_id, comment_id FROM likes WHERE id > ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("i", $last_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $new_like = $result->fetch_assoc();

    if ($new_like) {
        echo json_encode(['status' => 'new', 'data' => $new_like]);
        exit;
    }

    // No new like yet? Sleep for 2 seconds and try again
    sleep(2);
}

// If we reach here, 25 seconds passed with no news
echo json_encode(['status' => 'timeout']);