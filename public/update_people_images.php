<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$updates = [
    1 => 'images/people/amir-timur.jpg',
    2 => 'images/people/mirzo-ulugh-beg.jpg',
    3 => 'images/people/avicenna.jpg'
];

foreach ($updates as $id => $path) {
    $stmt = $pdo->prepare("UPDATE people SET image = :img WHERE id = :id");
    $stmt->execute(['img' => $path, 'id' => $id]);
    echo "Updated person $id with image $path\n";
}
