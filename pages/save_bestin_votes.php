<?php
session_start();
if (!isset($_SESSION['judge_id'])) {
    header("Location: ../judgesignin.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bestin'])) {
    $success = true;
    $message = '';
    
    foreach ($_POST['bestin'] as $round_id => $candidate_id) {
        if (!empty($candidate_id)) {
            // First check if record exists
            $check_query = "SELECT * FROM round_detail WHERE round_id = ? AND candidate_id = ?";
            $stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($stmt, "ii", $round_id, $candidate_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                // Update existing record
                $update_query = "UPDATE round_detail 
                               SET round_id = ?, candidate_id = ? 
                               WHERE round_id = ? AND candidate_id = ?";
                
                $stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($stmt, "iiii", $round_id, $candidate_id, $round_id, $candidate_id);
            } else {
                // Insert new record
                $insert_query = "INSERT INTO round_detail (round_id, candidate_id) 
                               VALUES (?, ?)";
                
                $stmt = mysqli_prepare($conn, $insert_query);
                mysqli_stmt_bind_param($stmt, "ii", $round_id, $candidate_id);
            }
            
            if (!mysqli_stmt_execute($stmt)) {
                $success = false;
                $message = "Error saving vote for round " . $round_id;
                break;
            }
        }
    }
    
    if ($success) {
        $_SESSION['success'] = "Best In votes saved successfully!";
    } else {
        $_SESSION['error'] = $message;
    }
    
    header("Location: judge_homepage.php");
    exit();
}

header("Location: judge_homepage.php");
?>