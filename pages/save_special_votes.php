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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['awards'])) {
    $judge_id = $_SESSION['judge_id'];
    $awards = $_POST['awards'];
    
    try {
        // Get a valid round_id first
        $round_query = "SELECT round_id FROM round LIMIT 1";
        $round_result = mysqli_query($conn, $round_query);
        $round = mysqli_fetch_assoc($round_result);
        
        if (!$round) {
            throw new Exception("No valid round found");
        }
        
        // Insert votes into award table with valid round_id
        $insert_query = "INSERT INTO award (award_id, category_id, round_id) VALUES (NULL, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        
        foreach ($awards as $category_id => $candidate_id) {
            mysqli_stmt_bind_param($stmt, "ii", $category_id, $round['round_id']);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception(mysqli_error($conn));
            }
        }
        
        $_SESSION['success'] = "Special awards votes saved successfully!";
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error saving votes: " . $e->getMessage();
        error_log("Error in save_special_votes.php: " . $e->getMessage());
    }
    
    mysqli_close($conn);
    header("Location: judge_homepage.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request";
    header("Location: judge_homepage.php");
    exit();
}
?>