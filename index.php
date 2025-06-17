    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemantauan Emosi Harian - Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="sidebar">
        <h2>Pemantauan Emosi</h2>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="record.php">Pencatat Emosi</a></li>
            <li><a href="recommendations.php">Rekomendasi Aktivitas</a></li>
        </ul>
    </div>
<?php
require_once 'config.php';

try {
    // Get emotion records
    $stmt = $pdo->query("SELECT * FROM emotion_records ORDER BY created_at DESC LIMIT 10");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get emotion counts
    $emotionCounts = [
        'senang' => 0,
        'cemas' => 0,
        'marah' => 0,
        'sedih' => 0
    ];

    // Only count emotions if records exist
    if (!empty($records)) {
        foreach ($records as $record) {
            // Ensure emotion_type exists and is valid
            if (isset($record['emotion_type']) && array_key_exists($record['emotion_type'], $emotionCounts)) {
                $emotionCounts[$record['emotion_type']]++;
            }
        }
    }

    // Get recent activities
    $stmt = $pdo->query("SELECT * FROM activity_recommendations ORDER BY created_at DESC LIMIT 5");
    $recentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // If there's an error, set default values
    $records = [];
    $recentActivities = [];
    $emotionCounts = [
        'senang' => 0,
        'cemas' => 0,
        'marah' => 0,
        'sedih' => 0
    ];
}
?>
    <div class="main-content">
        <h1>Dashboard</h1>
        
        <!-- Emotion Trend Chart -->
        <div class="card">
            <h2>Tren Emosi</h2>
            <div class="chart-container">
                <canvas id="emotionChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity Logs -->
        <div class="card">
            <h2>Riwayat Emosi Terbaru</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis Emosi</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?php echo date('d M Y H:i', strtotime($record['created_at'])); ?></td>
                        <td><?php echo ucfirst($record['emotion_type']); ?></td>
                        <td><?php echo $record['description']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Recent Recommendations -->
        <div class="card">
            <h2>Rekomendasi Aktivitas</h2>
            <div id="recommendation-list"></div>
        </div>
    </div>

    <script src="assets/js/recommendations.js"></script>
    <script>
        // Initialize emotion chart
        const ctx = document.getElementById('emotionChart').getContext('2d');
        const emotionChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Senang', 'Cemas', 'Marah', 'Sedih'],
                datasets: [{
                    label: 'Jumlah Emosi',
                    data: [
                        <?php echo $emotionCounts['senang']; ?>,
                        <?php echo $emotionCounts['cemas']; ?>,
                        <?php echo $emotionCounts['marah']; ?>,
                        <?php echo $emotionCounts['sedih']; ?>
                    ],
                    backgroundColor: [
                        '#4CAF50',
                        '#FFC107',
                        '#FF5722',
                        '#2196F3'
                    ]
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Initialize recommendations
        document.addEventListener('DOMContentLoaded', () => {
            // Show recommendations for the most common emotion
            const mostCommonEmotion = Object.entries(<?php echo json_encode($emotionCounts); ?>)
                .reduce((a, b) => a[1] > b[1] ? a : b)[0];
            
            // If no emotions recorded yet, show recommendations for 'senang'
            if (mostCommonEmotion === undefined) {
                showRecommendations('senang', 'recommendation-list');
            } else {
                showRecommendations(mostCommonEmotion, 'recommendation-list');
            }
        });
    </script>
</body>
</html>
