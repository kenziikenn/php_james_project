<?php
session_start();
require_once('includes/db_connection.php');

header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$event_id = $data['event_id'] ?? null;

if (!$event_id) {
    echo json_encode(['success' => false, 'message' => 'No event ID provided']);
    exit;
}

try {
    // Start transaction
    mysqli_begin_transaction($conn);

    // Get final rankings
    $ranking_query = "
        SELECT 
            c.candidate_id,
            CONCAT(c.first_name, ' ', COALESCE(c.middle_name, ''), ' ', c.last_name) as candidate_name,
            SUM(s.score) as total_score,
            RANK() OVER (ORDER BY SUM(s.score) DESC) as rank
        FROM candidate c
        LEFT JOIN scores s ON c.candidate_id = s.candidate_id
        WHERE c.event_id = ?
        GROUP BY c.candidate_id
        ORDER BY rank, c.candidate_id";

    $stmt = mysqli_prepare($conn, $ranking_query);
    mysqli_stmt_bind_param($stmt, "i", $event_id);
    mysqli_stmt_execute($stmt);
    $rankings = mysqli_stmt_get_result($stmt);

    // Insert into results table
    while ($row = mysqli_fetch_assoc($rankings)) {
        $insert_query = "INSERT INTO final_results 
                        (event_id, candidate_id, final_score, rank) 
                        VALUES (?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                        final_score = VALUES(final_score),
                        rank = VALUES(rank)";
        
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "iidi", 
            $event_id, 
            $row['candidate_id'], 
            $row['total_score'], 
            $row['rank']
        );
        mysqli_stmt_execute($stmt);
    }

    // Update event status to finalized
    $update_status = "UPDATE event SET status = 'finalized' WHERE event_id = ?";
    $stmt = mysqli_prepare($conn, $update_status);
    mysqli_stmt_bind_param($stmt, "i", $event_id);
    mysqli_stmt_execute($stmt);

    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Results saved successfully']);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>