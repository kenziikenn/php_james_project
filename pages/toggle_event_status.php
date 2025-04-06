<?php
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = mysqli_real_escape_string($conn, $_POST['event_id']);
    
    // Get current status
    $query = "SELECT status FROM event WHERE event_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $event_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $event = mysqli_fetch_assoc($result);
    
    // Toggle status
    $new_status = $event['status'] === 'Active' ? 'Inactive' : 'Active';
    
    $update_query = "UPDATE event SET status = ? WHERE event_id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $event_id);
    
    $response = [
        'success' => mysqli_stmt_execute($stmt),
        'status' => $new_status
    ];
    
    echo json_encode($response);
}
?>