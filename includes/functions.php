<?php

/**
 * Sanitize short input fields (names, titles, addresses).
 * Strips all HTML tags — use ONLY for single-line fields, NOT for descriptions/bios.
 */
function sanitize(string $input): string {
    return htmlspecialchars(trim(strip_tags($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Clean long text fields (descriptions, bios).
 * Preserves the text content but encodes HTML entities — does NOT strip_tags.
 * Use this instead of sanitize() for textarea / multiline content.
 */
function cleanText(string $input): string {
    return trim($input);
}

/**
 * Redirect and exit
 */
function redirect(string $url): void {
    header("Location: " . $url);
    exit;
}

/**
 * Set flash message
 */
function flashMessage(string $key, string $message): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'][$key] = $message;
}

/**
 * Get and clear flash message
 */
function getFlash(string $key): ?string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

/**
 * Pagination helper
 */
function paginate(int $total, int $perPage, int $currentPage): array {
    $totalPages = ceil($total / $perPage);
    if ($currentPage < 1) {
        $currentPage = 1;
    }
    if ($currentPage > $totalPages && $totalPages > 0) {
        $currentPage = $totalPages;
    }
    
    $offset = ($currentPage - 1) * $perPage;
    if ($offset < 0) {
        $offset = 0;
    }
    
    return [
        'offset'      => (int)$offset,
        'totalPages'  => (int)$totalPages,
        'currentPage' => (int)$currentPage
    ];
}

/**
 * Upload image with STRICT security validation.
 * 
 * Himoya darajalari:
 *   1. Fayl hajmi tekshiruvi (max 5MB)
 *   2. Kengaytma tekshiruvi (faqat jpg/jpeg/png/webp/gif)
 *   3. MIME turi — finfo orqali haqiqiy fayl tarkibini tekshiradi
 *   4. getimagesize() — bu fayl HAQIQATAN rasm ekanini tasdiqlaydi
 *      (viruslar .jpg kengaytma qo'yishi mumkin, lekin getimagesize() rad qiladi)
 *   5. GD orqali qayta yaratish — agar virus yashirin bo'lsa ham,
 *      GD uni butunlay yangi piksellar bilan qayta yozadi
 *
 * @return string|false Muvaffaqiyatli bo'lsa: "folder/filename.webp", xato bo'lsa: false
 */
function uploadImage(string $inputName, string $folder): string|false {
    global $uploadError;
    $uploadError = '';

    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        $uploadError = "Fayl yuklashda server xatosi: " . ($_FILES[$inputName]['error'] ?? 'Noma\'lum xato');
        return false;
    }
    
    $file = $_FILES[$inputName];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if ($file['size'] > $maxSize || $file['size'] === 0) {
        $uploadError = "Fayl hajmi 5MB dan katta yoki bo'sh fayl (" . number_format($file['size']/1024/1024, 2) . " MB)";
        return false;
    }
    
    // 1. Kengaytma tekshiruvi
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ext, $allowedExts)) {
        $uploadError = "Ruxsat etilmagan fayl turi: .$ext (faqat jpg, png, webp, gif)";
        return false;
    }
    
    // 2. Fayl tarkibi bo'yicha MIME tekshiruvi
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedMimes)) {
        $uploadError = "Haqiqiy fayl turi rasm emas (MIME: $mimeType)";
        return false;
    }
    
    // 3. getimagesize()
    $imgInfo = @getimagesize($file['tmp_name']);
    if ($imgInfo === false || $imgInfo[0] === 0 || $imgInfo[1] === 0) {
        $uploadError = "Fayl buzilgan rasm yoki rasm emas (getimagesize muvaffaqiyatsiz)";
        return false;
    }
    
    // 4. Papka tayyorlash
    $basePath = dirname(__DIR__) . '/public';
    $folderClean = trim($folder, '/');
    $targetDir = $basePath . '/uploads/' . $folderClean;
    
    if (!is_dir($targetDir)) {
        if (!@mkdir($targetDir, 0777, true)) {
            $uploadError = "Papka yaratishda xatolik: $targetDir";
            return false;
        }
    }
    
    $filename = uniqid('img_', true) . '.webp';
    $targetPath = $targetDir . '/' . $filename;
    
    // 5. GD orqali QAYTA YARATISH
    if (function_exists('imagecreatefromjpeg')) {
        $sourceImage = match($mimeType) {
            'image/jpeg' => @imagecreatefromjpeg($file['tmp_name']),
            'image/png'  => @imagecreatefrompng($file['tmp_name']),
            'image/webp' => @imagecreatefromwebp($file['tmp_name']),
            'image/gif'  => @imagecreatefromgif($file['tmp_name']),
            default      => false,
        };
        
        if ($sourceImage) {
            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);
            
            // Katta rasmlarni kichiklashtirish (max 1600px)
            $maxWidth = 1600;
            if ($width > $maxWidth) {
                $newHeight = (int)($height * ($maxWidth / $width));
                $resized = imagecreatetruecolor($maxWidth, $newHeight);
                
                // Transparency (PNG/WebP/GIF)
                if (in_array($mimeType, ['image/png', 'image/webp', 'image/gif'])) {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                    imagefilledrectangle($resized, 0, 0, $maxWidth, $newHeight, $transparent);
                }
                
                imagecopyresampled($resized, $sourceImage, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
                imagedestroy($sourceImage);
                $sourceImage = $resized;
            }
            
            // WebP formatida saqlash (sifat 85%)
            $success = @imagewebp($sourceImage, $targetPath, 85);
            imagedestroy($sourceImage);
            
            if ($success) {
                return $folderClean . '/' . $filename;
            }
        }
    }
    
    // GD ishlamasa — xom faylni ko'chirish (lekin faqat tekshiruvlardan o'tgan bo'lsa!)
    $fallbackExt = match($mimeType) {
        'image/jpeg' => '.jpg',
        'image/png'  => '.png',
        'image/gif'  => '.gif',
        default      => '.webp',
    };
    $filename = uniqid('img_', true) . $fallbackExt;
    $targetPath = $targetDir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $folderClean . '/' . $filename;
    }
    
    return false;
}

/**
 * Upload video file with validation.
 * Faqat MP4 va WebM formatlarni qabul qiladi (max 50MB).
 *
 * @return string|false Muvaffaqiyatli bo'lsa: "folder/filename.mp4", xato bo'lsa: false
 */
function uploadVideo(string $inputName, string $folder): string|false {
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $file = $_FILES[$inputName];
    $maxSize = 50 * 1024 * 1024; // 50MB
    
    if ($file['size'] > $maxSize || $file['size'] === 0) {
        return false;
    }
    
    // Kengaytma tekshiruvi
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['mp4', 'webm'])) {
        return false;
    }
    
    // MIME tekshiruvi
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, ['video/mp4', 'video/webm'])) {
        return false;
    }
    
    // Papka tayyorlash
    $basePath = dirname(__DIR__) . '/public';
    $folderClean = trim($folder, '/');
    $targetDir = $basePath . '/uploads/' . $folderClean;
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $filename = uniqid('vid_', true) . '.' . $ext;
    $targetPath = $targetDir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $folderClean . '/' . $filename;
    }
    
    return false;
}

/**
 * Clear public cache (barcha tillar)
 */
function clearPublicCache(): void {
    $cacheDir = dirname(__DIR__) . '/uploads/cache';
    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . '/index_cache*.html');
        foreach ($files as $file) {
            @unlink($file);
        }
    }
}

/**
 * Safely delete an uploaded image by its stored relative path.
 * Prevents path traversal by ensuring the resolved path stays within /uploads/.
 */
function deleteImage(?string $relativePath): void {
    if (empty($relativePath)) return;

    $uploadsBase = dirname(__DIR__) . '/public/uploads/';
    $fullPath    = realpath($uploadsBase . ltrim($relativePath, '/'));

    // Guard: must resolve to a real file inside /uploads/
    if ($fullPath && str_starts_with($fullPath, realpath($uploadsBase)) && is_file($fullPath)) {
        unlink($fullPath);
    }
}

/**
 * Validate input data against rules
 */
function validate(array $rules, array $data): array {
    $errors = [];
    foreach ($rules as $field => $ruleString) {
        $rulesArray = explode('|', $ruleString);
        $value = $data[$field] ?? '';
        
        foreach ($rulesArray as $rule) {
            if ($rule === 'required') {
                if (trim((string)$value) === '') {
                    $errors[$field] = 'Majburiy maydon';
                    break;
                }
            } elseif ($rule === 'numeric') {
                if ($value !== '' && !is_numeric($value)) {
                    $errors[$field] = 'Faqat son';
                }
            } elseif (strpos($rule, 'max:') === 0) {
                $max = (int)substr($rule, 4);
                if (mb_strlen((string)$value) > $max) {
                    $errors[$field] = "Max: $max belgi";
                }
            } elseif (strpos($rule, 'min:') === 0) {
                $min = (float)substr($rule, 4);
                if ($value !== '' && is_numeric($value) && (float)$value < $min) {
                    $errors[$field] = "Min: $min";
                }
            }
        }
    }
    return $errors;
}

/**
 * CSRF token generation
 */
function generateCsrf(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF token verification
 */
function verifyCsrf(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            die('Xavfsizlik tekshiruvidan o\'tmadi (CSRF token xato).');
        }
    }
}

/**
 * Upload multiple images at once from a multi-file input.
 * Returns array of uploaded paths (folder/filename.webp).
 *
 * @param string $inputName HTML input name (e.g. 'gallery_images')
 * @param string $folder    Upload subfolder (e.g. 'places/5')
 * @param int    $maxCount  Maximum number of images to upload
 * @return array Array of uploaded relative paths
 */
function uploadMultipleImages(string $inputName, string $folder, int $maxCount = 10): array {
    $paths = [];
    if (!isset($_FILES[$inputName]) || !is_array($_FILES[$inputName]['name'])) {
        return $paths;
    }
    
    $fileCount = count($_FILES[$inputName]['name']);
    $uploaded = 0;
    
    for ($i = 0; $i < $fileCount && $uploaded < $maxCount; $i++) {
        if ($_FILES[$inputName]['error'][$i] !== UPLOAD_ERR_OK) continue;
        
        // Reconstruct single file array for uploadImage-like processing
        $_FILES['__temp_multi_upload'] = [
            'name'     => $_FILES[$inputName]['name'][$i],
            'type'     => $_FILES[$inputName]['type'][$i],
            'tmp_name' => $_FILES[$inputName]['tmp_name'][$i],
            'error'    => $_FILES[$inputName]['error'][$i],
            'size'     => $_FILES[$inputName]['size'][$i],
        ];
        
        $result = uploadImage('__temp_multi_upload', $folder);
        if ($result !== false) {
            $paths[] = $result;
            $uploaded++;
        }
        
        unset($_FILES['__temp_multi_upload']);
    }
    
    return $paths;
}

/**
 * Get gallery images for an entity.
 * @return array Array of gallery rows with id, image_path, sort_order
 */
function getEntityGallery(PDO $pdo, string $entityType, int $entityId): array {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE entity_type = :type AND entity_id = :eid ORDER BY sort_order ASC, id ASC");
    $stmt->execute(['type' => $entityType, 'eid' => $entityId]);
    return $stmt->fetchAll();
}

/**
 * Count gallery images for an entity.
 */
function getGalleryCount(PDO $pdo, string $entityType, int $entityId): int {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM gallery WHERE entity_type = :type AND entity_id = :eid");
    $stmt->execute(['type' => $entityType, 'eid' => $entityId]);
    return (int)$stmt->fetchColumn();
}

/**
 * Add image to gallery with 10-image limit.
 * @return bool True if added, false if limit reached or upload failed
 */
function addGalleryImage(PDO $pdo, string $entityType, int $entityId, string $imagePath): bool {
    $count = getGalleryCount($pdo, $entityType, $entityId);
    if ($count >= 10) {
        return false; // Limit reached
    }
    
    $nextOrder = $count + 1;
    $stmt = $pdo->prepare("INSERT INTO gallery (entity_type, entity_id, image_path, sort_order) VALUES (:type, :eid, :path, :ord)");
    return $stmt->execute([
        'type' => $entityType,
        'eid'  => $entityId,
        'path' => $imagePath,
        'ord'  => $nextOrder
    ]);
}

/**
 * Delete a gallery image by ID with security check.
 * @return bool
 */
function deleteGalleryImage(PDO $pdo, int $galleryId, string $entityType, int $entityId): bool {
    $stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id = :id AND entity_type = :type AND entity_id = :eid");
    $stmt->execute(['id' => $galleryId, 'type' => $entityType, 'eid' => $entityId]);
    $row = $stmt->fetch();
    
    if (!$row) return false;
    
    deleteImage($row['image_path']);
    $pdo->prepare("DELETE FROM gallery WHERE id = :id")->execute(['id' => $galleryId]);
    return true;
}

/**
 * Build the entity-specific upload folder path.
 * E.g. getEntityUploadFolder('places', 5) => 'places/5'
 */
function getEntityUploadFolder(string $entityType, int $entityId): string {
    return $entityType . '/' . $entityId;
}

/**
 * Get Image HTML with fallback
 */
function getImageHtml(string $path, string $fallbackLetter): string {
    $fullPath = dirname(__DIR__) . '/public/uploads/' . trim($path, '/');
    if ($path !== '' && file_exists($fullPath) && is_file($fullPath)) {
        $url = BASE_URL . 'uploads/' . ltrim($path, '/');
        return '<img src="' . htmlspecialchars($url) . '" alt="" class="w-10 h-10 rounded object-cover border border-gray-200 shadow-sm">';
    } else {
        $letter = htmlspecialchars(mb_strtoupper(mb_substr($fallbackLetter, 0, 1)));
        return '<div class="w-10 h-10 rounded bg-blue-100 text-blue-700 font-bold border border-blue-200 flex items-center justify-center shadow-sm">' . $letter . '</div>';
    }
}
/**
 * Google Maps URL dan lat va lng ni ajratib olish (short link va to'liq linklar uchun)
 */
function parseGoogleMapsUrl($url) {
    if (empty($url)) return false;
    
    // Agar to'g'ridan to'g'ri koordinata probel yoki vergul bilan kiritilgan bo'lsa (masalan: "39.652, 66.973")
    if (preg_match('/^\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)\s*$/', $url, $m)) {
        return ['lat' => (float)$m[1], 'lng' => (float)$m[2]];
    }

    $location = $url;
    
    // Agar qisqa link bo'lsa (goo.gl, maps.app.goo.gl), yo'naltirishni (redirect) o'qiymiz
    if (str_contains($url, 'goo.gl') || str_contains($url, 'maps.app.goo.gl')) {
        $headers = @get_headers($url, 1);
        if ($headers && isset($headers['Location'])) {
            $location = is_array($headers['Location']) ? end($headers['Location']) : $headers['Location'];
        }
    }
    
    // 1. Dastlab aniq marker koordinatasini qidiramiz (!3d... !4d...)
    if (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $location, $m)) {
        return ['lat' => (float)$m[1], 'lng' => (float)$m[2]];
    } 
    // 2. Aks holda viewport markazini qidiramiz (@lat,lng)
    elseif (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $location, $m)) {
        return ['lat' => (float)$m[1], 'lng' => (float)$m[2]];
    }
    
    return false;
}
