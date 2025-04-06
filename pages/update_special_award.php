<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");

if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . mysqli_connect_error()]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $event_id = mysqli_real_escape_string($conn, $_POST['event_id']);
    $award_name = mysqli_real_escape_string($conn, $_POST['award_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $query = "UPDATE special_awards 
              SET event_id = '$event_id',
                  award_name = '$award_name',
                  description = '$description'
              WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Award updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

mysqli_close($conn);
?>