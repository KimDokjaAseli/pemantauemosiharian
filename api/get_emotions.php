<?php
require_once '../config/db.php';

header('Content-Type: application/json');

$stmt = $conn->query("SELECT DATE_FORMAT(timestamp, '%Y-%m-%d') as date, intensity FROM emotions ORDER BY timestamp DESC LIMIT 7");
$emotions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Reverse array to show oldest first
$emotions = array_reverse($emotions);

echo json_encode($emotions);
?>
