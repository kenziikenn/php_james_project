<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['judge_id'])) {
    echo json_encode(['error' => 'Not authorized']);
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

$query = "SELECT c.description, c.weight, s.score 
          FROM criterion c
          LEFT JOIN scores s ON c.criterion_id = s.criterion_id 
          AND s.judge_id = ? 
          WHERE c.event_id = ?
          ORDER BY c.weight DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'ii', $_SESSION['judge_id'], $event_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$scores = [];
while ($row = mysqli_fetch_assoc($result)) {
    $scores[] = $row;
}

echo json_encode($scores);
mysqli_close($conn);