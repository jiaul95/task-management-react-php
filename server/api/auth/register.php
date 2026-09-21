<?php

require_once __DIR__ . "/../../config/cors.php";
require_once __DIR__ . "/../../includes/db.php";

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        "message" => "Method not allowed"
    ]);

    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if ($name === '' || $email === '' || $password === '') {
    http_response_code(400);

    echo json_encode([
        "message" => "Name, email and password are required"
    ]);

    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);

    echo json_encode([
        "message" => "Invalid email address"
    ]);

    exit;
}

if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode([
        "message" => "Password must be at least 8 characters"
    ]);
    exit;
}

if (!preg_match('/[A-Z]/', $password)) {
    http_response_code(400);
    echo json_encode([
        "message" => "Password must contain at least one uppercase letter"
    ]);
    exit;
}

if (!preg_match('/[a-z]/', $password)) {
    http_response_code(400);
    echo json_encode([
        "message" => "Password must contain at least one lowercase letter"
    ]);
    exit;
}

if (!preg_match('/[0-9]/', $password)) {
    http_response_code(400);
    echo json_encode([
        "message" => "Password must contain at least one number"
    ]);
    exit;
}

if (!preg_match('/[^A-Za-z0-9]/', $password)) {
    http_response_code(400);
    echo json_encode([
        "message" => "Password must contain at least one special character"
    ]);
    exit;
}

$checkSql = "SELECT id
             FROM users
             WHERE email = ?";

$checkStmt = mysqli_prepare($conn, $checkSql);

mysqli_stmt_bind_param(
    $checkStmt,
    "s",
    $email
);

mysqli_stmt_execute($checkStmt);

$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) > 0) {
    http_response_code(409);

    echo json_encode([
        "message" => "Email already registered"
    ]);

    exit;
}

$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);

$roleSql = "SELECT id
            FROM roles
            WHERE name = 'user'";

$roleResult = mysqli_query($conn, $roleSql);

if (!$roleResult || mysqli_num_rows($roleResult) === 0) {
    http_response_code(500);

    echo json_encode([
        "message" => "User role not found"
    ]);

    exit;
}

$role = mysqli_fetch_assoc($roleResult);

$roleId = $role['id'];

$sql = "INSERT INTO users
        (name, email, password, role_id)
        VALUES (?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "sssi",
    $name,
    $email,
    $hashedPassword,
    $roleId
);

if (!mysqli_stmt_execute($stmt)) {
    http_response_code(500);

    echo json_encode([
        "message" => "Failed to register user"
    ]);

    exit;
}

http_response_code(201);

echo json_encode([
    "message" => "User registered successfully",
    "user_id" => mysqli_insert_id($conn)
]);