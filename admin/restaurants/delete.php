<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    verifyCsrf();
    $stmt = $pdo->prepare("SELECT image FROM restaurants WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $row = $stmt->fetch();
    if ($row) {
        if ($row['image'] && file_exists(dirname(__DIR__, 2) . UPLOAD_DIR . trim($row['image'], '/'))) {
            unlink(dirname(__DIR__, 2) . UPLOAD_DIR . trim($row['image'], '/'));
        }
        $pdo->prepare("DELETE FROM restaurants WHERE id = ?")->execute([$_POST['id']]);
        flashMessage('success', "O'chirildi!");
    }
}
redirect('index.php');
