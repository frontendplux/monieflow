<?php
require 'db.php'; // your connection

$data = json_decode(file_get_contents("php://input"), true);

$endpoint = $data['endpoint'];
$p256dh   = $data['keys']['p256dh'];
$auth     = $data['keys']['auth'];

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("INSERT INTO push_subscriptions 
(user_id, endpoint, p256dh, auth) VALUES (?, ?, ?, ?)");

$stmt->bind_param("isss", $user_id, $endpoint, $p256dh, $auth);
$stmt->execute();