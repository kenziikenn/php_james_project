<?php
session_start();
require_once(__DIR__ . '/../db_connection.php');

if (!isset($_POST['event_id'])) {
    $_SESSION['error'] = "No event specified";
    header('Location: manage_ranking.php');
    exit();
}

$event_id = (int)$_POST['event_id'];

// Check if all judges have submitted their scores
$check_query = "SELECT 
                    (SELECT COUNT(DISTINCT judge_id) FROM scores s 
                     JOIN candidate c ON s.candidate_id = c.candidate_id
                     WHERE c.event_id = ?) as judges_submitted,
                    (SELECT COUNT(*) FROM judges WHERE event_id = ?) as total_judges";
$stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($stmt, "ii", $event_id, $event_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt)->fetch_assoc();

if ($result['judges_submitted'] < $result['total_judges']) {
    $_SESSION['error'] = "Cannot finalize: Not all judges have submitted their scores";
    header('Location: manage_ranking.php?event_id=' . $event_id);
    exit();
}

// Update event status to finalized
$update_query = "UPDATE event SET status = 'finalized' WHERE event_id = ?";
$stmt = mysqli_prepare($conn, $update_query);
mysqli_stmt_bind_param($stmt, "i", $event_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Scores have been finalized successfully";
} else {
    $_SESSION['error'] = "Error finalizing scores";
}

header('Location: manage_ranking.php?event_id=' . $event_id);