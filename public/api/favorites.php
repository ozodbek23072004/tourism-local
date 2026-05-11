<?php
/**
 * API: Favorites — Toggle favorite status
 */
require_once '../../includes/config.php';
require_once '../../includes/db.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$sessionId = session_id();

$method = $_SERVER['REQUEST_METHOD'];

// GET — Check favorite status or list all favorites
if ($method === 'GET') {
    $type = $_GET['type'] ?? '';
    $id = (int)($_GET['id'] ?? 0);
    
    // If type and id provided, check single item
    if ($type && $id > 0) {
        $allowed = ['place', 'hotel', 'restaurant', 'person'];
        if (!in_array($type, $allowed)) {
            echo json_encode(['is_favorite' => false]);
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM favorites WHERE session_id = :sid AND entity_type = :type AND entity_id = :id");
        $stmt->execute(['sid' => $sessionId, 'type' => $type, 'id' => $id]);
        echo json_encode(['is_favorite' => (int)$stmt->fetchColumn() > 0]);
        exit;
    }
    
    // Otherwise list all favorites
    $favorites = [];
    
    // Places
    $stmt = $pdo->prepare("
        SELECT p.id, p.name_uz as name, p.image, 'place' as type, 'places/view.php?id=' as link
        FROM favorites f JOIN places p ON f.entity_id = p.id
        WHERE f.session_id = :sid AND f.entity_type = 'place' AND p.status = 'active'
        ORDER BY f.created_at DESC
    ");
    $stmt->execute(['sid' => $sessionId]);
    $favorites = array_merge($favorites, $stmt->fetchAll());
    
    // People
    $stmt = $pdo->prepare("
        SELECT p.id, p.name_uz as name, p.image, 'person' as type, 'people/view.php?id=' as link
        FROM favorites f JOIN people p ON f.entity_id = p.id
        WHERE f.session_id = :sid AND f.entity_type = 'person' AND p.status = 'active'
        ORDER BY f.created_at DESC
    ");
    $stmt->execute(['sid' => $sessionId]);
    $favorites = array_merge($favorites, $stmt->fetchAll());
    
    // Hotels
    $stmt = $pdo->prepare("
        SELECT h.id, COALESCE(h.name_uz, h.name) as name, h.image, 'hotel' as type, 'hotels/view.php?id=' as link
        FROM favorites f JOIN hotels h ON f.entity_id = h.id
        WHERE f.session_id = :sid AND f.entity_type = 'hotel' AND h.status = 'active'
        ORDER BY f.created_at DESC
    ");
    $stmt->execute(['sid' => $sessionId]);
    $favorites = array_merge($favorites, $stmt->fetchAll());
    
    // Restaurants
    $stmt = $pdo->prepare("
        SELECT r.id, r.name as name, r.image_path as image, 'restaurant' as type, 'restaurants/view.php?id=' as link
        FROM favorites f JOIN restaurants r ON f.entity_id = r.id
        WHERE f.session_id = :sid AND f.entity_type = 'restaurant'
        ORDER BY f.created_at DESC
    ");
    $stmt->execute(['sid' => $sessionId]);
    $favorites = array_merge($favorites, $stmt->fetchAll());
    
    echo json_encode(['favorites' => $favorites, 'count' => count($favorites)]);
    exit;
}

// POST — Toggle favorite
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $type = $data['type'] ?? '';
    $id = (int)($data['id'] ?? 0);
    
    $allowed = ['place', 'hotel', 'restaurant', 'person'];
    if (!in_array($type, $allowed) || $id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid parameters']);
        exit;
    }
    
    // Check if already favorited
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE session_id = :sid AND entity_type = :type AND entity_id = :id");
    $stmt->execute(['sid' => $sessionId, 'type' => $type, 'id' => $id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Remove
        $pdo->prepare("DELETE FROM favorites WHERE id = :id")->execute(['id' => $existing['id']]);
        echo json_encode(['is_favorite' => false, 'action' => 'removed']);
    } else {
        // Add
        $pdo->prepare("INSERT INTO favorites (session_id, entity_type, entity_id) VALUES (:sid, :type, :id)")
            ->execute(['sid' => $sessionId, 'type' => $type, 'id' => $id]);
        echo json_encode(['is_favorite' => true, 'action' => 'added']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
