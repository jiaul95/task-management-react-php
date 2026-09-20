<?php

require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);

    exit;
}

$id = (int) $_GET['id'] ?? 0;

if ($id <= 0) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Valid task id is required'
    ]);

    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$title = trim($data['title']) ?? '';
$description = trim($data['description']) ?? '';
$status = trim($data['status']) ?? '';
$priority = trim($data['priority']) ?? '';
$dueDate = trim($data['due_date']) ?? '';

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

$checkSql = "SELECT id FROM tasks WHERE id = ? AND deleted_at IS NULL";

$checkStmt = mysqli_prepare($conn, $checkSql);

mysqli_stmt_bind_param($checkStmt, "i", $id);

mysqli_stmt_execute($checkStmt);

$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) === 0) {
    http_response_code(404);

    echo json_encode([
        'success' => false,
        'message' => 'Task not found'
    ]);

    exit;
}

$sql = "UPDATE tasks
        SET title = ?,
            description = ?,
            status = ?,
            priority = ?,
            due_date = ?
        WHERE id = ? AND deleted_at IS NULL";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "sssssi",
    $title,
    $description,
    $status,
    $priority,
    $dueDate,
    $id
);

if (!mysqli_stmt_execute($stmt)) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Failed to update task'
    ]);

    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Task updated successfully'
]);

mysqli_close($conn);