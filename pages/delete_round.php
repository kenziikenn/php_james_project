<?php
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $query = "DELETE FROM round WHERE round_id = $id";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete round']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Round ID is required']);
}
?>