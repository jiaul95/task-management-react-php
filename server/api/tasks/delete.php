<?php

require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
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
        SET deleted_at = NOW()
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

if (!mysqli_stmt_execute($stmt)) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete task'
    ]);

    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Task deleted successfully'
]);

mysqli_close($conn);