<?php

require_once __DIR__ . "/../../config/cors.php";
require_once __DIR__ . "/../../includes/db.php";
$conn = db_connect();

session_start();

if (!isset($_SESSION['user_id'])) {

    http_response_code(401);

    echo json_encode([
        "message" => "Unauthenticated"
    ]);

    exit;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT
            users.id,
            users.name,
            users.email,
            roles.id AS role_id,
            roles.name AS role
        FROM users
        JOIN roles ON roles.id = users.role_id
        WHERE users.id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $userId
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {

    session_unset();
    session_destroy();

    http_response_code(401);

    echo json_encode([
        "message" => "Unauthenticated"
    ]);

    exit;
}

$user = mysqli_fetch_assoc($result);

echo json_encode($user);