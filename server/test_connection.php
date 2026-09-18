<?php
    require_once __DIR__ . "/includes/db.php";

    $test_connection = db_connect();

    if ($test_connection) {
        echo "DB Connected successfully!";
    } else {
        echo "DB Connection failed!";
    }
?>