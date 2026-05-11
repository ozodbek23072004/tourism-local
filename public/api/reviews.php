<?php
/**
 * API: Reviews — Submit and fetch reviews
 */
require_once '../../includes/config.php';
require_once '../../includes/db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// GET — Fetch reviews for an entity
if ($method === 'GET') {
    $type = $_GET['type'] ?? '';
    $id = (int)($_GET['id'] ?? 0);
    
    $allowed = ['place', 'hotel', 'restaurant', 'person'];
    if (!in_array($type, $allowed) || $id <= 0) {
        echo json_encode(['error' => 'Invalid parameters']);
        exit;
    }
    
    $stmt = $pdo->prepare("
        SELECT id, author_name, rating, comment, created_at 
        FROM reviews 
        WHERE entity_type = :type AND entity_id = :id AND status = 'approved'
        ORDER BY created_at DESC
        LIMIT 50
    ");
    $stmt->execute(['type' => $type, 'id' => $id]);
    $reviews = $stmt->fetchAll();
    
    // Get average rating
    $stmtAvg = $pdo->prepare("
        SELECT AVG(rating) as avg_rating, COUNT(*) as total 
        FROM reviews 
        WHERE entity_type = :type AND entity_id = :id AND status = 'approved'
    ");
    $stmtAvg->execute(['type' => $type, 'id' => $id]);
    $stats = $stmtAvg->fetch();
    
    echo json_encode([
        'reviews' => $reviews,
        'avg_rating' => round((float)($stats['avg_rating'] ?? 0), 1),
        'total' => (int)($stats['total'] ?? 0)
    ]);
    exit;
}

// POST — Submit a new review
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $type = $data['type'] ?? '';
    $id = (int)($data['id'] ?? 0);
    $name = trim($data['name'] ?? 'Mehmon');
    $rating = max(1, min(5, (int)($data['rating'] ?? 5)));
    $comment = trim($data['comment'] ?? '');
    
    $allowed = ['place', 'hotel', 'restaurant', 'person'];
    if (!in_array($type, $allowed) || $id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid parameters']);
        exit;
    }
    
    if (mb_strlen($comment) < 3) {
        http_response_code(400);
        echo json_encode(['error' => 'Comment too short']);
        exit;
    }
    
    // Rate limiting: max 3 reviews per IP per hour
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $stmtCheck = $pdo->prepare("
        SELECT COUNT(*) FROM reviews 
        WHERE ip_address = :ip AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    $stmtCheck->execute(['ip' => $ip]);
    if ((int)$stmtCheck->fetchColumn() >= 3) {
        http_response_code(429);
        echo json_encode(['error' => 'Too many reviews. Please wait.']);
        exit;
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO reviews (entity_type, entity_id, author_name, rating, comment, ip_address)
        VALUES (:type, :id, :name, :rating, :comment, :ip)
    ");
    $stmt->execute([
        'type' => $type,
        'id' => $id,
        'name' => mb_substr($name, 0, 100) ?: 'Mehmon',
        'rating' => $rating,
        'comment' => mb_substr($comment, 0, 2000),
        'ip' => $ip
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Review submitted for moderation']);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
