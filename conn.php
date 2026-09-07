<?php

$db_server_name = $_SERVER['HTTP_HOST'];
$local_hosts = [
    'localhost:3001',
    '127.0.0.1',
    '172.20.10.10:3001'
];
if (in_array($db_server_name, $local_hosts, true)) {

    // Local XAMPP
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'monieflow');
} else {
    // InfinityFree
    define('DB_HOST', 'sql213.infinityfree.com');
    define('DB_USER', 'if0_42199151');
    define('DB_PASSWORD', 'YOUR_PASSWORD');
    define('DB_NAME', 'if0_42199151_monieflow');
}
// Connect to MySQL
$conn = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASSWORD
);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Select database
if (!$conn->select_db(DB_NAME)) {
    die("Database selection failed: " . $conn->error);
}

?>