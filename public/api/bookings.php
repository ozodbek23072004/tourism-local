<?php
/**
 * Bookings API — Bron qilish endpoint
 * POST: Yangi bron yaratish
 * GET:  Admin uchun bronlarni olish
 */

require_once '../../includes/config.php';
require_once '../../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

// ── POST: Yangi bron yaratish ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
        exit;
    }
    
    $entityType = $input['entity_type'] ?? '';
    $entityId   = (int)($input['entity_id'] ?? 0);
    $guestName  = trim($input['guest_name'] ?? '');
    $guestPhone = trim($input['guest_phone'] ?? '');
    $guestEmail = trim($input['guest_email'] ?? '');
    $checkIn    = $input['check_in'] ?? '';
    $checkOut   = $input['check_out'] ?? null;
    $guestsCount = (int)($input['guests_count'] ?? 1);
    $message    = trim($input['message'] ?? '');
    
    // Validatsiya
    $errors = [];
    
    if (!in_array($entityType, ['hotel', 'restaurant'])) {
        $errors[] = 'entity_type noto\'g\'ri';
    }
    if ($entityId < 1) {
        $errors[] = 'entity_id noto\'g\'ri';
    }
    if (mb_strlen($guestName) < 2 || mb_strlen($guestName) > 100) {
        $errors[] = 'Ismingizni kiriting (2-100 belgi)';
    }
    if (!preg_match('/^\+?[\d\s\-()]{7,20}$/', $guestPhone)) {
        $errors[] = 'Telefon raqam noto\'g\'ri';
    }
    if ($guestEmail !== '' && !filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email noto\'g\'ri';
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkIn)) {
        $errors[] = 'Sana noto\'g\'ri';
    } else {
        $checkInDate = new DateTime($checkIn);
        $today = new DateTime('today');
        if ($checkInDate < $today) {
            $errors[] = 'O\'tgan sana tanlanmaydi';
        }
    }
    if ($entityType === 'hotel' && $checkOut) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkOut)) {
            $errors[] = 'Chiqish sanasi noto\'g\'ri';
        } elseif ($checkOut <= $checkIn) {
            $errors[] = 'Chiqish sanasi kirish sanasidan keyin bo\'lishi kerak';
        }
    }
    if ($guestsCount < 1 || $guestsCount > 20) {
        $errors[] = 'Mehmonlar soni 1-20 orasida bo\'lishi kerak';
    }
    
    // Entity mavjudligini tekshirish
    if (empty($errors)) {
        $table = $entityType === 'hotel' ? 'hotels' : 'restaurants';
        $check = $pdo->prepare("SELECT id FROM {$table} WHERE id = :id AND status = 'active'");
        $check->execute(['id' => $entityId]);
        if (!$check->fetch()) {
            $errors[] = 'Tanlangan joy topilmadi';
        }
    }
    
    // Spam himoya: Bitta IP/telefon dan 1 soatda max 3 ta bron
    if (empty($errors)) {
        $spamCheck = $pdo->prepare("
            SELECT COUNT(*) FROM bookings 
            WHERE guest_phone = :phone AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");
        $spamCheck->execute(['phone' => $guestPhone]);
        if ((int)$spamCheck->fetchColumn() >= 3) {
            $errors[] = 'Juda ko\'p so\'rov. Iltimos, biroz kuting.';
        }
    }
    
    if (!empty($errors)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }
    
    // Bazaga saqlash
    $stmt = $pdo->prepare("
        INSERT INTO bookings (entity_type, entity_id, guest_name, guest_phone, guest_email, check_in, check_out, guests_count, message)
        VALUES (:type, :eid, :name, :phone, :email, :cin, :cout, :cnt, :msg)
    ");
    $stmt->execute([
        'type'  => $entityType,
        'eid'   => $entityId,
        'name'  => htmlspecialchars($guestName, ENT_QUOTES, 'UTF-8'),
        'phone' => htmlspecialchars($guestPhone, ENT_QUOTES, 'UTF-8'),
        'email' => $guestEmail ? htmlspecialchars($guestEmail, ENT_QUOTES, 'UTF-8') : null,
        'cin'   => $checkIn,
        'cout'  => ($entityType === 'hotel' && $checkOut) ? $checkOut : null,
        'cnt'   => $guestsCount,
        'msg'   => $message ? htmlspecialchars(mb_substr($message, 0, 500), ENT_QUOTES, 'UTF-8') : null,
    ]);
    
    echo json_encode(['success' => true, 'booking_id' => $pdo->lastInsertId()]);
    exit;
}

// ── GET: Bronlar haqida ma'lumot (faqat soni) ──────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $type = $_GET['type'] ?? '';
    $id   = (int)($_GET['id'] ?? 0);
    
    if (!in_array($type, ['hotel', 'restaurant']) || $id < 1) {
        echo json_encode(['count' => 0]);
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE entity_type = :type AND entity_id = :id AND status != 'cancelled'");
    $stmt->execute(['type' => $type, 'id' => $id]);
    
    echo json_encode(['count' => (int)$stmt->fetchColumn()]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
