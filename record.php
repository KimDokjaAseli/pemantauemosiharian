<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emotion_type = $_POST['emotion_type'];
    $intensity = $_POST['intensity'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO emotions (emotion_type, intensity, notes) VALUES (?, ?, ?)");
    $stmt->execute([$emotion_type, $intensity, $notes]);
    
    // Get recommendations for the emotion
    $stmt = $conn->prepare("SELECT * FROM activities WHERE emotion_type = ?");
    $stmt->execute([$emotion_type]);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencatat Emosi - Pemantauan Emosi Harian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>Pemantauan Emosi</h3>
            </div>

            <ul class="list-unstyled components">
                <li>
                    <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li class="active">
                    <a href="record.php"><i class="fas fa-pen"></i> Pencatat Emosi</a>
                </li>
                <li>
                    <a href="recommendations.php"><i class="fas fa-lightbulb"></i> Rekomendasi</a>
                </li>
                <li>
                    <a href="about.php"><i class="fas fa-info-circle"></i> Tentang</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content" class="page-content">
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <h4 class="navbar-brand">Pencatat Emosi</h4>
                </div>
            </nav>

            <div class="container-fluid">
            <div class="container-fluid mt-4">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Catat Emosi Anda</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="" id="emotionForm">
                                    <div class="mb-3">
                                        <label for="emotion_type" class="form-label">Jenis Emosi</label>
                                        <select class="form-select" id="emotion_type" name="emotion_type" required>
                                            <option value="">Pilih jenis emosi</option>
                                            <option value="senang">Senang</option>
                                            <option value="cemas">Cemas</option>
                                            <option value="marah">Marah</option>
                                            <option value="sedih">Sedih</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="intensity" class="form-label">Intensitas Emosi</label>
                                        <input type="range" class="form-range" id="intensity" name="intensity" min="1" max="5" step="1" required>
                                        <div class="text-center mt-2">
                                            <span id="intensityValue">1</span>/5
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Catatan (opsional)</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">Simpan Emosi</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recommendations Section -->
                <div class="row mt-4" id="recommendationsSection" style="display: none;">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Rekomendasi Aktivitas</h5>
                            </div>
                            <div class="card-body">
                                <div id="activityList">
                                    <!-- Activities will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Update intensity value display
        document.getElementById('intensity').addEventListener('input', function() {
            document.getElementById('intensityValue').textContent = this.value;
        });

        // Handle form submission via AJAX
        $('#emotionForm').on('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            // Get form data
            const formData = new FormData(this);
            const emotionType = formData.get('emotion_type');

            // Submit form data
            fetch('api/save_emotion.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Show success message
                alert('Emosi berhasil disimpan!');
                
                // Show recommendations
                $('#recommendationsSection').show();
                
                // Load activities for the selected emotion
                return fetch('api/get_activities.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'emotion_type=' + emotionType
                });
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('activityList').innerHTML = html;
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Simpan Emosi';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan emosi.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Simpan Emosi';
            });
        });
    </script>
</body>
</html>
