<?php
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventName = mysqli_real_escape_string($conn, $_POST['eventName']);
    $eventTime = mysqli_real_escape_string($conn, $_POST['eventTime']);
    $eventId = isset($_POST['eventId']) ? mysqli_real_escape_string($conn, $_POST['eventId']) : null;
    
    if ($eventId) {
        // Update existing event
        $query = "UPDATE event SET event_name = ?, event_date = ? WHERE event_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $eventName, $eventTime, $eventId);
    } else {
        // Create new event
        $query = "INSERT INTO event (event_name, event_date, status) VALUES (?, ?, 'Active')";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $eventName, $eventTime);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        $action = isset($_GET['action']) ? '&action=' . $_GET['action'] : '';
        header("Location: manage_events.php?success=1" . $action);
    } else {
        header("Location: manage_events.php?error=1");
    }
}
?>