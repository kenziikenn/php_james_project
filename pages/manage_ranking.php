<?php
$conn = mysqli_connect ("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch criteria
$criteria_query = "SELECT * FROM criterion";
$criteria = mysqli_query($conn, $criteria_query);

// Fetch results with rankings and combined scores
$ranking_query = "
    SELECT 
        CONCAT(c.first_name, ' ', COALESCE(c.middle_name, ''), ' ', c.last_name) as candidate_name,
        c.candidate_id,
        GROUP_CONCAT(DISTINCT CONCAT(j.first_name, ' ', j.last_name)) as judges,
        GROUP_CONCAT(DISTINCT CONCAT(cr.description, ': ', s.score) ORDER BY cr.criterion_id) as criteria_scores,
        SUM(s.score) as total_score,
        RANK() OVER (ORDER BY SUM(s.score) DESC) as overall_rank,
        e.event_name,
        e.status as event_status
    FROM scores s
    JOIN candidate c ON s.candidate_id = c.candidate_id
    JOIN judges j ON s.judge_id = j.id
    JOIN criterion cr ON s.criterion_id = cr.criterion_id
    JOIN event e ON e.event_id = ?
    GROUP BY c.candidate_id
    ORDER BY overall_rank, c.candidate_id";

// Prepare and execute the query with event_id
$stmt = mysqli_prepare($conn, $ranking_query);
if (!$stmt) {
    die("Error preparing statement: " . mysqli_error($conn));
}

if (isset($_GET['event_id'])) {
    if (!mysqli_stmt_bind_param($stmt, "i", $_GET['event_id'])) {
        die("Error binding parameters: " . mysqli_stmt_error($stmt));
    }
    if (!mysqli_stmt_execute($stmt)) {
        die("Error executing query: " . mysqli_stmt_error($stmt));
    }
    $rankings = mysqli_stmt_get_result($stmt);
} else {
    $rankings = false;
}

// Store criteria in an array for reuse
$criteria_data = [];
while ($criterion = mysqli_fetch_assoc($criteria)) {
    $criteria_data[] = $criterion;
}
// Add this query at the top with other queries
$events_query = "SELECT event_id, event_name FROM event WHERE status = 'active'";
$events = mysqli_query($conn, $events_query);

// Update the special awards query to use correct column names
$special_awards_query = "
    SELECT 
        CONCAT(c.first_name, ' ', COALESCE(c.middle_name, ''), ' ', c.last_name) as candidate_name,
        cat.description as award_name,
        r.total as votes
    FROM result r
    JOIN candidate c ON r.candidate_id = c.candidate_id
    JOIN category cat ON r.category_id = cat.category_id
    WHERE r.rank = 1
    ORDER BY cat.category_id";

$special_awards = mysqli_query($conn, $special_awards_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Rankings</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }
    
        body {
            background: #0a1929;
            color: #fff;
            min-height: 100vh;
        }
    
        .navbar {
            background: #132f4c;
            padding: 18px 30px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    
        .back-btn {
            background: linear-gradient(135deg, #1e88e5, #1976d2);
            color: #fff;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
    
        .back-btn:hover {
            background: linear-gradient(135deg, #1976d2, #1565c0);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
        }
    
        .content {
            margin-top: 70px;
            padding: 25px;
        }
    
        .scoring-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #132f4c;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(102, 178, 255, 0.1);
        }
    
        .scoring-table th, .scoring-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid rgba(102, 178, 255, 0.1);
        }
    
        .scoring-table th {
            background: #1e4976;
            color: #66b2ff;
            font-weight: 500;
        }
    
        .scoring-table tr:hover {
            background: #1e4976;
        }
    
        .btn-custom {
            background: linear-gradient(135deg, #1e88e5, #1976d2);
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
    
        .btn-custom:hover {
            background: linear-gradient(135deg, #1976d2, #1565c0);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3);
        }
    
        .form-select {
            width: 300px;
            padding: 12px;
            background: #132f4c;
            border: 1px solid rgba(102, 178, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            margin: 10px 0;
        }
    
        .alert {
            background: #1e4976;
            border: 1px solid #66b2ff;
            color: #66b2ff;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
    </style>
    
 
    <body>
        <div class="navbar">
            Manage Ranking & Scoring
            <a href="hp.php" class="back-btn">Back to Homepage</a>
        </div>
        
        <div class="content">
            <!-- Add Event Selection -->
            <div class="event-selector">
                <form method="GET" action="">
                    <select name="event_id" onchange="this.form.submit()" class="event-select">
                        <option value="">Select an Event</option>
                        <?php while($event = mysqli_fetch_assoc($events)): ?>
                            <option value="<?php echo $event['event_id']; ?>" <?php echo (isset($_GET['event_id']) && $_GET['event_id'] == $event['event_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($event['event_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </form>
            </div>
    
            <h2>Competition Rankings</h2>
            
            <!-- Add Status and Actions Section -->
            <div class="status-panel">
                <?php
                if (!isset($_GET['event_id'])) {
                    echo '<div class="alert alert-warning">No event selected</div>';
                } else {
                    $status_query = "SELECT status FROM event WHERE event_id = ?";
                    $stmt = mysqli_prepare($conn, $status_query);
                    mysqli_stmt_bind_param($stmt, "i", $_GET['event_id']);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $event_data = mysqli_fetch_assoc($result);
                    $event_status = $event_data ? $event_data['status'] : 'unknown';
                    ?>
                    <div class="status-box">
                        <h3 style="color: #66b2ff;">Judging Status: 
                            <span class="status-text"><?php echo ucfirst($event_status); ?></span>
                        </h3>
                        <?php if($event_status != 'finalized'): ?>
                            <form action="finalize_scores.php" method="POST">
                                <button type="submit" class="btn-custom" 
                                        onclick="return confirm('Finalize scores? This cannot be undone!')">
                                    Finalize Scores
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php } ?>
            </div>
                    <table class="scoring-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Candidate Name</th>
                        <th>Judges</th>
                        <th>Criteria & Scores</th>
                        <th>Total Score</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($rankings && mysqli_num_rows($rankings) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($rankings)): ?>
                            <tr>
                                <td><?php echo getRankDisplay($row['overall_rank']); ?></td>
                                <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['judges']); ?></td>
                                <td>
                                    <?php 
                                    $criteria_scores = explode(',', $row['criteria_scores']);
                                    foreach ($criteria_scores as $score) {
                                        echo htmlspecialchars($score) . "<br>";
                                    }
                                    ?>
                                </td>
                                <td class="total-score"><?php echo number_format($row['total_score'], 2); ?></td>
                                <td>
                                    <button onclick="viewDetails(<?php echo $row['candidate_id']; ?>)" class="btn-action">View Details</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="no-data">
                                <?php echo isset($_GET['event_id']) ? 'No scores available for this event.' : 'Please select an event to view rankings.'; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Add this helper function at the top of your file -->
            <?php
            function getRankDisplay($rank) {
                switch($rank) {
                    case 1: return '🥇 1st';
                    case 2: return '🥈 2nd';
                    case 3: return '🥉 3rd';
                    default: return $rank . 'th';
                }
            }
            ?>
            <!-- Update Special Awards Section -->
            <h2 style="margin-top: 40px;">Special Awards Scoring</h2>
    
            <h2>Special Awards</h2>
            <table class="scoring-table">
                <thead>
                    <tr>
                        <th>Award</th>
                        <th>Candidate</th>
                        <th>Judge</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch special awards scores
                    $special_scores_query = "
                        SELECT 
                            sa.award_name,
                            CONCAT(c.first_name, ' ', COALESCE(c.middle_name, ''), ' ', c.last_name) as candidate_name,
                            CONCAT(j.first_name, ' ', j.last_name) as judge_name,
                            sas.points
                        FROM special_award_scores sas
                        JOIN special_awards sa ON sas.award_id = sa.id
                        JOIN candidate c ON sas.candidate_id = c.candidate_id
                        JOIN judges j ON sas.judge_id = j.id
                        WHERE sas.event_id = ?
                        ORDER BY sa.award_name, sas.points DESC";
                    
                    if (isset($_GET['event_id'])) {
                        $stmt = mysqli_prepare($conn, $special_scores_query);
                        mysqli_stmt_bind_param($stmt, "i", $_GET['event_id']);
                        mysqli_stmt_execute($stmt);
                        $special_scores = mysqli_stmt_get_result($stmt);
    
                        while ($score = mysqli_fetch_assoc($special_scores)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($score['award_name']); ?></td>
                                <td><?php echo htmlspecialchars($score['candidate_name']); ?></td>
                                <td><?php echo htmlspecialchars($score['judge_name']); ?></td>
                                <td><?php echo $score['points']; ?></td>
                            </tr>
                        <?php endwhile;
                    } else { ?>
                        <tr>
                            <td colspan="4" class="no-data">Please select an event to view special awards scores.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <br>
            <!-- Add Export Options -->
            <div class="export-options">
                
                <button onclick="exportToTabulator()" class="btn-custom">Send to Tabulator</button>
            </div>
        </div>

        <style>
            /* Add these styles with your existing styles */
            .event-selector {
                margin-bottom: 30px;
            }
    
            .event-select {
                padding: 10px 20px;
                font-size: 1rem;
                border: 2px solid #ddd;
                border-radius: 5px;
                width: 300px;
                cursor: pointer;
                background-color: white;
            }
    
            .event-select:hover {
                border-color: #ff9800;
            }
    
            .alert {
                padding: 15px;
                margin-bottom: 20px;
                border-radius: 5px;
                color: #856404;
                background-color: #fff3cd;
                border: 1px solid #ffeeba;
            }
        </style>
    
        <script>
            function exportToTabulator() {
                if (!confirm('Are you sure you want to send these results to the tabulator?')) {
                    return;
                }
    
                const eventId = document.querySelector('[name="event_id"]').value;
                if (!eventId) {
                    alert('Please select an event first');
                    return;
                }
    
                fetch('send_to_tabulator.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        event_id: eventId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Results successfully sent to tabulator!');
                        location.reload();
                    } else {
                        throw new Error(data.message || 'Failed to send results');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error: ' + error.message);
                });
            }
        </script>
</body>
</html>