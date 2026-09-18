<?php

require_once __DIR__ . '/../../includes/db.php';

$conn = db_connect();

header('Content-Type: application/json');

$status = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "SELECT id, title, description, status, priority, due_date, user_id
        FROM tasks";

if ($status !== '') {
    $sql .= " WHERE status = ?";
}

$sql .= " ORDER BY id DESC";

if ($status !== '') {
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $status);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $sql);
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
    'data' => $tasks
]);

mysqli_close($conn);