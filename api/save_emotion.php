<?php
require_once '../config/db.php';

header('Content-Type: application/json');

// Get POST data
$emotion_type = $_POST['emotion_type'];
$intensity = $_POST['intensity'];
$notes = $_POST['notes'];

try {
    // Insert emotion
    $stmt = $conn->prepare("INSERT INTO emotions (emotion_type, intensity, notes) VALUES (?, ?, ?)");
    $stmt->execute([$emotion_type, $intensity, $notes]);
    
    // Get last inserted ID
    $emotion_id = $conn->lastInsertId();
    
    // Get activities for this emotion
    $stmt = $conn->prepare("SELECT * FROM activities WHERE emotion_type = ?");
    $stmt->execute([$emotion_type]);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'emotion_id' => $emotion_id,
        'activities' => $activities
    ]);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menyimpan emosi: ' . $e->getMessage()
    ]);
}
?>
