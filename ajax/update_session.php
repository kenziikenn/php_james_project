<?php
session_start();
header('Content-Type: application/json');

if (isset($_GET['event_id'])) {
    $_SESSION['selected_event_id'] = $_GET['event_id'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No event ID provided']);
}
?>