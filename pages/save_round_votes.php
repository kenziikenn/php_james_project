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

if (isset($_POST['round_votes'])) {
    foreach ($_POST['round_votes'] as $round_id => $candidate_id) {
        $round_id = mysqli_real_escape_string($conn, $round_id);
        $candidate_id = mysqli_real_escape_string($conn, $candidate_id);
        $judge_id = $_SESSION['judge_id'];

        // Check if vote exists
        $check_query = "SELECT * FROM round_detail WHERE round_id = '$round_id' AND candidate_id = '$candidate_id'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            // Update existing vote
            $update_query = "UPDATE round_detail SET judge_id = '$judge_id' 
                           WHERE round_id = '$round_id' AND candidate_id = '$candidate_id'";
            mysqli_query($conn, $update_query);
        } else {
            // Insert new vote
            $insert_query = "INSERT INTO round_detail (round_id, candidate_id, judge_id) 
                           VALUES ('$round_id', '$candidate_id', '$judge_id')";
            mysqli_query($conn, $insert_query);
        }
    }
    
    $_SESSION['success'] = "Round votes saved successfully!";
} else {
    $_SESSION['error'] = "No votes submitted";
}

header("Location: judge_homepage.php");
exit();
?>