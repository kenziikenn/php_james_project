<?php
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

if ($id && in_array($status, ['active', 'inactive'])) {
    $query = "UPDATE round SET status = '$status' WHERE round_id = $id";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
}
?>