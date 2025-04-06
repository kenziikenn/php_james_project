<?php
session_start();
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

if (!isset($_GET['event_id'])) {
    echo json_encode(['error' => 'Event ID is required']);
    exit();
}

$event_id = (int)$_GET['event_id'];

$criteria_query = "SELECT criterion_id, description, weight 
                  FROM criterion 
                  WHERE event_id = ? 
                  ORDER BY weight DESC";

$stmt = $conn->prepare($criteria_query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$criteria = [];
while ($row = $result->fetch_assoc()) {
    $criteria[] = $row;
}

echo json_encode($criteria);
$stmt->close();
$conn->close();
?>