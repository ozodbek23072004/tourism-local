<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$dir = __DIR__ . '/images/people';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

function makeSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9]+/', '-', $string);
    return trim($string, '-');
}

$stmt = $pdo->query("SELECT id, name_en, image FROM people WHERE image LIKE 'http%'");
$people = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($people as $person) {
    $url = $person['image'];
    $slug = makeSlug($person['name_en'] ?: 'person_' . $person['id']);
    $localPath = "images/people/{$slug}.jpg";
    $fullPath = __DIR__ . '/' . $localPath;
    
    echo "Processing {$person['name_en']}...\n";
    
    // Download image
    $ch = curl_init($url);
    $fp = fopen($fullPath, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    
    if ($statusCode == 200 && filesize($fullPath) > 0) {
        $update = $pdo->prepare("UPDATE people SET image = :img WHERE id = :id");
        $update->execute(['img' => $localPath, 'id' => $person['id']]);
        echo " -> Saved and updated to $localPath\n";
    } else {
        echo " -> Failed to download (Status: $statusCode)\n";
        @unlink($fullPath);
    }
}
echo "Done.\n";
