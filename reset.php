<?php   
        include __DIR__."/conn.php";
        $db_queries = file_get_contents(__DIR__ . "/db.sql");
        if ($conn->multi_query($db_queries)) {
            do {
                if ($result = $conn->store_result()) {
                    $result->free();
                }
            } while ($conn->more_results() && $conn->next_result());
            echo "Database reset successfully.";

        } else {
           echo "Error resetting database: " . $conn->error;
        }

?>