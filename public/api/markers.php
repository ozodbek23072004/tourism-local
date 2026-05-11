<?php
/**
 * Map Markers API — Barcha obyektlarni xarita uchun JSON formatda qaytaradi
 * GET params: ?types=places,hotels,restaurants (ixtiyoriy)
 */

require_once '../../includes/config.php';
require_once '../../includes/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300'); // 5 daqiqa kesh

$allowedTypes = ['places', 'hotels', 'restaurants'];
$requestedTypes = isset($_GET['types']) 
    ? array_intersect(explode(',', $_GET['types']), $allowedTypes)
    : $allowedTypes;

if (empty($requestedTypes)) {
    $requestedTypes = $allowedTypes;
}

$markers = [];

// Places
if (in_array('places', $requestedTypes)) {
    $stmt = $pdo->query("
        SELECT p.id, p.name_uz, p.name_ru, p.name_en, p.latitude, p.longitude, p.image,
               c.name_uz as category_name, c.icon as category_icon
        FROM places p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'active' AND p.latitude IS NOT NULL AND p.longitude IS NOT NULL
              AND p.latitude != 0 AND p.longitude != 0
    ");
    foreach ($stmt->fetchAll() as $row) {
        $markers[] = [
            'id'       => (int)$row['id'],
            'type'     => 'place',
            'name_uz'  => $row['name_uz'],
            'name_ru'  => $row['name_ru'],
            'name_en'  => $row['name_en'],
            'lat'      => (float)$row['latitude'],
            'lng'      => (float)$row['longitude'],
            'image'    => $row['image'],
            'category' => $row['category_name'],
            'icon'     => $row['category_icon'] ?: 'museum',
            'url'      => 'places/view.php?id=' . $row['id'],
        ];
    }
}

// Hotels
if (in_array('hotels', $requestedTypes)) {
    $stmt = $pdo->query("
        SELECT h.id, h.name_uz, h.name_ru, h.name_en, h.latitude, h.longitude, h.image, h.stars, h.price_from
        FROM hotels h
        WHERE h.status = 'active' AND h.latitude IS NOT NULL AND h.longitude IS NOT NULL
              AND h.latitude != 0 AND h.longitude != 0
    ");
    foreach ($stmt->fetchAll() as $row) {
        $markers[] = [
            'id'       => (int)$row['id'],
            'type'     => 'hotel',
            'name_uz'  => $row['name_uz'],
            'name_ru'  => $row['name_ru'],
            'name_en'  => $row['name_en'],
            'lat'      => (float)$row['latitude'],
            'lng'      => (float)$row['longitude'],
            'image'    => $row['image'],
            'stars'    => (int)$row['stars'],
            'price'    => $row['price_from'] ? (float)$row['price_from'] : null,
            'icon'     => 'hotel',
            'url'      => 'hotels/view.php?id=' . $row['id'],
        ];
    }
}

// Restaurants
if (in_array('restaurants', $requestedTypes)) {
    $stmt = $pdo->query("
        SELECT r.id, r.name_uz, r.name_ru, r.name_en, r.latitude, r.longitude, r.image, r.cuisine_type, r.price_range
        FROM restaurants r
        WHERE r.status = 'active' AND r.latitude IS NOT NULL AND r.longitude IS NOT NULL
              AND r.latitude != 0 AND r.longitude != 0
    ");
    foreach ($stmt->fetchAll() as $row) {
        $markers[] = [
            'id'       => (int)$row['id'],
            'type'     => 'restaurant',
            'name_uz'  => $row['name_uz'],
            'name_ru'  => $row['name_ru'],
            'name_en'  => $row['name_en'],
            'lat'      => (float)$row['latitude'],
            'lng'      => (float)$row['longitude'],
            'image'    => $row['image'],
            'cuisine'  => $row['cuisine_type'],
            'price'    => $row['price_range'],
            'icon'     => 'restaurant',
            'url'      => 'restaurants/view.php?id=' . $row['id'],
        ];
    }
}

echo json_encode($markers, JSON_UNESCAPED_UNICODE);
