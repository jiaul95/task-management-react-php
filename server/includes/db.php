<?php

require_once __DIR__ . "/../config/dbconfig.php";

function db_connect(){
    $connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $connection;
}



?>
