<?php
    define('db_host', 'localhost');
    define('db_user', 'root');
    define('db_pass', '');
    define('db_name', 'monieflow3');
$host = $_SERVER['HTTP_HOST'];
// Local settings
if (in_array($host, ['localhost:3000','localhost:5500', 'http://127.0.0.1:5500', '127.0.0.1:3000', '192.168.8.129:3000', '172.20.10.10:3000','172.20.10.4:3000','192.168.8.110:3000'])) {
    $db_host = db_host;
    $db_user = db_user;
    $db_pass = db_pass;
    $db_name = db_name;

    // Connect without DB to create it if missing
    $conn = new mysqli($db_host, $db_user, $db_pass);
    if ($conn->connect_error) {
        die('Unable to connect: ' . $conn->connect_error);
    }
    $conn->query("CREATE DATABASE IF NOT EXISTS `$db_name`");
} 
// Production settings
else {
    $db_host = 'localhost';
    $db_user = 'sheggevv_blog';
    $db_pass = 'samuel252.';
    $db_name = 'sheggevv_monieflow';

    $conn = new mysqli($db_host, $db_user, $db_pass);
    if ($conn->connect_error) {
        die('Unable to connect: ' . $conn->connect_error);
    }
}

// Always select DB after creation
$conn->select_db($db_name);

// function timeAgo($dateTime)
// {
//     // Convert string to timestamp
//     $timestamp = strtotime($dateTime);
//     $seconds = time() - $timestamp;

//     if ($seconds < 60) {
//         return $seconds . 's ago';
//     }

//     $minutes = floor($seconds / 60);
//     if ($minutes < 60) {
//         return $minutes . ' min' . ($minutes > 1 ? 's' : '') . ' ago';
//     }

//     $hours = floor($minutes / 60);
//     if ($hours < 24) {
//         return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
//     }

//     $days = floor($hours / 24);
//     if ($days < 30) {
//         return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
//     }

//     $months = floor($days / 30);
//     if ($months < 12) {
//         return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
//     }

//     $years = floor($months / 12);
//     return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
// }