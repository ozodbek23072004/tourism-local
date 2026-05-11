<?php
require_once __DIR__ . '/../includes/db.php';
$rows = $pdo->query("SELECT * FROM hero_sliders")->fetchAll();
echo "Count: " . count($rows) . "\n";
foreach($rows as $r) {
    echo $r['page_key'] . ": " . $r['image_path'] . "\n";
}
