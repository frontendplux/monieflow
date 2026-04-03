<?php
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'db_uts');
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS) or die('Unable to connect to database!');
    $conn->query("create database if not exists " . DB_NAME);
    $conn->select_db(DB_NAME) or die('Database not found!');
    $conn->set_charset("utf8mb4");
?>