<?php
require_once('../includes/db_connection.php');

header('Content-Type: application/json');

if (!isset($_GET['event_id'])) {
    echo json_encode([]);
    exit;
}

$event_id = mysqli_real_escape_string($conn, $_GET['event_id']);
$query = "SELECT * FROM special_awards WHERE event_id = ? AND status = 'active' ORDER BY id DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$awards = [];
while ($row = mysqli_fetch_assoc($result)) {
    $awards[] = [
        'id' => $row['id'],
        'award_name' => $row['award_name'],
        'description' => $row['description'],
        'event_id' => $row['event_id']
    ];
}

echo json_encode($awards);
?>