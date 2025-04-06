<?php
session_start();
if(isset($_POST['event_id'])) {
    $_SESSION['selected_event_id'] = (int)$_POST['event_id'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>