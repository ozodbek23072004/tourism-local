<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

require_once '../includes/functions.php';

$token = $_POST['csrf_token'] ?? '';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    header("Location: " . BASE_URL . "admin/index.php");
    exit;
}
verifyCsrf();

logout();
