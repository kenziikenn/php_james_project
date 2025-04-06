<?php
session_start();

if (isset($_GET['error'])) {
    header("Location: ../index.php?error");
}

// Process contestant form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addContestant"])) {
    $contestantName = trim($_POST["contestantName"]);

    if (!empty($contestantName)) {
        // Store the contestant (in real projects, save to a database)
        $_SESSION["contestants"][] = $contestantName;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <title>Judging System - Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
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

        .content h1 {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .btn-custom {
            background-color: #ff9800;
            border: none;
            padding: 12px 30px;
            font-size: 1rem;
            color: white;
            border-radius: 25px;
            transition: background-color 0.3s, transform 0.3s;
            cursor: pointer;
        }

        .btn-custom:hover {
            background-color: #e68900;
            transform: scale(1.05);
        }

        .hidden {
            display: none;
        }

        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            display: inline-block;
            text-align: left;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <span>Judging System - Admin Dashboard</span>
    </nav>
    
    <div class="sidebar">
        <a href="manage_events.php" onclick="showSection('contest')">🏆 Manage Events</a>
        <a href="manage_judge.php" onclick="showSection('judges')">🧑‍⚖️ Manage Judges</a>
        <a href="#" onclick="showSection('ranking')">📊 Manage Ranking & Scoring</a>
        <a href="manage_criteria.php" onclick="showSection('criteria')">📝 Manage Criteria</a>
        <a href="manage_contestants.php" onclick="showSection('contestants')">👤 Manage Contestants</a>
        <a href="log-out.php" class="btn-custom">🚪 Logout</a>
    </div>
    
    <div class="content">
        <h1>Welcome to the Admin Dashboard</h1>
        <h2>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>Select an option from the sidebar to manage the system.</p>

        <div id="contest" class="hidden">
            <h2>🏆 Manage Events</h2>
            <p>Create and manage events.</p>
            <a href="manage_events.php" class="btn-custom">Manage Events</a>
        </div>

        <div id="judges" class="hidden">
            <h2>🧑‍⚖️ Manage Judges</h2>
            <p>Add, remove, and manage judges for events.</p>
            <a href="manage_judges.php" class="btn-custom">Manage Judges</a>
        </div>

        <div id="ranking" class="hidden">
            <h2>📊 Manage Ranking & Scoring</h2>
            <p>Set up rankings and scoring systems for each event.</p>
            <a href="manage_ranking.php" class="btn-custom">Manage Rankings</a>
        </div>

        <div id="criteria" class="hidden">
            <h2>📝 Manage Criteria</h2>
            <p>Define the criteria for evaluating participants.</p>
            <a href="manage_criteria.php" class="btn-custom">Manage Criteria</a>
        </div>

        <div id="contestants" class="hidden">
            <h2>👤 Manage Contestants</h2>
            <p>Add, remove, and manage contestants.</p>
            <a href="manage_contestants.php" class="btn-custom">Manage Criteria</a>
            </div>
    </script>

</body>
</html>
