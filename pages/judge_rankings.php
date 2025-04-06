<?php
session_start();

if(isset($_GET['error']))
{
    header ("Location: ../index.php?error");
}
?>
<?php
// Connect to database
include '../../config.php';

$judge_id = $_SESSION['user_id'];
$rankings = [];

// Fetch rankings from database
$query = "SELECT contestants.name AS contestant, criteria.name AS criterion, scores.score 
          FROM scores 
          JOIN contestants ON scores.contestant_id = contestants.id 
          JOIN criteria ON scores.criteria_id = criteria.id 
          WHERE scores.judge_id = ? 
          ORDER BY contestants.name, criteria.name";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $judge_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $rankings[$row['contestant']][] = [
        'criterion' => $row['criterion'],
        'score' => $row['score']
    ];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Rankings</title>
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Poppins', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #444;
            padding: 20px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            color: #fff;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 220px;
            background-color: #222;
            padding-top: 20px;
            box-shadow: 2px 0px 10px rgba(0,0,0,0.1);
        }
        .sidebar a {
            padding: 15px 20px;
            color: #ccc;
            text-decoration: none;
            display: block;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #ff9800;
            color: white;
            padding-left: 30px;
        }
        .content {
            margin-left: 240px;
            padding: 40px;
            text-align: center;
        }
        table {
            margin: auto;
            border-collapse: collapse;
            width: 80%;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #444;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <span>Your Rankings</span>
    </nav>
    
    <div class="sidebar">
        <a href="judging_panel.php">🏅 Rank Contestants</a>
        <a href="judge_rankings.php">📊 Your Rankings</a>
        <a href="log-out.php" class="btn-custom">🚪 Logout</a>
    </div>
    
    <div class="content">
        <h1>Your Submitted Rankings</h1>
        
        <?php if (empty($rankings)): ?>
            <p>No rankings submitted yet.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Contestant</th>
                    <th>Criteria Scores</th>
                </tr>
                <?php foreach ($rankings as $contestant => $scores): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($contestant); ?></td>
                        <td>
                            <ul>
                                <?php foreach ($scores as $score): ?>
                                    <li><?php echo htmlspecialchars($score['criterion']) . ": " . htmlspecialchars($score['score']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
