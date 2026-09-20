<?php
    require_once __DIR__ . "/includes/db.php";

    $test_connection = db_connect();

    if ($test_connection) {
        echo json_encode(["success" => true, "message" => "DB Connected successfully!"]);
    } else {
        echo json_encode(["success" => false, "error" => db_connect()]);
    }
?>