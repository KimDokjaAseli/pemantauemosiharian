-- Database schema for Emotion Monitoring Application

CREATE DATABASE IF NOT EXISTS emotion_monitoring;
USE emotion_monitoring;

-- Table for storing user emotions
CREATE TABLE IF NOT EXISTS emotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emotion_type ENUM('senang', 'cemas', 'marah', 'sedih') NOT NULL,
    intensity INT NOT NULL CHECK (intensity BETWEEN 1 AND 5),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT
);

-- Table for storing recommended activities
CREATE TABLE IF NOT EXISTS activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emotion_type ENUM('senang', 'cemas', 'marah', 'sedih') NOT NULL,
    activity_name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert some sample activities
INSERT INTO activities (emotion_type, activity_name, description) VALUES
('cemas', 'Meditasi', 'Lakukan meditasi selama 10 menit untuk menenangkan pikiran'),
('cemas', 'Mendengarkan Musik', 'Dengarkan musik yang menenangkan'),
('marah', 'Olahraga', 'Lakukan olahraga ringan untuk mengalihkan energi negatif'),
('marah', 'Menulis', 'Tuliskan perasaan Anda dalam jurnal'),
('sedih', 'Berbicara dengan Orang Terdekat', 'Bicarakan perasaan Anda dengan orang yang dipercaya'),
('sedih', 'Membaca', 'Baca buku atau artikel yang menginspirasi'),
('senang', 'Berbagi', 'Bagikan kebahagiaan Anda dengan orang lain'),
('senang', 'Mencoba Hal Baru', 'Coba aktivitas baru yang menarik');
