<?php
// Prevent script from timing out
set_time_limit(0); 
ignore_user_abort(true);

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // Essential for Nginx servers

include __DIR__."/../../config/function.php";

while (true) {
    // Check for connection loss
    if (connection_aborted()) break;

    // Only send data if there is an actual change
    // You should ideally track the 'last_like_id' in a session or JS variable
    $res = $conn->query("SELECT l.feed_id, u.username FROM likes l JOIN users u ON l.user_id = u.id ORDER BY l.id DESC LIMIT 1");
    $data = $res->fetch_assoc();

    if ($data) {
        echo "data: " . json_encode($data) . "\n\n";
    }

    // Small heartbeat to keep connection alive
    echo ": heartbeat\n\n";

    ob_flush();
    flush();
    sleep(3); // Increase to 3-5 seconds to reduce server load
}