<?php
require_once 'config/db.php';

// Get all activities
$stmt = $conn->query("SELECT * FROM activities");
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekomendasi - Pemantauan Emosi Harian</title>
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
                <li>
                    <a href="record.php"><i class="fas fa-pen"></i> Pencatat Emosi</a>
                </li>
                <li class="active">
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
                    <h4 class="navbar-brand">Rekomendasi</h4>
                </div>
            </nav>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <button class="btn btn-primary w-100 emotion-btn" data-emotion="senang">
                            <i class="fas fa-smile"></i> Senang
                        </button>
                    </div>
                    <div class="col-md-3 mb-4">
                        <button class="btn btn-warning w-100 emotion-btn" data-emotion="cemas">
                            <i class="fas fa-frown"></i> Cemas
                        </button>
                    </div>
                    <div class="col-md-3 mb-4">
                        <button class="btn btn-danger w-100 emotion-btn" data-emotion="marah">
                            <i class="fas fa-angry"></i> Marah
                        </button>
                    </div>
                    <div class="col-md-3 mb-4">
                        <button class="btn btn-info w-100 emotion-btn" data-emotion="sedih">
                            <i class="fas fa-sad-cry"></i> Sedih
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Aktivitas Rekomendasi</h5>
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
    <script src="assets/js/app.js"></script>
    <script>
        // Initialize activities display
        loadActivities('senang');

        // Handle emotion button clicks
        document.querySelectorAll('.emotion-btn').forEach(button => {
            button.addEventListener('click', function() {
                const emotion = this.getAttribute('data-emotion');
                loadActivities(emotion);
            });
        });

        function loadActivities(emotion) {
            $.ajax({
                url: 'api/get_activities.php',
                method: 'POST',
                data: { emotion_type: emotion },
                success: function(response) {
                    $('#activityList').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading activities:', error);
                    $('#activityList').html('<div class="alert alert-danger">Gagal memuat rekomendasi aktivitas. Silakan coba lagi.</div>');
                }
            });
        }
    </script>            $.ajax({
                url: 'api/get_activities.php',
                method: 'POST',
                data: { emotion_type: emotion },
                success: function(response) {
                    $('#activityList').html(response);
                }
            });
        }
    </script>
</body>
</html>
