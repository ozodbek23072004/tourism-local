<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');
if (mb_strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$qLike = '%' . $q . '%';
$results = [];

// Places
$stmt = $pdo->prepare("SELECT id, name_uz as name, image, 'Joy' as type FROM places WHERE status = 'active' AND (name_uz LIKE :q1 OR name_ru LIKE :q2 OR name_en LIKE :q3) LIMIT 3");
$stmt->execute(['q1' => $qLike, 'q2' => $qLike, 'q3' => $qLike]);
foreach ($stmt->fetchAll() as $row) {
    $results[] = ['name' => $row['name'], 'type' => $row['type'], 'image' => publicImage($row['image'], $row['name']), 'url' => BASE_URL . 'public/places/view.php?id=' . $row['id']];
}

// People
$stmt = $pdo->prepare("SELECT id, name_uz as name, image, 'Shaxs' as type FROM people WHERE status = 'active' AND (name_uz LIKE :q1 OR name_ru LIKE :q2 OR name_en LIKE :q3) LIMIT 2");
$stmt->execute(['q1' => $qLike, 'q2' => $qLike, 'q3' => $qLike]);
foreach ($stmt->fetchAll() as $row) {
    $results[] = ['name' => $row['name'], 'type' => $row['type'], 'image' => publicImage($row['image'], $row['name']), 'url' => BASE_URL . 'public/people/view.php?id=' . $row['id']];
}

// Restaurants
$stmt = $pdo->prepare("SELECT id, name_uz as name, image, 'Restoran' as type FROM restaurants WHERE status = 'active' AND (name_uz LIKE :q1 OR COALESCE(name_ru,'') LIKE :q2) LIMIT 2");
$stmt->execute(['q1' => $qLike, 'q2' => $qLike]);
foreach ($stmt->fetchAll() as $row) {
    $results[] = ['name' => $row['name'], 'type' => $row['type'], 'image' => publicImage($row['image'], $row['name']), 'url' => BASE_URL . 'public/restaurants/view.php?id=' . $row['id']];
}

// Hotels
$stmt = $pdo->prepare("SELECT id, name_uz as name, image, 'Mehmonxona' as type FROM hotels WHERE status = 'active' AND (name_uz LIKE :q1 OR COALESCE(name_ru,'') LIKE :q2) LIMIT 2");
$stmt->execute(['q1' => $qLike, 'q2' => $qLike]);
foreach ($stmt->fetchAll() as $row) {
    $results[] = ['name' => $row['name'], 'type' => $row['type'], 'image' => publicImage($row['image'], $row['name']), 'url' => BASE_URL . 'public/hotels/view.php?id=' . $row['id']];
}

echo json_encode($results);
