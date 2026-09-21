<?php

require_once __DIR__ . "/../../config/cors.php";
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/auth.php";

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit;
}

$status = trim($_GET['status'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$limit = max(1, intval($_GET['limit'] ?? 5));
$offset = ($page - 1) * $limit;

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

$sql = "SELECT
            tasks.id,
            tasks.title,
            tasks.description,
            tasks.status,
            tasks.priority,
            tasks.due_date,
            tasks.user_id,
            users.name AS owner_name
        FROM tasks
        JOIN users ON users.id = tasks.user_id
        WHERE tasks.deleted_at IS NULL";

$params = [];
$types = "";

if ($role !== 'admin') {
    $sql .= " AND tasks.user_id = ?";
    $params[] = $userId;
    $types .= "i";
}

if ($status !== '') {

    if (!in_array($status, ['todo', 'in-progress', 'done'])) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid status"]);
        exit;
    }

    $sql .= " AND tasks.status = ?";
    $params[] = $status;
    $types .= "s";
}

$sql .= " ORDER BY tasks.id DESC LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    $types,
    ...$params
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$tasks = [];

while ($row = mysqli_fetch_assoc($result)) {
    $tasks[] = $row;
}

http_response_code(200);

echo json_encode([
    "data" => $tasks,
    "page" => $page,
    "limit" => $limit
]);