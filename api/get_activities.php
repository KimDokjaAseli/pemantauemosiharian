<?php
require_once '../config/db.php';

header('Content-Type: text/html');

$emotion_type = $_POST['emotion_type'];

$stmt = $conn->prepare("SELECT * FROM activities WHERE emotion_type = ?");
$stmt->execute([$emotion_type]);
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($activities)) {
    echo '<div class="activities-list">';
    foreach ($activities as $activity) {
        echo '<div class="activity-card">';
        echo '<h5>' . ucfirst($activity['activity_name']) . '</h5>';
        echo '<p>' . $activity['description'] . '</p>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo '<p>Tidak ada aktivitas tersedia untuk jenis emosi ini.</p>';
}
?>
