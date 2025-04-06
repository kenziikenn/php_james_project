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

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

// Modified query to correctly join with criterion table
$query = "SELECT 
            c.candidate_id,
            CONCAT(c.first_name, ' ', COALESCE(c.middle_name, ''), ' ', c.last_name) as candidate_name,
            cr.criterion_id,
            cr.description as criterion_name,
            cr.weight,
            COALESCE(s.score, 0) as score
          FROM candidate c
          CROSS JOIN criterion cr
          LEFT JOIN scores s ON c.candidate_id = s.candidate_id 
            AND cr.criterion_id = s.criterion_id
            AND s.judge_id = ?
          WHERE cr.event_id = ?
          ORDER BY c.candidate_id, cr.weight DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $_SESSION['judge_id'], $event_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    if (!isset($data[$row['candidate_id']])) {
        $data[$row['candidate_id']] = [
            'candidate_id' => $row['candidate_id'],
            'candidate_name' => $row['candidate_name'],
            'criteria' => []
        ];
    }
    $data[$row['candidate_id']]['criteria'][] = [
        'criterion_id' => $row['criterion_id'],
        'name' => $row['criterion_name'],
        'weight' => $row['weight'],
        'score' => $row['score']
    ];
}

echo json_encode(array_values($data));
$stmt->close();
$conn->close();