<?php
// include "auth/conn.php";
include __DIR__."/conn/conn.php";
// Load and execute schema file
// $path = 'main/admin/query.sql';
$path = __DIR__.'/conn/db.sql';
if (!file_exists($path)) {
    die("SQL file not found at: $path");
}

$sql_content = file_get_contents($path);
if ($conn->multi_query($sql_content)) {
    echo "✅ Database reset successfully.<br>";
    // Clear remaining results from multi_query
    while ($conn->more_results() && $conn->next_result()) {
        $extra = $conn->store_result();
        if ($extra) $extra->free();
    }
} else {
    die("❌ Failed to run query.sql: " . $conn->error);
}

// if ($conn->query("SELECT * FROM member where email='shop@shoplenca.com' and ")) {
//    mysqli_query($conn,"insert into member(email,pass,roles,status) values('shop@shoplenca.com','$2y$10$Zr47PL3YAcrSeDSYfIRp7OSr9wqLa6NJFcQ2wsmZNB0b6AS8b0bE2','admin','active');");

// }

// Categories to seed
$categories = [
    ["label" => "Appliances", "img" => "appliances.webp"],
    ["label" => "Phones & Tablets", "img" => "phone&tablet.jpeg"],
    ["label" => "Health & Beauty", "img" => "health&beauty.webp"],
    ["label" => "Home & Office", "img" => "Home&Office.webp"],
    ["label" => "Electronics", "img" => "electronic-sales.jpeg"],
    ["label" => "Fashion", "img" => "fashion.jpeg"],
    ["label" => "Supermarket", "img" => "Supermarket.jpg"],
    ["label" => "Computing", "img" => "Computing.jpeg"],
    ["label" => "Baby Products", "img" => "Baby-Products.jpeg"],
    ["label" => "Gaming", "img" => "Gaming.jpeg"],
    ["label" => "Musical Instruments", "img" => "Musical-Instruments.jpeg"],
    ["label" => "Other categories", "img" => "Other-categories.jpg"]
];

$conn->close();
