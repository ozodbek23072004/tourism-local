<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$sql = "
DROP TABLE IF EXISTS restaurants;

CREATE TABLE IF NOT EXISTS restaurants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  cuisine_type VARCHAR(100),
  price_level ENUM('low','mid','high') DEFAULT 'mid',
  price_level_num TINYINT DEFAULT 2,
  rating DECIMAL(2,1) DEFAULT 0.0,
  image_path VARCHAR(500),
  working_hours VARCHAR(100),
  phone VARCHAR(50),
  address TEXT,
  region_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (region_id) REFERENCES regions(id)
);

INSERT INTO restaurants (name, description, cuisine_type, price_level, price_level_num, rating, working_hours, region_id) VALUES
('Samarqand Uyi', 'Milliy taomlar', 'O\'zbek', 'mid', 2, 4.8, '09:00-23:00', 4),
('Registon Choyxona', 'An\'anaviy choyxona', 'O\'zbek', 'low', 1, 4.5, '08:00-22:00', 4),
('Buxoro Oshxonasi', 'Buxoro milliy taomlari', 'O\'zbek', 'mid', 2, 4.7, '10:00-22:00', 2);
";

try {
    $pdo->exec($sql);
    echo "SQL executed successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
