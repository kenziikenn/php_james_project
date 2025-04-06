<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");

if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $candidate_id = mysqli_real_escape_string($conn, $_POST['candidate_id']);
    $award_id = mysqli_real_escape_string($conn, $_POST['award_id']);
    $judge_id = $_SESSION['judge_id'];

    $query = "INSERT INTO special_award_winners (award_id, candidate_id, judge_id) 
              VALUES (?, ?, ?) 
              ON DUPLICATE KEY UPDATE candidate_id = VALUES(candidate_id)";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iii', $award_id, $candidate_id, $judge_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
}
?>