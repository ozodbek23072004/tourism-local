<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';

echo "=== Places images ===\n";
$rows = $pdo->query("SELECT id, name_uz, image FROM places LIMIT 5")->fetchAll();
foreach($rows as $r) {
    $path = trim($r['image'] ?? '', '/');
    $exists = $path && file_exists(dirname(__DIR__) . '/' . $path) ? 'EXISTS' : 'MISSING';
    echo "{$r['id']} | {$r['name_uz']} | [{$path}] => {$exists}\n";
}

echo "\n=== People images ===\n";
$rows = $pdo->query("SELECT id, name_uz, image FROM people LIMIT 5")->fetchAll();
foreach($rows as $r) {
    $path = trim($r['image'] ?? '', '/');
    $exists = $path && file_exists(dirname(__DIR__) . '/' . $path) ? 'EXISTS' : 'MISSING';
    echo "{$r['id']} | {$r['name_uz']} | [{$path}] => {$exists}\n";
}

echo "\n=== Hotels images ===\n";
$rows = $pdo->query("SELECT id, name_uz, image FROM hotels LIMIT 3")->fetchAll();
foreach($rows as $r) {
    $path = trim($r['image'] ?? '', '/');
    $exists = $path && file_exists(dirname(__DIR__) . '/' . $path) ? 'EXISTS' : 'MISSING';
    echo "{$r['id']} | {$r['name_uz']} | [{$path}] => {$exists}\n";
}

echo "\n=== docRoot: " . dirname(__DIR__) . " ===\n";
echo "=== placeholder exists: " . (file_exists(dirname(__DIR__) . '/assets/placeholder.jpg') ? 'YES' : 'NO') . " ===\n";
