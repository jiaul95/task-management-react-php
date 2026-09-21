<?php

require_once __DIR__ . "/../../config/cors.php";
require_once __DIR__ . "/../../includes/db.php";

$conn = db_connect();

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        "message" => "Method not allowed"
    ]);

    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if ($email === '' || $password === '') {
    http_response_code(400);

    echo json_encode([
        "message" => "Email and password are required"
    ]);

    exit;
}

$sql = "SELECT
            users.id,
            users.name,
            users.email,
            users.password,
            roles.id AS role_id,
            roles.name AS role
        FROM users
        JOIN roles ON roles.id = users.role_id
        WHERE users.email = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $email
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    http_response_code(401);

    echo json_encode([
        "message" => "Invalid email or password"
    ]);

    exit;
}

$user = mysqli_fetch_assoc($result);

if (!password_verify($password, $user['password'])) {
    http_response_code(401);

    echo json_encode([
        "message" => "Invalid email or password"
    ]);

    exit;
}

session_regenerate_id(true);

$_SESSION['user_id'] = $user['id'];
$_SESSION['role_id'] = $user['role_id'];
$_SESSION['role'] = $user['role'];

unset($user['password']);

http_response_code(200);

echo json_encode([
    "message" => "Login successful",
    "user" => $user
]);