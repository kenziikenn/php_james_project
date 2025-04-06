<?php
session_start();
if (!isset($_SESSION['account_id']) || $_SESSION['type_id'] != 2) {
    header("Location: ../index.php");
    exit();
}

// Fix the session variable check and event_id assignment
if (!isset($_SESSION['selected_event_id'])) {
    header("Location: select_event.php");
    exit();
}

$event_id = $_SESSION['selected_event_id']; // Changed from selected_events_id to selected_event_id

// Add error handling for the database connection
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all scores and rankings
$query = "SELECT 
    CONCAT(c.first_name, ' ', COALESCE(c.middle_name, ''), ' ', c.last_name) as candidate_name,
    CONCAT(j.first_name, ' ', j.last_name) as judge_name,
    cr.description as criteria,
    s.score,
    RANK() OVER (PARTITION BY j.id ORDER BY s.score DESC) as judge_ranking
FROM scores s
JOIN candidate c ON s.candidate_id = c.candidate_id
JOIN judges j ON s.judge_id = j.id
JOIN criterion cr ON s.criterion_id = cr.criterion_id
ORDER BY c.candidate_id, j.id";

// Update rounds query
$rounds_query = "SELECT 
    CONCAT(c.first_name, ' ', COALESCE(c.middle_name, ''), ' ', c.last_name) as candidate_name,
    r.round_id,
    r.round_name
FROM round_detail rd
JOIN candidate c ON rd.candidate_id = c.candidate_id
JOIN round r ON rd.round_id = r.round_id
ORDER BY rd.round_id";

// Update special awards query
$special_awards_query = "SELECT 
    CONCAT(c.first_name, ' ', COALESCE(c.middle_name, ''), ' ', c.last_name) as candidate_name,
    sa.award_name
FROM special_award_scores sas
JOIN candidate c ON sas.candidate_id = c.candidate_id
JOIN special_awards sa ON sas.award_id = sa.id
WHERE sa.status = 'active'
ORDER BY sa.id";

$result = mysqli_query($conn, $query);
$rounds_result = mysqli_query($conn, $rounds_query);
$special_awards_result = mysqli_query($conn, $special_awards_query);

$scores = [];
$rounds = [];
$special_awards = [];

while($row = mysqli_fetch_assoc($result)) {
    $scores[] = $row;
}
while($row = mysqli_fetch_assoc($rounds_result)) {
    $rounds[] = $row;
}
while($row = mysqli_fetch_assoc($special_awards_result)) {
    $special_awards[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\"device-width\", initial-scale=1.0">
    <title>Ranking and Scoring Management</title>
    <link href="https://unpkg.com/tabulator-tables/dist/css/tabulator.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #2c3e50;
            line-height: 1.6;
        }

        .navbar {
            background: linear-gradient(135deg, #1a237e, #0d47a1);
            padding: 25px;
            text-align: center;
            font-size: 1.8rem;
            font-weight: 600;
            color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .logout-btn {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background-color: #ff3547;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #ff1744;
            transform: translateY(-50%) scale(1.05);
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        h1 {
            color: #1a237e;
            margin-bottom: 30px;
            font-size: 2.2rem;
            font-weight: 700;
        }

        .search-bar {
            width: 70%;
            padding: 15px 25px;
            border: 2px solid #e0e0e0;
            border-radius: 50px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }

        .search-bar:focus {
            outline: none;
            border-color: #1a237e;
            box-shadow: 0 0 15px rgba(26, 35, 126, 0.1);
        }

        .print-btn {
            background: linear-gradient(135deg, #1a237e, #0d47a1);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(26, 35, 126, 0.2);
        }

        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(26, 35, 126, 0.3);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 25px 0;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        th {
            background: linear-gradient(135deg, #1a237e, #0d47a1);
            color: white;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #2c3e50;
        }

        tr:hover {
            background-color: #f8f9ff;
        }

        tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <span>Judging System - Ranking & Scoring</span>
        <a href="../index.php" class="logout-btn">Logout</a>
    </div>

    <div class="container">
        <h1>Contestant Ranking & Scoring</h1>
        <input type="text" id="searchInput" class="search-bar" placeholder="Search Results" onkeyup="searchResults()">
        <button class="print-btn" onclick="printFinalResults()">Print Final Results</button>
        <div id="results"></div>
    </div>

    <script>
        const finalData = <?php echo json_encode($scores); ?>;
        const roundsData = <?php echo json_encode($rounds); ?>;
        const specialAwardsData = <?php echo json_encode($special_awards); ?>;

        function searchResults() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let resultsDiv = document.getElementById("results");
            resultsDiv.innerHTML = "";
            
            let filteredData = finalData.filter(entry => 
                entry.candidate_name.toLowerCase().includes(input) || 
                entry.judge_name.toLowerCase().includes(input) ||
                entry.criteria.toLowerCase().includes(input)
            );
            
            if (filteredData.length > 0) {
                let output = "<table border='1' cellpadding='5' cellspacing='0' style='width: 100%; text-align: center;'>";
                output += "<tr><th>Candidate</th><th>Judge</th><th>Criteria</th><th>Score</th><th>Judge Ranking</th></tr>";
                filteredData.forEach(row => {
                    output += `<tr>
                        <td>${row.candidate_name}</td>
                        <td>${row.judge_name}</td>
                        <td>${row.criteria}</td>
                        <td>${row.score}</td>
                        <td>#${row.judge_ranking}</td>
                    </tr>`;
                });
                output += "</table>";
                resultsDiv.innerHTML = output;
            } else {
                resultsDiv.innerHTML = "<p>No results found.</p>";
            }
        }

        function printFinalResults() {
            let currentDate = new Date().toLocaleDateString();
            let printContent = `
                <h1 style="text-align: center;">Judge Final Results</h1>
                <p style="text-align: right;">Date: ${currentDate}</p>
                
                <h2 style="margin: 20px 0;">Scoring Results</h2>
                <table border='1' cellpadding='5' cellspacing='0' style='width: 100%; text-align: center; margin: 20px 0;'>
                <tr><th>Candidate</th><th>Judge</th><th>Criteria</th><th>Score</th><th>Judge Ranking</th></tr>`;
            
            finalData.forEach(row => {
                printContent += `<tr>
                    <td>${row.candidate_name}</td>
                    <td>${row.judge_name}</td>
                    <td>${row.criteria}</td>
                    <td>${row.score}</td>
                    <td>#${row.judge_ranking}</td>
                </tr>`;
            });
            
            printContent += `</table>
                
                <h2 style="margin: 20px 0;">Round Results</h2>
                <table border='1' cellpadding='5' cellspacing='0' style='width: 100%; text-align: center; margin: 20px 0;'>
                <tr><th>Round</th><th>Winner</th></tr>`;
            
            roundsData.forEach(row => {
                printContent += `<tr>
                    <td>${row.round_name}</td>
                    <td>${row.candidate_name}</td>
                </tr>`;
            });
            
            printContent += `</table>
                
                <h2 style="margin: 20px 0;">Special Awards</h2>
                <table border='1' cellpadding='5' cellspacing='0' style='width: 100%; text-align: center; margin: 20px 0;'>
                <tr><th>Award</th><th>Winner</th></tr>`;
            
            specialAwardsData.forEach(row => {
                printContent += `<tr>
                    <td>${row.award_name}</td>
                    <td>${row.candidate_name}</td>
                </tr>`;
            });
            
            printContent += `</table>
                
                <div style="margin-top: 50px; text-align: center;">
                    <p style="margin-bottom: 80px;">
                        <strong>Certified by:</strong>
                    </p>
                    <div style="display: inline-block; margin: 0 50px;">
                        <hr style="width: 200px;">
                        <p>Tabulator Signature</p>
                    </div>
                    <div style="display: inline-block; margin: 0 50px;">
                        <hr style="width: 200px;">
                        <p>Event Coordinator</p>
                    </div>
                </div>`;
            
            const newWindow = window.open();
            newWindow.document.write(`
                <html>
                    <head>
                        <title>Judge Final Results</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 20px; }
                            table { border-collapse: collapse; width: 100%; }
                            th, td { border: 1px solid #000; padding: 8px; }
                            th { background-color: #f2f2f2; }
                        </style>
                    </head>
                    <body>${printContent}</body>
                </html>
            `);
            newWindow.print();
            newWindow.close();
        }
    </script>

   
</script>
</body>
</html>