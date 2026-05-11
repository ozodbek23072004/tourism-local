<?php
require_once __DIR__ . '/../includes/db.php';

// Create slider images directory
$sliderDir = __DIR__ . '/uploads/sliders/';
if (!is_dir($sliderDir)) {
    mkdir($sliderDir, 0775, true);
    echo "Created directory: $sliderDir\n";
}

// Images to download for each page
$imageGroups = [
    'home' => [
        ['url' => 'https://images.unsplash.com/photo-1548698517-c87d6023cb36?w=1400&q=80', 'name' => 'home_1.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1584888200169-ecb3c29fb1e3?w=1400&q=80', 'name' => 'home_2.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1627998634994-6b9487decf0d?w=1400&q=80', 'name' => 'home_3.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1574345513256-4bdf2380d1da?w=1400&q=80', 'name' => 'home_4.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1598462058448-6a56e7e436f5?w=1400&q=80', 'name' => 'home_5.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1565506085526-787dbf030d35?w=1400&q=80', 'name' => 'home_6.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1602434079836-e82ebda7d079?w=1400&q=80', 'name' => 'home_7.jpg'],
    ],
    'restaurants' => [
        ['url' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=1400&q=80', 'name' => 'rest_1.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?w=1400&q=80', 'name' => 'rest_2.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1400&q=80', 'name' => 'rest_3.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=1400&q=80', 'name' => 'rest_4.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1502301103665-0b95cc738daf?w=1400&q=80', 'name' => 'rest_5.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1400&q=80', 'name' => 'rest_6.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?w=1400&q=80', 'name' => 'rest_7.jpg'],
    ],
    'hotels' => [
        ['url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1400&q=80', 'name' => 'hotel_1.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1400&q=80', 'name' => 'hotel_2.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1551882547-ff43c63faf7c?w=1400&q=80', 'name' => 'hotel_3.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=1400&q=80', 'name' => 'hotel_4.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=1400&q=80', 'name' => 'hotel_5.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=1400&q=80', 'name' => 'hotel_6.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=1400&q=80', 'name' => 'hotel_7.jpg'],
    ],
    'places' => [
        ['url' => 'https://images.unsplash.com/photo-1548698517-c87d6023cb36?w=1400&q=80', 'name' => 'places_1.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1584888200169-ecb3c29fb1e3?w=1400&q=80', 'name' => 'places_2.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1627998634994-6b9487decf0d?w=1400&q=80', 'name' => 'places_3.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1574345513256-4bdf2380d1da?w=1400&q=80', 'name' => 'places_4.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1598462058448-6a56e7e436f5?w=1400&q=80', 'name' => 'places_5.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1565506085526-787dbf030d35?w=1400&q=80', 'name' => 'places_6.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1602434079836-e82ebda7d079?w=1400&q=80', 'name' => 'places_7.jpg'],
    ],
    'people' => [
        ['url' => 'https://images.unsplash.com/photo-1627998634994-6b9487decf0d?w=1400&q=80', 'name' => 'people_1.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1548698517-c87d6023cb36?w=1400&q=80', 'name' => 'people_2.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1584888200169-ecb3c29fb1e3?w=1400&q=80', 'name' => 'people_3.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1574345513256-4bdf2380d1da?w=1400&q=80', 'name' => 'people_4.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1598462058448-6a56e7e436f5?w=1400&q=80', 'name' => 'people_5.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1565506085526-787dbf030d35?w=1400&q=80', 'name' => 'people_6.jpg'],
        ['url' => 'https://images.unsplash.com/photo-1602434079836-e82ebda7d079?w=1400&q=80', 'name' => 'people_7.jpg'],
    ],
];

$downloadedCount = 0;
$failedCount = 0;

// Test connectivity first
echo "<h2>Downloading slider images...</h2>\n";
echo "<pre>\n";

$ctx = stream_context_create([
    'http' => [
        'timeout' => 30,
        'user_agent' => 'Mozilla/5.0 (compatible; SliderDownloader/1.0)',
        'follow_location' => true,
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

$dbUpdates = []; // Collect DB updates

foreach ($imageGroups as $pageKey => $images) {
    echo "\n[$pageKey]\n";
    foreach ($images as $i => $img) {
        $localPath = $sliderDir . $img['name'];
        $webPath = '/public/uploads/sliders/' . $img['name'];

        if (file_exists($localPath) && filesize($localPath) > 1000) {
            echo "  SKIP (exists): {$img['name']}\n";
            $dbUpdates[$pageKey][] = ['path' => $webPath, 'order' => $i];
            $downloadedCount++;
            continue;
        }

        $data = @file_get_contents($img['url'], false, $ctx);
        if ($data !== false && strlen($data) > 1000) {
            file_put_contents($localPath, $data);
            echo "  OK ({$img['name']}): " . round(strlen($data)/1024) . "KB\n";
            $dbUpdates[$pageKey][] = ['path' => $webPath, 'order' => $i];
            $downloadedCount++;
        } else {
            echo "  FAIL: {$img['url']}\n";
            $failedCount++;
            // Keep original URL as fallback
            $dbUpdates[$pageKey][] = ['path' => $img['url'], 'order' => $i];
        }
        
        usleep(200000); // 200ms between requests
    }
}

echo "\n\nUpdating database...\n";

// Clear old records and insert new ones with local paths
$pdo->exec("DELETE FROM hero_sliders");
$stmt = $pdo->prepare("INSERT INTO hero_sliders (page_key, image_path, sort_order) VALUES (?, ?, ?)");
foreach ($dbUpdates as $pageKey => $items) {
    foreach ($items as $item) {
        $stmt->execute([$pageKey, $item['path'], $item['order']]);
    }
}

echo "\nDone! Downloaded: $downloadedCount, Failed: $failedCount\n";
echo "</pre>\n";

if ($failedCount > 0) {
    echo "<p style='color:red'>$failedCount rasm yuklab olinmadi. Internet aloqasini tekshiring.</p>";
} else {
    echo "<p style='color:green'>Barcha rasmlar muvaffaqiyatli yuklandi!</p>";
}
echo "<a href='./index.php'>Bosh sahifaga qaytish</a>";
