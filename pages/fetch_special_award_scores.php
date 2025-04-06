<?php
session_start();
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die(json_encode(['error' => 'Database connection failed']));
}

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$awardId = mysqli_real_escape_string($conn, $data['award_id']);
$eventId = mysqli_real_escape_string($conn, $data['event_id']);
$judgeId = mysqli_real_escape_string($conn, $data['judge_id']);

$query = "SELECT c.candidate_id, 
                 CONCAT(c.first_name, ' ', c.last_name) as candidate_name,
                 s.score 
          FROM candidate c
          LEFT JOIN special_award_scores s 
            ON c.candidate_id = s.candidate_id 
            AND s.award_id = '$awardId'
            AND s.judge_id = '$judgeId'
          WHERE c.event_id = '$eventId'";

$result = mysqli_query($conn, $query);
$scores = [];

while ($row = mysqli_fetch_assoc($result)) {
    $scores[] = $row;
}

echo json_encode($scores);
mysqli_close($conn);
?>