<?php
session_start();
include("../include/db_connect.php");

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventId = $_POST["eventId"];
    $firstName = trim($_POST["firstName"]);
    $middleName = trim($_POST["middleName"]);
    $lastName = trim($_POST["lastName"]);
    $about = trim($_POST["about"]);
    $status = 'active';
    
    // Handle file upload
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($_FILES["contestantImage"]["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . uniqid() . '.' . $imageFileType;
    
    // Check if image file is valid
    $valid_types = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $valid_types)) {
        echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed.']);
        exit;
    }
    
    if (move_uploaded_file($_FILES["contestantImage"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO candidate (first_name, middle_name, last_name, about, image, status, event_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $firstName, $middleName, $lastName, $about, $target_file, $status, $eventId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error uploading file']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>