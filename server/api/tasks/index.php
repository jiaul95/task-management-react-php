<?php

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . "/../../config/cors.php";


header('Content-Type: application/json');

$conn = db_connect();

$status = $_GET['status'] ?? null;

$page = $_GET['page'] ?? 1;
$limit = $_GET['limit'] ?? 10;

if ($page < 1) {
    $page = 1;
}

if ($limit < 1 || $limit > 100) {
    $limit = 10;
}

$offset = ($page - 1) * $limit;



if (!empty($status)) {

    $sql = "SELECT id, title, description, status, priority, due_date, user_id
            FROM tasks
            WHERE status = ? AND deleted_at IS NULL
            ORDER BY id DESC LIMIT ? OFFSET ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "sii", $status, $limit, $offset);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

} else {

    $sql = "SELECT id, title, description, status, priority, due_date, user_id
            FROM tasks WHERE deleted_at IS NULL
            ORDER BY id DESC
            LIMIT ? OFFSET ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
}

if (!$result) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch tasks'
    ]);

    exit;
}

$tasks = [];

while ($row = mysqli_fetch_assoc($result)) {
    $tasks[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $tasks,
    'page' => $page,
    'limit' => $limit
]);

mysqli_close($conn);