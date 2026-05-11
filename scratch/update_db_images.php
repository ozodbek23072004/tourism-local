<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

echo "Updating images to real URLs...\n";

$tables = [
    'places' => 'name_uz',
    'people' => 'name_uz',
    'restaurants' => 'name',
    'hotels' => 'name'
];

foreach ($tables as $table => $nameCol) {
    echo "Table: $table\n";
    $stmt = $pdo->query("SELECT id, $nameCol FROM $table");
    $items = $stmt->fetchAll();
    
    foreach ($items as $item) {
        $name = $item[$nameCol];
        $keyword = urlencode(str_replace(' ', ',', $name) . ',uzbekistan');
        $url = "https://loremflickr.com/800/600/{$keyword}/all";
        
        $pdo->prepare("UPDATE $table SET image = ? WHERE id = ?")->execute([$url, $item['id']]);
        echo " - Updated {$item['id']}: $name\n";
    }
}

echo "\nDone!\n";
