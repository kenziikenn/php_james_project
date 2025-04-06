<?php
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id'];
    
    $sql = "DELETE FROM criterion WHERE criterion_id = $id";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
}
mysqli_close($conn);
?>