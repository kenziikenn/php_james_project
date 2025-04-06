<?php
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $weight = (int)$_POST['weight'];
    $event_id = (int)$_POST['event_id'];
    
    $sql = "INSERT INTO criterion (description, weight, event_id) VALUES ('$description', $weight, $event_id)";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn)]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
}
mysqli_close($conn);
?>