<?php
include __dir__."/config/conn.php";
$path = __DIR__.'/config/db.sql';
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
$conn->close();
