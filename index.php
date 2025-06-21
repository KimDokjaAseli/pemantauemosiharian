<?php
require_once 'config/db.php';

// Get all emotions for dashboard
$stmt = $conn->query("SELECT * FROM emotions ORDER BY timestamp DESC LIMIT 7");
$recent_emotions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get activities for recommendations
$stmt = $conn->query("SELECT * FROM activities");
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pemantauan Emosi Harian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="active">
            <div class="sidebar-header">
                <h3>Pemantauan Emosi</h3>
            </div>

            <ul class="list-unstyled components">
                <li class="active">
                    <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li>
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
                    <h4 class="navbar-brand">Dashboard</h4>
                </div>
            </nav>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Grafik Tren Emosi</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="emotionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Emosi Terakhir</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jenis Emosi</th>
                                                <th>Intensitas</th>
                                                <th>Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="recentEmotions">
                                            <?php foreach ($recent_emotions as $emotion): ?>
                                            <tr>
                                                <td><?= date('d M Y', strtotime($emotion['timestamp'])) ?></td>
                                                <td><?= ucfirst($emotion['emotion_type']) ?></td>
                                                <td><?= $emotion['intensity'] ?></td>
                                                <td><?= $emotion['notes'] ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
