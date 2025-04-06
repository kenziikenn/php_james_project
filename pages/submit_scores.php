<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['judge_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judge_id = $_SESSION['judge_id'];
    $criterion_id = $_POST['criterion_id'];
    $event_id = $_POST['event_id'];
    $scores = $_POST['scores'];
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Delete existing scores for this judge, criterion, and candidates
        $delete_query = "DELETE FROM scores WHERE judge_id = ? AND criterion_id = ? AND candidate_id IN (" . implode(',', array_keys($scores)) . ")";
        $stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt, "ii", $judge_id, $criterion_id);
        mysqli_stmt_execute($stmt);
        
        // Insert new scores
        $insert_query = "INSERT INTO scores (judge_id, candidate_id, criterion_id, score) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        
        foreach ($scores as $candidate_id => $score) {
            mysqli_stmt_bind_param($stmt, "iiid", $judge_id, $candidate_id, $criterion_id, $score);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Error inserting score');
            }
        }
        
        mysqli_commit($conn);
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

mysqli_close($conn);
?>