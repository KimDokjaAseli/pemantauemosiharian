<?php
require_once 'config.php';

// Get emotion from query parameter
$emotion = isset($_GET['emotion']) ? $_GET['emotion'] : null;

// If emotion is provided, return recommendations as JSON
if ($emotion) {
    header('Content-Type: application/json');
    
    // Get recommendations from database
    $stmt = $pdo->prepare("SELECT * FROM activity_recommendations WHERE emotion_type = ?");
    $stmt->execute([$emotion]);
    $recommendations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($recommendations);
    exit;
}

// Otherwise show the full page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemantauan Emosi Harian - Rekomendasi Aktivitas</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Pemantauan Emosi</h2>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="record.php">Pencatat Emosi</a></li>
            <li><a href="recommendations.php" class="active">Rekomendasi Aktivitas</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Rekomendasi Aktivitas</h1>
        
        <div class="card">
            <div class="emotion-selection">
                <div class="emotion-btn" onclick="selectEmotion('senang')">
                    <h3>Senang</h3>
                    <p>Menunjukkan kebahagiaan dan kegembiraan</p>
                </div>
                <div class="emotion-btn" onclick="selectEmotion('cemas')">
                    <h3>Cemas</h3>
                    <p>Merasa khawatir atau tidak tenang</p>
                </div>
                <div class="emotion-btn" onclick="selectEmotion('marah')">
                    <h3>Marah</h3>
                    <p>Merasa kesal atau tidak puas</p>
                </div>
                <div class="emotion-btn" onclick="selectEmotion('sedih')">
                    <h3>Sedih</h3>
                    <p>Merasa sedih atau kecewa</p>
                </div>
            </div>

            <div id="recommendation-container">
                <h3>Rekomendasi Aktivitas:</h3>
                <div id="recommendation-list"></div>
            </div>
        </div>
    </div>

    <script src="assets/js/recommendations.js"></script>
    <script>
        // Initialize page
                    <h3>${rec.activity}</h3>
                    <p>${rec.description}</p>
                `;
                listContainer.appendChild(card);
            });
        }

        // Show recommendations for the first emotion by default
        document.addEventListener('DOMContentLoaded', () => {
            showRecommendations('senang');
        });
    </script>
</body>
</html>
