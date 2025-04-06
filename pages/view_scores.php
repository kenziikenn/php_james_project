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

// Fetch judge's scores
$scores_query = "SELECT 
                    CONCAT(c.first_name, ' ', COALESCE(c.middle_name, ''), ' ', c.last_name) as full_name,
                    cr.description, 
                    cr.weight, 
                    s.score 
                FROM scores s 
                JOIN candidate c ON s.candidate_id = c.candidate_id
                JOIN criterion cr ON s.criterion_id = cr.criterion_id
                WHERE s.judge_id = ?";

$stmt = mysqli_prepare($conn, $scores_query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['judge_id']);
mysqli_stmt_execute($stmt);
$scores_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Scores - Judge Dashboard</title>
    <style>
        /* Copy existing styles from judge_homepage.php */
        body {
            background: linear-gradient(-45deg, #2c3e50, #3498db, #2980b9, #34495e);
            background-size: 300% 300%;
            color: #fff;
            min-height: 100vh;
            font-family: "Poppins", sans-serif;
        }

        .navbar {
            background: rgba(44, 62, 80, 0.9);
            padding: 15px;
            text-align: center;
        }

        .content {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .section-card {
            background: rgba(44, 62, 80, 0.8);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .scores-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .scores-table th, 
        .scores-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid rgba(52, 152, 219, 0.3);
        }

        .scores-table th {
            background: rgba(52, 152, 219, 0.3);
        }

        .btn-back {
            background: #3498db;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Judge Dashboard - Current Scores</h1>
    </div>

    <div class="content">
        <a href="judge_homepage.php" class="btn-back">← Back to Dashboard</a>
        
        <div class="section-card">
            <h2>Your Assigned Scores</h2>
            <table class="scores-table">
                <thead>
                    <tr>
                        <th>Candidate Name</th>
                        <th>Criteria</th>
                        <th>Weight</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($score = mysqli_fetch_assoc($scores_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($score['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($score['description']); ?></td>
                        <td><?php echo htmlspecialchars($score['weight']); ?>%</td>
                        <td><?php echo htmlspecialchars($score['score']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>