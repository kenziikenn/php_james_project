<?php
session_start();
require_once('../includes/db_connection.php');

header('Content-Type: application/json');

try {
    $award_id = $_POST['award_id'];
    $points = $_POST['award_points'];
    $event_id = $_SESSION['selected_event_id'];

    // Update rankings in the special_award_rankings table
    foreach ($points as $candidate_id => $score) {
        $stmt = $conn->prepare("INSERT INTO special_award_rankings (award_id, candidate_id, points, event_id) 
                               VALUES (?, ?, ?, ?) 
                               ON DUPLICATE KEY UPDATE points = ?");
        $stmt->bind_param('iiiii', $award_id, $candidate_id, $score, $event_id, $score);
        $stmt->execute();
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>