<?php
require_once 'config.php';

try {
    // Create tables
    $sql = "CREATE TABLE IF NOT EXISTS emotion_records (
        id INT AUTO_INCREMENT PRIMARY KEY,
        emotion_type VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS activity_recommendations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        emotion_type VARCHAR(50) NOT NULL,
        activity TEXT NOT NULL,
        description TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Insert sample data
    INSERT INTO activity_recommendations (emotion_type, activity, description) VALUES
    ('senang', 'Bermain musik atau alat musik favorit', 'Meningkatkan mood positif dan kreativitas'),
    ('senang', 'Membaca buku favorit', 'Membuat pikiran lebih tenang dan damai'),
    ('senang', 'Berkebun atau menanam tanaman', 'Membuat suasana hati lebih tenang dan produktif'),
    ('cemas', 'Meditasi atau yoga ringan', 'Membantu menenangkan pikiran dan mengurangi stres'),
    ('cemas', 'Mendengarkan musik relaksasi', 'Membantu menenangkan pikiran dan mengurangi kecemasan'),
    ('cemas', 'Menggambar atau membuat karya seni', 'Membantu mengekspresikan perasaan dan mengurangi kecemasan'),
    ('marah', 'Berolahraga atau berlari', 'Membantu mengeluarkan energi negatif dan menenangkan pikiran'),
    ('marah', 'Mengambil waktu untuk bernafas dalam-dalam', 'Membantu mengontrol emosi dan menenangkan diri'),
    ('marah', 'Menulis jurnal atau mengungkapkan perasaan', 'Membantu mengelola emosi dan mengekspresikan perasaan'),
    ('sedih', 'Bercerita pada teman atau keluarga', 'Membantu mengurangi beban dan merasa didukung'),
    ('sedih', 'Membuat daftar hal-hal yang disyukuri', 'Membantu mengubah fokus dan meningkatkan mood'),
    ('sedih', 'Membuat karya seni atau menulis', 'Membantu mengekspresikan perasaan dan mengurangi sedih');";

    // Execute the SQL
    $pdo->exec($sql);
    echo "Database tables created successfully!";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
