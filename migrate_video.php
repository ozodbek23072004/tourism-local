<?php
require_once 'includes/db.php';

$tables = ['places', 'people', 'restaurants', 'hotels'];
foreach ($tables as $table) {
    try {
        $pdo->exec("ALTER TABLE $table ADD COLUMN IF NOT EXISTS video_url VARCHAR(255) DEFAULT NULL");
        echo "Table $table updated with video_url\n";
    } catch (PDOException $e) {
        echo "Error updating $table: " . $e->getMessage() . "\n";
    }
}

// Ensure gallery table is ready for all types
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS gallery (
        id INT AUTO_INCREMENT PRIMARY KEY,
        entity_type VARCHAR(50) NOT NULL,
        entity_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Gallery table checked/created.\n";
} catch (PDOException $e) {
    echo "Error with gallery table: " . $e->getMessage() . "\n";
}
