<?php
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judgeName = trim($_POST['judge_name']);
    $judgeId = isset($_POST['judgeId']) ? $_POST['judgeId'] : null;

    if ($judgeId) {
        // Edit existing judge
        $stmt = $conn->prepare("UPDATE judges SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $judgeName, $judgeId);
        if ($stmt->execute()) {
            header("Location: manage_judge.php?success=1&action=edit");
        } else {
            header("Location: manage_judge.php?error=1");
        }
    } else {
        // Add new judge
        $judgeCode = "JUDGE-" . rand(100000, 999999);
        $stmt = $conn->prepare("INSERT INTO judges (name, judge_code) VALUES (?, ?)");
        $stmt->bind_param("ss", $judgeName, $judgeCode);
        if ($stmt->execute()) {
            header("Location: manage_judge.php?success=1");
        } else {
            header("Location: manage_judge.php?error=1");
        }
    }
}

$conn->close();
?>