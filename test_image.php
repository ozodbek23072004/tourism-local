<?php
// Test script — /test_image.php orqali ochish
define('BASE_URL', 'http://tourism.local/');
define('UPLOAD_DIR', '/uploads/');

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/public/includes/image.php';

$testPaths = [
    'registan.png',
    'places/10/img_69f9f44cd68d84.03500221.webp',
    'amirtemur.png',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/test.jpg',
];

echo '<h2>publicImage() test</h2><table border=1 cellpadding=5>';
echo '<tr><th>Input path</th><th>Output URL</th><th>Fayl mavjud?</th></tr>';

foreach ($testPaths as $p) {
    $url = publicImage($p, 'test');
    $localPath = __DIR__ . '/public/uploads/' . ltrim($p, '/');
    $exists = file_exists($localPath) ? '✅ HA' : '❌ YO\'Q';
    echo "<tr><td>" . htmlspecialchars($p) . "</td><td><a href='$url' target='_blank'>" . htmlspecialchars($url) . "</a></td><td>$exists</td></tr>";
}
echo '</table>';
