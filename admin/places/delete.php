<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

verifyCsrf();

$id = $_POST['id'] ?? null;

if ($id && is_numeric($id)) {
    // Fetch place to get image
    $stmt = $pdo->prepare("SELECT image FROM places WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $place = $stmt->fetch();

    if ($place) {
        // Delete image from disk
        if (!empty($place['image'])) {
            $imagePath = dirname(__DIR__, 2) . UPLOAD_DIR . trim($place['image'], '/');
            if (file_exists($imagePath) && is_file($imagePath)) {
                unlink($imagePath);
            }
        }
        
        // Delete row
        $delStmt = $pdo->prepare("DELETE FROM places WHERE id = :id");
        $delStmt->execute(['id' => $id]);
        
        clearPublicCache();
        flashMessage('success', "Joy muvaffaqiyatli o'chirildi.");
    } else {
        flashMessage('error', "Bunday joy topilmadi.");
    }
} else {
    flashMessage('error', "Noto'g'ri ID.");
}

redirect('index.php');
