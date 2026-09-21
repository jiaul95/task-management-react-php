<?php

require_once __DIR__ . "/../../config/cors.php";
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/auth.php";

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        "message" => "Method not allowed"
    ]);

    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$title = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');
$status = $data['status'] ?? 'todo';
$priority = $data['priority'] ?? 'medium';
$dueDate = $data['due_date'] ?? null;

if ($title === '') {
    http_response_code(400);

    echo json_encode([
        "message" => "Title is required"
    ]);

    exit;
}

if (!in_array(
    $status,
    ['todo', 'in-progress', 'done']
)) {
    http_response_code(400);

    echo json_encode([
        "message" => "Invalid status"
    ]);

    exit;
}

if (!in_array(
    $priority,
    ['low', 'medium', 'high']
)) {
    http_response_code(400);

    echo json_encode([
        "message" => "Invalid priority"
    ]);

    exit;
}

$userId = $_SESSION['user_id'];

$sql = "INSERT INTO tasks
        (
            title,
            description,
            status,
            priority,
            due_date,
            user_id
        )
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "sssssi",
    $title,
    $description,
    $status,
    $priority,
    $dueDate,
    $userId
);

if (!mysqli_stmt_execute($stmt)) {
    http_response_code(500);

    echo json_encode([
        "message" => "Failed to create task"
    ]);

    exit;
}

http_response_code(201);

echo json_encode([
    "message" => "Task created successfully",
    "task_id" => mysqli_insert_id($conn)
]);