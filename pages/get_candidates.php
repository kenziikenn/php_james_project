<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

$query = "SELECT candidate_id, first_name, middle_name, last_name 
          FROM candidate 
          WHERE event_id = ? AND status = 'active'";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $event_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$candidates = [];
while ($row = mysqli_fetch_assoc($result)) {
    $candidates[] = $row;
}

echo json_encode($candidates);
mysqli_close($conn);