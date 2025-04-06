<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . mysqli_connect_error()]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    if (!isset($_POST['event_id']) || !isset($_POST['award_name'])) {
        die(json_encode(['success' => false, 'message' => 'Missing required fields']));
    }

    $event_id = mysqli_real_escape_string($conn, $_POST['event_id']);
    $award_name = mysqli_real_escape_string($conn, $_POST['award_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');

    // Insert the award
    $query = "INSERT INTO special_awards (event_id, award_name, description, status) 
              VALUES ('$event_id', '$award_name', '$description', 'active')";

    try {
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'message' => 'Award added successfully']);
        } else {
            throw new Exception(mysqli_error($conn));
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

mysqli_close($conn);
?>