<?php

include __DIR__ . "/config/conn.php";

// Path to SQL file
$path = __DIR__ . '/config/query.sql';
if (!file_exists($path)) {
    die("❌ SQL file not found at: $path");
}

// Read SQL file content
$sql_content = file_get_contents($path);

// Execute multiple queries safely
if ($conn->multi_query($sql_content)) {
    echo "✅ Database reset successfully.<br>";

    // Clear remaining results from multi_query
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());

} else {
    die("❌ Failed to run query.sql: " . $conn->error);
}

// Close connection
$conn->close();