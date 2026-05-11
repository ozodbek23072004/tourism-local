<?php
/**
 * Returns a public image URL for display with smart fallbacks.
 * 
 * DB dagi image ustunida saqlanishi mumkin bo'lgan formatlar:
 *   1. "places/img_abc123.webp" — uploads/ ichidagi subfolder
 *   2. "registan.png" — uploads/ ildizida
 *   3. "https://..." — to'liq tashqi URL
 *   4. NULL yoki "" — rasm yo'q
 */
function publicImage(?string $path, string $keyword = ''): string {
    $path = trim((string)$path);
    
    // Bo'sh bo'lsa, fallback
    if ($path === '' || $path === 'NULL') {
        return _imageFallback($keyword);
    }
    
    // Boshidagi / larni olib tashlaymiz
    $path = ltrim($path, '/');
    
    // 1. To'liq URL bo'lsa, to'g'ridan-to'g'ri qaytaramiz
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        return $path;
    }

    // 2. Lokal faylni topamiz
    //    DB da saqlangan yo'llar:
    //    - "places/img_abc.webp"  => public/uploads/places/img_abc.webp
    //    - "registan.png"         => public/uploads/registan.png
    //    - "uploads/registan.png" => public/uploads/registan.png (redundant prefix)
    
    $docRoot = dirname(__DIR__);  // tourism.local/public
    $projectRoot = dirname($docRoot); // tourism.local
    
    // To'g'ridan to'g'ri uploads/ ichida qidirish
    $searchPaths = [
        $docRoot . '/uploads/' . $path,           // public/uploads/places/img.webp
        $docRoot . '/' . $path,                     // public/registan.png (unlikely)
        $projectRoot . '/uploads/' . $path,         // uploads/ (project root)
    ];
    
    // Agar path "uploads/" bilan boshlansa, dublikatni oldini olamiz
    if (str_starts_with($path, 'uploads/')) {
        $cleanPath = substr($path, 8);
        array_unshift($searchPaths, $docRoot . '/uploads/' . $cleanPath);
    }
    
    foreach ($searchPaths as $fullPath) {
        if (file_exists($fullPath) && is_file($fullPath)) {
            // URL yaratish: BASE_URL + uploads/ + path
            $uploadsBase = realpath($docRoot . '/uploads');
            $realFile = realpath($fullPath);
            
            if ($uploadsBase && $realFile && str_starts_with($realFile, $uploadsBase)) {
                $relativePath = str_replace('\\', '/', substr($realFile, strlen($uploadsBase) + 1));
                return rtrim(BASE_URL, '/') . '/public/uploads/' . ltrim($relativePath, '/');
            }
        }
    }
    
    // 3. Topilmadi — fallback
    return _imageFallback($keyword ?: $path);
}

/**
 * Kontekstga mos placeholder rasm qaytaradi.
 * Hech qachon bir xil rasm qaytarmaydi — har bir keyword uchun noyob.
 */
function _imageFallback(string $keyword): string {
    // Keyword ni tozalaymiz
    $keyword = str_replace(['_', '-', '.', 'png', 'jpg', 'webp'], ' ', $keyword);
    $keyword = preg_replace('/[^a-z0-9 ]/i', '', $keyword);
    $keyword = trim($keyword);
    
    if (empty($keyword)) {
        $keyword = 'uzbekistan architecture';
    }
    
    // placehold.co — har doim ishlaydi, har doim yuklanadi, xavfsiz
    $seed = abs(crc32($keyword)) % 999;
    $bgColor = dechex(($seed * 37) % 200 + 30) . dechex(($seed * 73) % 200 + 30) . dechex(($seed * 11) % 200 + 30);
    $text = urlencode(mb_substr($keyword, 0, 15));
    
    return "https://placehold.co/800x600/{$bgColor}/ffffff?text={$text}";
}
