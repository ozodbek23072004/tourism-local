<?php

require_once 'config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Log error internally if needed, but show clean message to user
    error_log("Database Connection Error: " . $e->getMessage());
    die("Xatolik: Ma'lumotlar bazasiga ulanishda xatolik yuz berdi. Iltimos, keyinroq qayta urinib ko'ring.");
}
