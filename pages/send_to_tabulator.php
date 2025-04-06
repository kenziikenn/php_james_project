<?php
session_start();
header('Content-Type: application/json');

// Check if the request is POST and is JSON
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER["CONTENT_TYPE"]) || stripos($_SERVER["CONTENT_TYPE"], 'application/json') === false) {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$event_id = isset($data['event_id']) ? intval($data['event_id']) : 0;

// Validate event_id
if ($event_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
    exit();
}

// Connect to database
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

try {
    // Update event status to 'tabulated'
    $update_query = "UPDATE event SET status = 'tabulated' WHERE event_id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "i", $event_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Results successfully tabulated']);
    } else {
        throw new Exception('Failed to update event status');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    mysqli_close($conn);
}
?>