<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['judge_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$candidate_id = isset($_GET['candidate_id']) ? (int)$_GET['candidate_id'] : 0;

$scores_query = "SELECT c.first_name, c.last_name, cr.description as criterion_name, s.score 
                 FROM candidate c
                 LEFT JOIN scores s ON c.candidate_id = s.candidate_id
                 LEFT JOIN criterion cr ON s.criterion_id = cr.criterion_id
                 WHERE c.candidate_id = ?";

$stmt = $conn->prepare($scores_query);
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$result = $stmt->get_result();

$scores = [];
while ($row = $result->fetch_assoc()) {
    $scores[] = [
        'candidate_name' => $row['first_name'] . ' ' . $row['last_name'],
        'criterion_name' => $row['criterion_name'],
        'score' => $row['score']
    ];
}

echo json_encode($scores);
$stmt->close();
$conn->close();