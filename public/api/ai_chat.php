<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/ai.php';
require_once '../includes/lang.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => true, 'message' => 'Invalid request']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$query = $data['query'] ?? '';
$lang = currentLang();

if (empty(trim($query))) {
    echo json_encode(['error' => true, 'message' => 'Savol bo\'sh bo\'lishi mumkin emas.']);
    exit;
}

$response = generateAITourGuide($pdo, $query, $lang);

if ($response['error']) {
    echo json_encode(['error' => true, 'message' => $response['message']]);
} else {
    echo json_encode(['error' => false, 'reply' => $response['content']]);
}
