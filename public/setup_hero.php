<?php
require_once __DIR__ . '/../includes/db.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS hero_sliders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        page_key VARCHAR(50) NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Table 'hero_sliders' created successfully!\n";

    // Check if empty, then seed with defaults
    $count = $pdo->query("SELECT COUNT(*) FROM hero_sliders")->fetchColumn();
    if ($count == 0) {
        $defaults = [
            'home' => [
                'https://images.unsplash.com/photo-1548698517-c87d6023cb36?q=80&w=2500',
                'https://images.unsplash.com/photo-1584888200169-ecb3c29fb1e3?q=80&w=2500',
                'https://images.unsplash.com/photo-1627998634994-6b9487decf0d?q=80&w=2500',
                'https://images.unsplash.com/photo-1574345513256-4bdf2380d1da?q=80&w=2500',
                'https://images.unsplash.com/photo-1598462058448-6a56e7e436f5?q=80&w=2500',
                'https://images.unsplash.com/photo-1565506085526-787dbf030d35?q=80&w=2500',
                'https://images.unsplash.com/photo-1602434079836-e82ebda7d079?q=80&w=2500'
            ],
            'restaurants' => [
                'https://images.unsplash.com/photo-1514933651103-005eec06c04b?q=80&w=2500',
                'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?q=80&w=2500',
                'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?q=80&w=2500',
                'https://images.unsplash.com/photo-1559339352-11d035aa65de?q=80&w=2500',
                'https://images.unsplash.com/photo-1502301103665-0b95cc738daf?q=80&w=2500',
                'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=2500',
                'https://images.unsplash.com/photo-1544148103-0773bf10d330?q=80&w=2500'
            ],
            'hotels' => [
                'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2500',
                'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=2500',
                'https://images.unsplash.com/photo-1551882547-ff43c63faf7c?q=80&w=2500',
                'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?q=80&w=2500',
                'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=2500',
                'https://images.unsplash.com/photo-1571896349842-33c89424de2d?q=80&w=2500',
                'https://images.unsplash.com/photo-1582719508461-905c673771fd?q=80&w=2500'
            ],
            'places' => [
                'https://images.unsplash.com/photo-1548698517-c87d6023cb36?q=80&w=2500',
                'https://images.unsplash.com/photo-1584888200169-ecb3c29fb1e3?q=80&w=2500',
                'https://images.unsplash.com/photo-1627998634994-6b9487decf0d?q=80&w=2500',
                'https://images.unsplash.com/photo-1574345513256-4bdf2380d1da?q=80&w=2500',
                'https://images.unsplash.com/photo-1598462058448-6a56e7e436f5?q=80&w=2500',
                'https://images.unsplash.com/photo-1565506085526-787dbf030d35?q=80&w=2500',
                'https://images.unsplash.com/photo-1602434079836-e82ebda7d079?q=80&w=2500'
            ],
            'people' => [
                'https://images.unsplash.com/photo-1627998634994-6b9487decf0d?q=80&w=2500',
                'https://images.unsplash.com/photo-1548698517-c87d6023cb36?q=80&w=2500',
                'https://images.unsplash.com/photo-1584888200169-ecb3c29fb1e3?q=80&w=2500',
                'https://images.unsplash.com/photo-1574345513256-4bdf2380d1da?q=80&w=2500',
                'https://images.unsplash.com/photo-1598462058448-6a56e7e436f5?q=80&w=2500',
                'https://images.unsplash.com/photo-1565506085526-787dbf030d35?q=80&w=2500',
                'https://images.unsplash.com/photo-1602434079836-e82ebda7d079?q=80&w=2500'
            ]
        ];

        $stmt = $pdo->prepare("INSERT INTO hero_sliders (page_key, image_path, sort_order) VALUES (:key, :path, :order)");
        foreach ($defaults as $key => $imgs) {
            foreach ($imgs as $i => $path) {
                $stmt->execute(['key' => $key, 'path' => $path, 'order' => $i]);
            }
        }
        echo "Default slider data seeded!\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
