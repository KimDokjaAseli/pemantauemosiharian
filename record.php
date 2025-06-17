<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (!isset($_POST['emotion_type']) || !isset($_POST['description']) || 
        !isset($_POST['question1']) || !isset($_POST['question2']) || 
        !isset($_POST['question3'])) {
        die('Form submission error: All fields are required.');
    }

    // Get form data
    $emotion_type = $_POST['emotion_type'];
    $description = $_POST['description'];
    $question1 = $_POST['question1'];
    $question2 = $_POST['question2'];
    $question3 = $_POST['question3'];

    // Validate emotion_type
    if (!in_array($emotion_type, ['senang', 'cemas', 'marah', 'sedih'])) {
        die('Invalid emotion type selected.');
    }

    // Validate intensity (must be between 1-10)
    if (!is_numeric($question2) || $question2 < 1 || $question2 > 10) {
        die('Intensity must be a number between 1 and 10.');
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO emotion_records (emotion_type, description, cause, intensity, coping) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$emotion_type, $description, $question1, $question2, $question3]);
        
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        die('Sorry, there was an error saving your emotion record. Please try again.');
    }
}

// Get all recommendations for AJAX
$stmt = $pdo->query("SELECT * FROM activity_recommendations ORDER BY emotion_type");
$recommendations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemantauan Emosi Harian - Pencatat Emosi</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Pemantauan Emosi</h2>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="record.php" class="active">Pencatat Emosi</a></li>
            <li><a href="recommendations.php">Rekomendasi Aktivitas</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Pencatat Emosi</h1>
        
        <div class="card">
            <form method="POST" action="">
                <div id="form-content">
                    <div class="emotion-selection">
                        <div class="emotion-option">
                            <input type="radio" id="senang" name="emotion_type" value="senang" onchange="selectEmotion('senang')">
                            <label for="senang">
                                <h3>Senang</h3>
                                <p>Menunjukkan kebahagiaan dan kegembiraan</p>
                            </label>
                        </div>
                        <div class="emotion-option">
                            <input type="radio" id="cemas" name="emotion_type" value="cemas" onchange="selectEmotion('cemas')">
                            <label for="cemas">
                                <h3>Cemas</h3>
                                <p>Merasa khawatir atau tidak tenang</p>
                            </label>
                        </div>
                        <div class="emotion-option">
                            <input type="radio" id="marah" name="emotion_type" value="marah" onchange="selectEmotion('marah')">
                            <label for="marah">
                                <h3>Marah</h3>
                                <p>Merasa kesal atau tidak puas</p>
                            </label>
                        </div>
                        <div class="emotion-option">
                            <input type="radio" id="sedih" name="emotion_type" value="sedih" onchange="selectEmotion('sedih')">
                            <label for="sedih">
                                <h3>Sedih</h3>
                                <p>Merasa sedih atau kecewa</p>
                            </label>
                        </div>
                    </div>

                    <div id="form-fields" style="display: none;">
                        <div class="form-group">
                            <label for="description">Deskripsi Emosi:</label>
                            <textarea id="description" name="description" rows="4" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="question1" data-number="1">Apa yang menyebabkan Anda merasakan emosi ini?</label>
                            <textarea id="question1" name="question1" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="question2" data-number="2">Bagaimana tingkat intensitas emosi Anda (1-10)?</label>
                            <input type="number" id="question2" name="question2" min="1" max="10" required>
                        </div>

                        <div class="form-group">
                            <label for="question3" data-number="3">Apa yang telah Anda lakukan untuk mengatasi emosi ini?</label>
                            <textarea id="question3" name="question3" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="submit-btn">Simpan Emosi</button>
                        </div>
                    </div>
                </div>
            </form>

                <div id="recommendation-container" style="display: none;">
                    <h3>Rekomendasi Aktivitas:</h3>
                    <div id="recommendation-list"></div>
                </div>

                <button type="submit" class="btn">Simpan Emosi</button>
            </form>
        </div>
    </div>

    <script src="assets/js/recommendations.js"></script>
    <script>
        // Add smooth scrolling for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        function selectEmotion(type) {
            // Show form fields when emotion is selected
            document.getElementById('form-fields').style.display = 'block';
            document.getElementById('recommendation-container').style.display = 'block';
            showRecommendations(type);
        }

        function showRecommendations(emotion) {
            const container = document.getElementById('recommendation-container');
            if (!container) return;

            container.innerHTML = '';
            
            const recommendations = recommendationsData[emotion] || [];
            
            recommendations.forEach(rec => {
                const card = document.createElement('div');
                card.className = 'activity-card fade-in';
                
                card.innerHTML = `
                    <div class="activity-header">
                        <h4>${rec.activity}</h4>
                        <button class="copy-btn" onclick="copyToClipboard('${rec.activity}')">Copy</button>
                    </div>
                    <p>${rec.description}</p>
                `;
                
                container.appendChild(card);
            });
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text)
                .then(() => {
                    const btn = event.target;
                    btn.textContent = 'Copied!';
                    setTimeout(() => {
                        btn.textContent = 'Copy';
                    }, 2000);
                })
                .catch(err => {
                    console.error('Failed to copy:', err);
                });
        }

        // Initialize recommendations when page loads
        document.addEventListener('DOMContentLoaded', () => {
            // Show recommendations if emotion is in URL
            const urlParams = new URLSearchParams(window.location.search);
            const emotion = urlParams.get('emotion');
            if (emotion) {
                document.getElementById('recommendation-container').style.display = 'block';
                showRecommendations(emotion);
            }

            // Add form validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Check if emotion is selected
                    const selectedEmotion = document.querySelector('input[name="emotion_type"]:checked');
                    if (!selectedEmotion) {
                        alert('Silakan pilih emosi terlebih dahulu');
                        e.preventDefault();
                        return;
                    }

                    // Check if all required fields are filled
                    const requiredFields = form.querySelectorAll('[required]');
                    for (const field of requiredFields) {
                        if (!field.value) {
                            alert('Harap isi semua field yang diperlukan');
                            e.preventDefault();
                            return;
                        }
                    }
                });
            }
        });

        // Add tooltip functionality
        document.addEventListener('DOMContentLoaded', () => {
            const tooltips = document.querySelectorAll('[data-tooltip]');
            tooltips.forEach(tooltip => {
                tooltip.addEventListener('mouseenter', () => {
                    const tooltipText = tooltip.getAttribute('data-tooltip');
                    const tooltipElement = document.createElement('div');
                    tooltipElement.className = 'tooltip';
                    tooltipElement.textContent = tooltipText;
                    document.body.appendChild(tooltipElement);
                    
                    const rect = tooltip.getBoundingClientRect();
                    tooltipElement.style.left = `${rect.left + window.scrollX}px`;
                    tooltipElement.style.top = `${rect.top + window.scrollY - 30}px`;
                });

                tooltip.addEventListener('mouseleave', () => {
                    document.querySelector('.tooltip')?.remove();
                });
            });
        });
    </script>

    <style>
        /* Emotion Selection */
        .emotion-selection {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #34495e;
            border-radius: 15px;
        }

        .emotion-option {
            background: #2c3e50;
            border-radius: 10px;
            padding: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .emotion-option:hover {
            background: #273849;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .emotion-option input[type="radio"] {
            display: none;
        }

        .emotion-option input[type="radio"]:checked + label {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            border: 2px solid #27ae60;
        }

        .emotion-option label {
            display: block;
            padding: 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .emotion-option h3 {
            color: white;
            margin: 0 0 10px 0;
            font-size: 1.2em;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .emotion-option p {
            color: white;
            margin: 0;
            font-size: 0.9em;
            opacity: 1;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Form labels */
        .form-group label {
            display: block;
            margin-bottom: 12px;
            color: white;
            font-weight: 600;
            font-size: 1.2em;
            padding: 5px;
            background: #34495e;
            border-radius: 5px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Add question numbers styling */
        .form-group label::before {
            content: attr(data-number);
            display: inline-block;
            width: 25px;
            height: 25px;
            background: #2ecc71;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 25px;
            margin-right: 10px;
            font-weight: bold;
        }

        /* Input fields */
        .form-group textarea,
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #34495e;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: #2c3e50;
            color: white;
        }

        /* Activity cards text */
        .activity-header h4 {
            margin: 0;
            color: white;
            font-size: 1.1em;
        }

        .activity-card p {
            color: #555;
            margin: 0;
            font-size: 0.95em;
            line-height: 1.5;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group textarea,
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .form-group textarea:focus,
        .form-group input:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.2);
            outline: none;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-group input[type="number"] {
            text-align: center;
        }

        /* Recommendation Container */
        #recommendation-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .activity-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .activity-card:hover {
            transform: translateY(-5px);
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .activity-header h4 {
            margin: 0;
            color: #333;
        }

        .copy-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .copy-btn:hover {
            background: #45a049;
            transform: scale(1.05);
        }

        /* Animation */
        .fade-in {
            opacity: 0;
            transform: translateY(10px);
            animation: fadeInUp 0.5s ease-out forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        .shake {
            animation: shake 0.5s;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        .fade-out {
            animation: fadeOut 0.3s ease-out;
        }
    </style>

    <style>
        .tooltip {
            position: absolute;
            background: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            z-index: 1000;
        }
        
        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .copy-btn {
            background: none;
            border: none;
            color: #4CAF50;
            cursor: pointer;
            font-size: 14px;
            padding: 5px 10px;
            transition: all 0.3s ease;
        }
        
        .copy-btn:hover {
            color: #45a049;
        }
    </style>
    </script>
</body>
</html>
