<?php
session_start();
if (!isset($_SESSION['account_id']) || $_SESSION['type_id'] != 2) {
    header("Location: ../index.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT * FROM event WHERE status = 'active'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Event</title>
    <style>
        body {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Poppins", sans-serif;
            animation: gradientMove 15s ease infinite;
            margin: 0;
        }

        .container {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 90%;
        }

        h1 {
            color: white;
            margin-bottom: 30px;
            font-size: 2em;
        }

        .event-list {
            display: grid;
            gap: 15px;
            margin: 20px 0;
        }

        .event-button {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 15px 25px;
            border-radius: 12px;
            color: white;
            font-size: 1.1em;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .event-button:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Select Event</h1>
        <div class="event-list">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <button class="event-button" onclick="selectEvent(<?php echo $row['event_id']; ?>)">
                    <?php echo htmlspecialchars($row['event_name']); ?>
                </button>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function selectEvent(eventId) {
            fetch('select_event_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'event_id=' + eventId
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.href = 'tabulator.php';
                }
            });
        }
    </script>
</body>
</html>