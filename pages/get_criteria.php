<?php
session_start();
require_once('../db_connection.php');

header('Content-Type: application/json');

if (!isset($_SESSION['judge_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

$query = "SELECT criterion_id, description, weight FROM criterion WHERE event_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$criteria = [];
while ($row = mysqli_fetch_assoc($result)) {
    $criteria[] = $row;
}

echo json_encode($criteria);