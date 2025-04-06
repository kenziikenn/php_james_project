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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $candidate_id = $_POST['candidate_id'];
    $judge_id = $_SESSION['judge_id'];
    $scores = $_POST['scores'];

    foreach ($scores as $criterion_id => $score) {
        $stmt = $conn->prepare("INSERT INTO scores (candidate_id, judge_id, criterion_id, score) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $candidate_id, $judge_id, $criterion_id, $score);
        $stmt->execute();
    }

    header("Location: judge_homepage.php");
    exit();
}