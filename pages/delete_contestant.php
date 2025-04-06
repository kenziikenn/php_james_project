<?php
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

if (isset($_POST['id'])) {
    $candidate_id = mysqli_real_escape_string($conn, $_POST['id']);
    
    try {
        mysqli_begin_transaction($conn);
        
        // Delete from related tables first
        /* if (!mysqli_query($conn, "DELETE FROM event_participants WHERE candidate_id = '$candidate_id'")) {
            throw new Exception(mysqli_error($conn));
        } */
        if (!mysqli_query($conn, "DELETE FROM result WHERE candidate_id = '$candidate_id'")) {
            throw new Exception(mysqli_error($conn));
        }
        if (!mysqli_query($conn, "DELETE FROM round_detail WHERE candidate_id = '$candidate_id'")) {
            throw new Exception(mysqli_error($conn));
        }
        if (!mysqli_query($conn, "DELETE FROM scores WHERE candidate_id = '$candidate_id'")) {
            throw new Exception(mysqli_error($conn));
        }
        if (!mysqli_query($conn, "DELETE FROM special_award_scores WHERE candidate_id = '$candidate_id'")) {
            throw new Exception(mysqli_error($conn));
        }
        /* if (!mysqli_query($conn, "DELETE FROM judge_scores WHERE candidate_id = '$candidate_id'")) {
            throw new Exception(mysqli_error($conn));
        } */

        // Then delete the candidate
        if (!mysqli_query($conn, "DELETE FROM candidate WHERE candidate_id = '$candidate_id'")) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_commit($conn);
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode([
            'success' => false,
            'message' => 'Deletion failed: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

mysqli_close($conn);
?>