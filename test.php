<?php
require_once 'config.php';

try {
    // Test connection
    $stmt = $pdo->query("SELECT 1");
    echo "Database connection successful!";
    
    // Test table existence
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "\nTables in database:\n";
    print_r($tables);
    
    // Test inserting a record
    $stmt = $pdo->prepare("INSERT INTO emotion_records (emotion_type, description) VALUES (?, ?)");
    $stmt->execute(['senang', 'Testing connection']);
    echo "\nTest record inserted successfully!";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
