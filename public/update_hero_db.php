<?php
require_once __DIR__ . '/../includes/db.php';

$pdo->exec("DELETE FROM hero_sliders");

$data = [
    'home' => ['home_1.jpg','home_2.jpg','home_3.jpg','home_4.jpg','home_5.jpg','home_6.jpg','home_7.jpg'],
    'restaurants' => ['rest_1.jpg','rest_2.jpg','rest_3.jpg','rest_4.jpg','rest_5.jpg','rest_6.jpg','rest_7.jpg'],
    'hotels' => ['hotel_1.jpg','hotel_2.jpg','hotel_3.jpg','hotel_4.jpg','hotel_5.jpg','hotel_6.jpg','hotel_7.jpg'],
    'places' => ['places_1.jpg','places_2.jpg','places_3.jpg','places_4.jpg','places_5.jpg','places_6.jpg','places_7.jpg'],
    'people' => ['people_1.jpg','people_2.jpg','people_3.jpg','people_4.jpg','people_5.jpg','people_6.jpg','people_7.jpg'],
];

$stmt = $pdo->prepare("INSERT INTO hero_sliders (page_key, image_path, sort_order) VALUES (?, ?, ?)");
$total = 0;
foreach ($data as $key => $files) {
    foreach ($files as $i => $file) {
        $path = '/public/uploads/sliders/' . $file;
        $stmt->execute([$key, $path, $i]);
        $total++;
    }
}

echo "Done! $total records inserted.\n";

// Verify
$rows = $pdo->query("SELECT page_key, image_path FROM hero_sliders ORDER BY page_key, sort_order")->fetchAll();
foreach ($rows as $r) {
    $localFile = __DIR__ . '/uploads/sliders/' . basename($r['image_path']);
    $exists = file_exists($localFile) ? "OK" : "MISSING";
    echo "[{$r['page_key']}] {$r['image_path']} -> $exists\n";
}
