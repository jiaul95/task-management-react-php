<?php

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . "/../../config/cors.php";

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);

    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$title = trim($data['title']) ?? '';
$description = trim($data['description']) ?? '';
$status = trim($data['status']) ?? 'todo';
$priority = trim($data['priority']) ?? 'medium';
$dueDate = trim($data['due_date']) ?? '';
$userId = (int) $data['user_id'] ?? 0;

if ($title === '') {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Title is required'
    ]);

    exit;
}

$allowedStatuses = ['todo', 'in-progress', 'done'];

if (!in_array($status, $allowedStatuses)) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Invalid status'
    ]);

    exit;
}

$allowedPriorities = ['low', 'medium', 'high'];

if (!in_array($priority, $allowedPriorities)) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Invalid priority'
    ]);

    exit;
}

if ($userId <= 0) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Valid user_id is required'
    ]);

    exit;
}

$sql = "INSERT INTO tasks
        (title, description, status, priority, due_date, user_id)
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
        'success' => false,
        'message' => 'Failed to create task'
    ]);

    exit;
}

$taskId = mysqli_insert_id($conn);

http_response_code(201);

echo json_encode([
    'success' => true,
    'message' => 'Task created successfully',
    'task_id' => $taskId
]);

mysqli_close($conn);