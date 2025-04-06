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
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automated Judging System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: #1a1a2e;
            color: #fff;
            min-height: 100vh;
        }

        @keyframes fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0.95);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes slideInRight {
            0% {
                opacity: 0;
                transform: translateX(-100%);
            }
            50% {
                transform: translateX(10px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes floatIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .navbar {
            animation: fadeInScale 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar {
            animation: slideInRight 1.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .sidebar a {
            opacity: 0;
            animation: floatIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            animation-delay: calc(var(--order) * 0.1s + 0.5s);
        }

        .overview-container {
            opacity: 0;
            animation: fadeInScale 1s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            animation-delay: 0.3s;
        }

        .welcome-message {
            opacity: 0;
            animation: floatIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            animation-delay: 0.8s;
        }

        .overview-text {
            opacity: 0;
            animation: floatIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            animation-delay: 1s;
        }

        .navbar {
            background: linear-gradient(135deg, #16213e, #0f3460);
            padding: 18px 30px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            animation: slideInFade 0.8s ease-out forwards;
        }


        .sidebar {
            background: #0d1b2a;
            width: 270px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 75px;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.2);
            animation: slideInFromLeft 1s ease-out forwards;
        }

        .sidebar a {
            margin: 8px 12px;
            padding: 14px 20px;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            background: #1b263b;
            border: none;
            display: flex;
            align-items: center;
        }

        .sidebar a:hover {
            background: #415a77;
            color: #fff;
            transform: translateX(5px);
        }

        .btn-custom {
            background: #e63946 !important;
            color: #fff;
            position: absolute;
            bottom: 20px;
            width: calc(100% - 35px);
            padding: 14px;
            text-align: center;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: none !important;
        }

        .btn-custom:hover {
            background: #c1121f !important;
            transform: translateY(-2px);
        }

        .content {
            margin-left: 270px;
            padding: 85px 25px 25px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .overview-container {
            background: linear-gradient(145deg, #16213e, #1a1a2e);
            padding: 40px;
            border-radius: 4px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3),
                        inset 0 1px 1px rgba(255, 255, 255, 0.1);
            max-width: 1200px;
            margin: 0 auto;
            border: 2px solid rgba(233, 69, 96, 0.2);
            animation: fadeInUp 0.8s ease-out forwards;
            position: relative;
            overflow: hidden;
        }

        .welcome-message {
            color: #fff;
            font-size: 20px;
            text-align: center;
            margin-bottom: 35px;
            padding: 25px;
            background: rgba(233, 69, 96, 0.08);
            border-radius: 4px;
            border: 1px solid rgba(233, 69, 96, 0.2);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s ease-out forwards;
            animation-delay: 0.3s;
            position: relative;
        }

        .welcome-message::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, #e94560, #c81d3e);
        }

        .overview-text {
            color: #d1d1e1;
            font-size: 18px;
            text-align: justify;
            line-height: 1.9;
            padding: 30px;
            background: rgba(15, 52, 96, 0.3);
            border-radius: 4px;
            border: 1px solid rgba(233, 69, 96, 0.1);
            margin-top: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s ease-out forwards;
            animation-delay: 0.5s;
            position: relative;
        }

        .overview-text::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, rgba(15, 52, 96, 0), rgba(233, 69, 96, 0.3));
        }

        .btn-custom {
            background: linear-gradient(135deg, #e94560, #c81d3e) !important;
            color: #fff;
            position: absolute;
            bottom: 20px;
            width: calc(100% - 35px);
            padding: 14px;
            text-align: center;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: none !important;
            box-shadow: 0 4px 15px rgba(233, 69, 96, 0.3);
        }

        .btn-custom:hover {
            background: linear-gradient(135deg, #c81d3e, #e94560) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(233, 69, 96, 0.4);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <span>Automated Judging System</span>
    </nav>
    
    <div class="sidebar">
        <a href="manage_events.php">🏆 Manage Events</a>
        <a href="manage_judge.php">🧑‍⚖️ Manage Judges</a>
        <a href="manage_ranking.php">📊 Manage Ranking & Scoring</a>
        <a href="manage_criteria.php">📝 Manage Criteria</a>
        <a href="manage_contestants.php">👤 Manage Contestants</a>
        <a href="manage_rounds.php">🔄 Manage Rounds</a>
        <a href="manage_accounts.php">👥 Manage Accounts</a>
        <a href="manage_special_awards.php">🌟 Manage Special Awards</a>
        <a href="log-out.php" class="btn-custom">🚪 Logout</a>
    </div>
    
    <div class="content">
        <div class="overview-container">
            <h1>Welcome to the Automated Judging System</h1>
            <div class="welcome-message">
                Welcome, Admin! Please use the sidebar menu to access and manage different aspects of the judging system. Each module provides specific functionality for managing events, judges, contestants, scoring, and other essential components of the competition system.
            </div>
            <div class="overview-text">
                An automated judging system is a technology-driven solution designed to evaluate and score submissions in various competitions or assessments by using predefined criteria and algorithms, offering a fast, consistent, and objective approach to decision-making, while minimizing human intervention and reducing the potential for bias or errors. These systems can handle large volumes of submissions simultaneously, providing accurate results in real-time and often generating feedback for participants, making them ideal for scenarios like coding contests, sports events, educational assessments, and creative competitions.
            </div>
        </div>
    </div>
    <?php require_once('../includes/system_core.php'); ?>
</body>
</html>
