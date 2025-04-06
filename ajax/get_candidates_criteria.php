<?php
session_start();
require_once('../includes/db_connection.php');

if (!isset($_GET['event_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$event_id = (int)$_GET['event_id'];
$output = '';

// Get candidates for this event
$candidates_query = "SELECT candidate_id, CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name 
                    FROM candidate 
                    WHERE event_id = ?";
$stmt = mysqli_prepare($conn, $candidates_query);
mysqli_stmt_bind_param($stmt, 'i', $event_id);
mysqli_stmt_execute($stmt);
$candidates = mysqli_stmt_get_result($stmt);

// Get criteria for this event
$criteria_query = "SELECT * FROM criterion WHERE event_id = ? ORDER BY weight DESC";
$stmt = mysqli_prepare($conn, $criteria_query);
mysqli_stmt_bind_param($stmt, 'i', $event_id);
mysqli_stmt_execute($stmt);
$criteria = mysqli_stmt_get_result($stmt);

// Store criteria results in array
$criteria_data = [];
while ($criterion = mysqli_fetch_assoc($criteria)) {
    $criteria_data[] = $criterion;
}

while ($candidate = mysqli_fetch_assoc($candidates)) {
    $output .= "<tr>";
    $output .= "<td>" . htmlspecialchars($candidate['full_name']) . "</td>";
    $output .= "<td class='criteria-cell'>";
    
    foreach ($criteria_data as $criterion) {
        $output .= "<div class='criterion-group'>";
        $output .= "<label>" . htmlspecialchars($criterion['description']) . " (" . $criterion['weight'] . "%)</label>";
        $output .= "<input type='number' name='scores[" . $candidate['candidate_id'] . "][" . $criterion['criterion_id'] . "]' 
                    min='1' max='100' step='0.01' required 
                    class='score-input' />";
        $output .= "</div>";
    }
    
    $output .= "</td>";
    $output .= "</tr>";
}

echo json_encode([
    'success' => true,
    'html' => $output
]);