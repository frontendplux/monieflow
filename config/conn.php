<?php
    define('db_host', 'localhost');
    define('db_user', 'root');
    define('db_pass', '');
    define('db_name', 'monieflow');
$host = $_SERVER['HTTP_HOST'];
// Local settings
if (in_array($host, ['localhost:3000', '127.0.0.1:3000', '192.168.8.129:3000', '172.20.10.10:3000','172.20.10.4:3000','192.168.8.110:3000'])) {
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