<?php
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$events_query = "SELECT * FROM event WHERE status = 'Active' ORDER BY event_date DESC";
$events = mysqli_query($conn, $events_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ongoing Events</title>
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
            padding: 15px;
            text-align: center;
            font-size: 1.8rem;
            font-weight: bold;
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
        }

        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .event-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
        }

        .event-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .event-date {
            color: #666;
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border-radius: 15px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="navbar">Ongoing Events</div>
    <div class="sidebar">
        <a href="hp.php">Back to Homepage</a>
        <a href="manage_events.php">Back to Manage Events</a>
    </div>

    <div class="content">
        <div class="event-grid">
            <?php while($event = mysqli_fetch_assoc($events)): ?>
                <div class="event-card">
                    <div class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></div>
                    <div class="event-date">
                        <?php echo date('F j, Y - g:i A', strtotime($event['event_date'])); ?>
                    </div>
                    <span class="status-badge">Active</span>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>