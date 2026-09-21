<?php

require_once __DIR__ . "/../../config/cors.php";
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/auth.php";

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);

    echo json_encode([
        "message" => "Method not allowed"
    ]);

    exit;
}

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);

    echo json_encode([
        "message" => "Invalid task ID"
    ]);

    exit;
}

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role === 'admin') {

    $checkSql = "SELECT id
                 FROM tasks
                 WHERE id = ?
                 AND deleted_at IS NULL";

    $checkStmt = mysqli_prepare(
        $conn,
        $checkSql
    );

    mysqli_stmt_bind_param(
        $checkStmt,
        "i",
        $id
    );

} else {

    $checkSql = "SELECT id
                 FROM tasks
                 WHERE id = ?
                 AND user_id = ?
                 AND deleted_at IS NULL";

    $checkStmt = mysqli_prepare(
        $conn,
        $checkSql
    );

    mysqli_stmt_bind_param(
        $checkStmt,
        "ii",
        $id,
        $userId
    );
}

mysqli_stmt_execute($checkStmt);

$checkResult = mysqli_stmt_get_result(
    $checkStmt
);

if (mysqli_num_rows($checkResult) === 0) {
    http_response_code(403);

    echo json_encode([
        "message" => "You are not allowed to delete this task"
    ]);

    exit;
}

$sql = "UPDATE tasks
        SET deleted_at = NOW()
        WHERE id = ?";

$stmt = mysqli_prepare(
    $conn,
    $sql
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

if (!mysqli_stmt_execute($stmt)) {
    http_response_code(500);

    echo json_encode([
        "message" => "Failed to delete task"
    ]);

    exit;
}

http_response_code(200);

echo json_encode([
    "message" => "Task deleted successfully"
]);