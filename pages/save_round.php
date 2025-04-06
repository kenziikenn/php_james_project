<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");

if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $round_name = mysqli_real_escape_string($conn, $_POST['round_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $sql = "INSERT INTO round (round_name, description) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $round_name, $description);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

mysqli_close($conn);
?>