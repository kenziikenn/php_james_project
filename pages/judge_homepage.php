<?php
session_start();
if (!isset($_SESSION['judge_id'])) {
    header("Location: ../judge-login.php");  // Updated path to match the new login file name
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch criteria
// Initialize variables
$criteria = null;
$rankings = null;

// Only fetch data if an event is selected
if (isset($_SESSION['selected_event_id'])) {
    // Fetch criteria based on event_id
    $criteria_query = "SELECT * FROM criterion WHERE event_id = ? ORDER BY weight DESC";
    $stmt = mysqli_prepare($conn, $criteria_query);
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['selected_event_id']);
    mysqli_stmt_execute($stmt);
    $criteria = mysqli_stmt_get_result($stmt);

    // Fetch rankings based on event_id
    $rankings_query = "SELECT c.candidate_id as id, 
                             CONCAT(c.first_name, ' ', COALESCE(c.middle_name, ''), ' ', c.last_name) as candidate_name, 
                             COALESCE(SUM(s.score), 0) as total_score 
                      FROM candidate c 
                      LEFT JOIN scores s ON c.candidate_id = s.candidate_id 
                      WHERE c.event_id = ?
                      GROUP BY c.candidate_id, c.first_name, c.middle_name, c.last_name 
                      ORDER BY total_score DESC";
    $stmt = mysqli_prepare($conn, $rankings_query);
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['selected_event_id']);
    mysqli_stmt_execute($stmt);
    $rankings = mysqli_stmt_get_result($stmt);
}
 // After the events query, add this to fetch special awards
 $special_awards_query = "SELECT sa.id, sa.award_name, sa.description, e.event_name 
 FROM special_awards sa
 INNER JOIN event e ON sa.event_id = e.event_id
 WHERE e.status = 'active' 
 AND sa.event_id = ?";
 
 if (isset($_SESSION['selected_event_id'])) {
     $stmt = mysqli_prepare($conn, $special_awards_query);
     mysqli_stmt_bind_param($stmt, 'i', $_SESSION['selected_event_id']);
     mysqli_stmt_execute($stmt);
     $special_awards = mysqli_stmt_get_result($stmt);
 }
 
 // Add this section after the View Scores Section but before the closing dashboard-grid div


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judge Dashboard</title>
    <link rel="stylesheet" href="/automated/assets/css/judge_dashboard.css?v=<?php echo time(); ?>">
    <script src="../assets/js/judge_dashboard.js"></script>
</head>
<body>
    <div class="navbar">
        <h1>Judge Dashboard</h1>
        <div class="judge-info">
            Welcome, Judge <?php echo htmlspecialchars($_SESSION['judge_name']); ?>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <div class="content">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Event Selection Cards -->
        <div class="section-card">
            <h2 class="section-title">Select An Event to Judge</h2>
            <div class="event-grid">
                <?php
                // Update the events query to show only assigned events
                $events_query = "SELECT e.event_id, e.event_name 
                                FROM event e 
                                INNER JOIN judges j ON e.event_id = j.event_id 
                                WHERE e.status = 'active' 
                                AND j.id = '" . mysqli_real_escape_string($conn, $_SESSION['judge_id']) . "'";
                $events_result = mysqli_query($conn, $events_query);
                
                // Check if judge has any assigned events
                if (mysqli_num_rows($events_result) == 0) {
                    echo '<div class="alert alert-info">You have no assigned events to judge at this time.</div>';
                }
                
                while($event = mysqli_fetch_assoc($events_result)): 
                ?>
                    <div class="event-card" onclick="updateCandidates(<?php echo $event['event_id']; ?>)">
                        <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
                        <div class="event-card-overlay">
                            <span>Click to Start Judging</span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="dashboard-grid" id="judgingContent" style="display: none;">
            <!-- Score Candidates Section -->
            <div class="section-card">
                <h2 class="section-title">Major Awards</h2>
                <div id="successMessage" class="alert alert-success" style="display: none;">
                    Successfully submitted scores!
                </div>
                <form id="scoringForm" onsubmit="submitScores(event)">
                    <input type="hidden" id="selectedEventId" name="event_id" value="<?php echo $_SESSION['selected_event_id']; ?>" data-judge-id="<?php echo $_SESSION['judge_id']; ?>">
                    <input type="hidden" id="selectedCriterionId" name="criterion_id">
                    <input type="hidden" name="judge_id" value="<?php echo $_SESSION['judge_id']; ?>">
                    <div class="scoring-table-container">
                        <table class="scoring-table">
                            <thead>
                                <tr>
                                    <th>Contestant Name</th>
                                    <th>
                                        <select id="criteriaSelect" onchange="updateCriteriaScores(this.value)" class="criteria-header-select">
                                            <option value="">Select Criteria</option>
                                            <?php
                                            if (isset($_SESSION['selected_event_id'])) {
                                                mysqli_data_seek($criteria, 0);
                                                while ($criterion = mysqli_fetch_assoc($criteria)) {
                                                    echo "<option value='" . $criterion['criterion_id'] . "'>" 
                                                        . htmlspecialchars($criterion['description']) 
                                                        . " (" . $criterion['weight'] . "%)</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="scoringTableBody">
                                <?php
                                if (isset($_SESSION['selected_event_id'])) {
                                    // Get candidates for this event
                                    $candidates_query = "SELECT candidate_id, CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name 
                                               FROM candidate 
                                               WHERE event_id = ?";
                                    $stmt = mysqli_prepare($conn, $candidates_query);
                                    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['selected_event_id']);
                                    mysqli_stmt_execute($stmt);
                                    $candidates = mysqli_stmt_get_result($stmt);
                                
                                    while ($candidate = mysqli_fetch_assoc($candidates)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($candidate['full_name']) . "</td>";
                                        echo "<td class='criteria-cell'>";
                                        echo "<input type='number' name='scores[" . $candidate['candidate_id'] . "]' 
                                              min='1' max='100' step='0.01' required 
                                              class='score-input' placeholder='Enter score'>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn-custom">Submit Scores</button>
                    
                    <button type="button" class="btn-custom" onclick="viewCurrentScores()">View Current Scores</button>
                    
                
                    <div id="currentScoresModal" class="modal">
                        <div class="modal-content">
                            <span class="close" onclick="closeCurrentScoresModal()">&times;</span>
                            <h2>Current Assigned Scores</h2>
                            <div id="currentScoresContent">
                                <table class="scores-table">
                                    <thead>
                                        <tr>
                                            <th>Contestant</th>
                                            <th>Score</th>
                                        </tr>
                                    </thead>
                                    <tbody id="scoresList">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
    
                    <script>
                    function viewCurrentScores() {
                        const criterionId = document.getElementById('criteriaSelect').value;
                        const eventId = document.getElementById('selectedEventId').value;
                        const judgeId = <?php echo $_SESSION['judge_id']; ?>;
                    
                        if (!criterionId) {
                            alert('Please select a criteria first');
                            return;
                        }
                    
                        fetch('fetch_scores.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `criterion_id=${criterionId}&event_id=${eventId}&judge_id=${judgeId}`
                        })
                        .then(response => response.json())
                        .then(scores => {
                            const tbody = document.getElementById('scoresList');
                            tbody.innerHTML = '';
                            
                            scores.forEach(score => {
                                const row = `<tr>
                                    <td>${score.candidate_name}</td>
                                    <td>${score.score || 'Not scored yet'}</td>
                                </tr>`;
                                tbody.innerHTML += row;
                            });
                            
                            document.getElementById('currentScoresModal').style.display = 'block';
                        });
                    }
                    
                    function closeCurrentScoresModal() {
                        document.getElementById('currentScoresModal').style.display = 'none';
                    }
                    </script>
                </form>
            </div>
            
    

        <!-- Score Details Modal -->
        <div id="scoreDetailsModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Score Details</h2>
                <div id="scoreDetailsContent"></div>
            </div>
        </div>

        <!-- Special Awards Section -->
        <div class="section-card" id="specialAwardsSection">
            <h2 class="section-title">Special Awards</h2>
            <div class="scoring-table-container">
                <form id="specialAwardsForm" action="save_special_award.php" method="POST">
                    <input type="hidden" name="judge_id" value="<?php echo $_SESSION['judge_id']; ?>">
                    <input type="hidden" name="event_id" value="<?php echo $_SESSION['selected_event_id']; ?>">
                    <table class="scoring-table">
                        <thead>
                            <tr>
                                <th>Contestant Name</th>
                                <th>
                                    <select id="specialAwardSelect" name="award_id" required class="criteria-header-select" onchange="updateSpecialAwardSelection(this.value)">
                                        <option value="">Select Special Award</option>
                                        <?php
                                        $special_awards_query = "SELECT id, award_name FROM special_awards WHERE event_id = ?";
                                        $stmt = mysqli_prepare($conn, $special_awards_query);
                                        mysqli_stmt_bind_param($stmt, 'i', $_SESSION['selected_event_id']);
                                        mysqli_stmt_execute($stmt);
                                        $special_awards = mysqli_stmt_get_result($stmt);
                                        
                                        while ($award = mysqli_fetch_assoc($special_awards)) {
                                            echo "<option value='" . $award['id'] . "'>" 
                                                . htmlspecialchars($award['award_name']) 
                                                . "</option>";
                                        }
                                        ?>
                                    </select>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="specialAwardsTableBody">
                            <?php
                            if (isset($_SESSION['selected_event_id'])) {
                                $candidates_query = "SELECT candidate_id, CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name 
                                           FROM candidate 
                                           WHERE event_id = ?";
                                $stmt = mysqli_prepare($conn, $candidates_query);
                                mysqli_stmt_bind_param($stmt, 'i', $_SESSION['selected_event_id']);
                                mysqli_stmt_execute($stmt);
                                $candidates = mysqli_stmt_get_result($stmt);
                                
                                while ($candidate = mysqli_fetch_assoc($candidates)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($candidate['full_name']) . "</td>";
                                    echo "<td class='award-cell'>";
                                    echo "<input type='number' name='award_points[" . $candidate['candidate_id'] . "]' 
                                          min='1' max='10' step='1'
                                          class='score-input' placeholder='Points (1-10)'
                                          oninput='validatePoints(this)'>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <button type="submit" class="btn-custom">Submit Special Award</button>
                </form>
            </div>
        </div>
        <button type="button" class="btn-custom" onclick="viewSpecialAwardScores()">View Current Scores</button>
    
        <div id="specialAwardScoresModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeSpecialAwardModal()">&times;</span>
                <h2>Current Special Award Scores</h2>
                <div id="specialAwardScoresContent">
                    <table class="scores-table">
                        <thead>
                            <tr>
                                <th>Contestant</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody id="specialAwardScoresList">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
  
        <script>
        function viewSpecialAwardScores() {
            const awardId = document.getElementById('specialAwardSelect').value;
            const eventId = document.getElementById('selectedEventId').value;
            const judgeId = <?php echo $_SESSION['judge_id']; ?>;
        
            if (!awardId) {
                alert('Please select a special award first');
                return;
            }
        
            fetch('fetch_special_award_scores.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `award_id=${awardId}&event_id=${eventId}&judge_id=${judgeId}`
            })
            .then(response => response.json())
            .then(scores => {
                const tbody = document.getElementById('specialAwardScoresList');
                tbody.innerHTML = '';
                
                scores.forEach(score => {
                    const row = `<tr>
                        <td>${score.candidate_name}</td>
                        <td>${score.score || 'Not scored yet'}</td>
                    </tr>`;
                    tbody.innerHTML += row;
                });
                
                document.getElementById('specialAwardScoresModal').style.display = 'block';
            });
        }
        
        function closeSpecialAwardModal() {
            document.getElementById('specialAwardScoresModal').style.display = 'none';
        }
        </script>
        
        <script>
                function submitScores(event) {
                    event.preventDefault();
                    
                    const form = document.getElementById('scoringForm');
                    const criterionId = document.getElementById('criteriaSelect').value;
                    const eventId = document.getElementById('selectedEventId').value;
                    
                    if (!criterionId) {
                        alert('Please select a criteria first');
                        return;
                    }
                    
                    const formData = new FormData(form);
                    formData.append('criterion_id', criterionId);
                    formData.append('event_id', eventId);
                    
                    fetch('submit_scores.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('successMessage').style.display = 'block';
                            setTimeout(() => {
                                document.getElementById('successMessage').style.display = 'none';
                            }, 3000);
                            // Clear the form inputs
                            const inputs = form.querySelectorAll('input[type="number"]');
                            inputs.forEach(input => input.value = '');
                        } else {
                            throw new Error(data.message || 'Failed to submit scores');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error: ' + error.message);
                    });
                }
            </script>