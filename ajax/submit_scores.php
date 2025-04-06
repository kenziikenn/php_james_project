<?php
session_start();
require_once('../config/database.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['judge_id'])) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }

    $event_id = $_POST['event_id'];
    $criterion_id = $_POST['criterion_id'];
    $scores = $_POST['scores'];
    $judge_id = $_SESSION['judge_id'];

    // Get criterion weight
    $weight_query = "SELECT weight FROM criterion WHERE criterion_id = ?";
    $stmt = mysqli_prepare($conn, $weight_query);
    mysqli_stmt_bind_param($stmt, 'i', $criterion_id);
    mysqli_stmt_execute($stmt);
    $weight_result = mysqli_stmt_get_result($stmt);
    $criterion = mysqli_fetch_assoc($weight_result);
    
    if (!$criterion) {
        echo json_encode(['success' => false, 'message' => 'Invalid criterion']);
        exit;
    }

    // Validate scores against weight
    foreach ($scores as $score) {
        if ($score > $criterion['weight']) {
            echo json_encode(['success' => false, 'message' => 'Score cannot exceed criterion weight of ' . $criterion['weight'] . '%']);
            exit;
        }
    }

    try {
        mysqli_begin_transaction($conn);

        foreach ($scores as $candidate_id => $score) {
            // Check if score already exists
            $check_query = "SELECT id FROM scores WHERE judge_id = ? AND candidate_id = ? AND criterion_id = ?";
            $stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($stmt, 'iii', $judge_id, $candidate_id, $criterion_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                // Update existing score
                $update_query = "UPDATE scores SET score = ? WHERE judge_id = ? AND candidate_id = ? AND criterion_id = ?";
                $stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($stmt, 'diii', $score, $judge_id, $candidate_id, $criterion_id);
            } else {
                // Insert new score
                $insert_query = "INSERT INTO scores (judge_id, candidate_id, criterion_id, score, event_id) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $insert_query);
                mysqli_stmt_bind_param($stmt, 'iiidd', $judge_id, $candidate_id, $criterion_id, $score, $event_id);
            }
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception(mysqli_error($conn));
            }
        }

        mysqli_commit($conn);
        echo json_encode(['success' => true, 'message' => 'Scores submitted successfully']);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>