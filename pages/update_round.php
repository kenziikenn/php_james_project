<?php
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $round_id = $_POST['round_id'];
    $round_name = $_POST['round_name'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE round SET round_name = ?, description = ? WHERE round_id = ?");
    $stmt->bind_param("ssi", $round_name, $description, $round_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    $stmt->close();
}
?>