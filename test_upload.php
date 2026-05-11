<?php
// Diagnostika skripti — muammoni topish uchun
echo "<h2>🔍 Rasm yuklash diagnostikasi</h2>";

// 1. PHP sozlamalari
echo "<h3>1. PHP sozlamalari</h3>";
echo "<table border=1 cellpadding=5>";
echo "<tr><td>upload_max_filesize</td><td>" . ini_get('upload_max_filesize') . "</td></tr>";
echo "<tr><td>post_max_size</td><td>" . ini_get('post_max_size') . "</td></tr>";
echo "<tr><td>file_uploads</td><td>" . (ini_get('file_uploads') ? 'ON' : 'OFF') . "</td></tr>";
echo "<tr><td>max_file_uploads</td><td>" . ini_get('max_file_uploads') . "</td></tr>";
echo "<tr><td>upload_tmp_dir</td><td>" . (ini_get('upload_tmp_dir') ?: sys_get_temp_dir()) . "</td></tr>";
echo "<tr><td>tmp_dir writable?</td><td>" . (is_writable(ini_get('upload_tmp_dir') ?: sys_get_temp_dir()) ? '✅ YES' : '❌ NO') . "</td></tr>";
echo "</table>";

// 2. GD library
echo "<h3>2. GD kutubxonasi</h3>";
if (extension_loaded('gd')) {
    $gdInfo = gd_info();
    echo "<table border=1 cellpadding=5>";
    echo "<tr><td>GD versiyasi</td><td>" . $gdInfo['GD Version'] . "</td></tr>";
    echo "<tr><td>JPEG</td><td>" . ($gdInfo['JPEG Support'] ? '✅' : '❌') . "</td></tr>";
    echo "<tr><td>PNG</td><td>" . ($gdInfo['PNG Support'] ? '✅' : '❌') . "</td></tr>";
    echo "<tr><td>WebP</td><td>" . (($gdInfo['WebP Support'] ?? false) ? '✅' : '❌') . "</td></tr>";
    echo "<tr><td>GIF</td><td>" . ($gdInfo['GIF Read Support'] ? '✅' : '❌') . "</td></tr>";
    echo "<tr><td>imagecreatefromjpeg</td><td>" . (function_exists('imagecreatefromjpeg') ? '✅' : '❌') . "</td></tr>";
    echo "<tr><td>imagewebp</td><td>" . (function_exists('imagewebp') ? '✅' : '❌') . "</td></tr>";
    echo "</table>";
} else {
    echo "<p style='color:red;font-size:20px'>❌❌❌ GD KUTUBXONASI YUKLANMAGAN! Bu asosiy muammo!</p>";
}

// 3. finfo
echo "<h3>3. finfo (MIME tekshiruv)</h3>";
echo extension_loaded('fileinfo') ? "✅ fileinfo mavjud" : "❌ fileinfo YO'Q!";

// 4. Upload papka
echo "<h3>4. Upload papka</h3>";
$uploadDir = __DIR__ . '/public/uploads';
echo "<table border=1 cellpadding=5>";
echo "<tr><td>Yo'l</td><td>$uploadDir</td></tr>";
echo "<tr><td>Mavjud?</td><td>" . (is_dir($uploadDir) ? '✅ YES' : '❌ NO') . "</td></tr>";
echo "<tr><td>Yozish mumkin?</td><td>" . (is_writable($uploadDir) ? '✅ YES' : '❌ NO') . "</td></tr>";
echo "</table>";

// 5. Test upload form
echo "<h3>5. Test upload</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_img'])) {
    $f = $_FILES['test_img'];
    echo "<table border=1 cellpadding=5>";
    echo "<tr><td>Fayl nomi</td><td>" . htmlspecialchars($f['name']) . "</td></tr>";
    echo "<tr><td>Hajmi</td><td>" . number_format($f['size']) . " bytes (" . round($f['size']/1024/1024, 2) . " MB)</td></tr>";
    echo "<tr><td>type (brauzer)</td><td>" . $f['type'] . "</td></tr>";
    echo "<tr><td>tmp_name</td><td>" . $f['tmp_name'] . "</td></tr>";
    echo "<tr><td>error code</td><td>" . $f['error'] . "</td></tr>";
    
    if ($f['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            1 => 'upload_max_filesize dan oshib ketdi (php.ini)',
            2 => 'MAX_FILE_SIZE dan oshib ketdi (form)',
            3 => 'Faqat qisman yuklandi',
            4 => 'Fayl tanlanmadi',
            6 => 'Temp papka topilmadi',
            7 => 'Diskka yozib bo\'lmadi',
        ];
        echo "<tr><td style='color:red'>XATO!</td><td style='color:red'>" . ($errors[$f['error']] ?? 'Noma\'lum xato') . "</td></tr>";
    } else {
        // MIME check
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = finfo_file($finfo, $f['tmp_name']);
        finfo_close($finfo);
        echo "<tr><td>Haqiqiy MIME</td><td>$realMime</td></tr>";
        
        // Extension
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        echo "<tr><td>Kengaytma</td><td>$ext</td></tr>";
        
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        
        echo "<tr><td>Kengaytma ruxsat?</td><td>" . (in_array($ext, $allowedExts) ? '✅' : '❌ Rad etiladi!') . "</td></tr>";
        echo "<tr><td>MIME ruxsat?</td><td>" . (in_array($realMime, $allowedMimes) ? '✅' : '❌ Rad etiladi!') . "</td></tr>";
        
        // getimagesize
        $imgInfo = @getimagesize($f['tmp_name']);
        echo "<tr><td>getimagesize</td><td>" . ($imgInfo ? $imgInfo[0].'x'.$imgInfo[1].' ✅' : '❌ Rad etiladi!') . "</td></tr>";
        
        // GD test
        if (function_exists('imagecreatefromjpeg') && in_array($realMime, $allowedMimes)) {
            $src = match($realMime) {
                'image/jpeg' => @imagecreatefromjpeg($f['tmp_name']),
                'image/png' => @imagecreatefrompng($f['tmp_name']),
                'image/webp' => @imagecreatefromwebp($f['tmp_name']),
                'image/gif' => @imagecreatefromgif($f['tmp_name']),
                default => false,
            };
            echo "<tr><td>GD yaratish</td><td>" . ($src ? '✅ OK' : '❌ Xato!') . "</td></tr>";
            
            if ($src) {
                $testFile = $uploadDir . '/test_diag_' . time() . '.webp';
                $webpOk = @imagewebp($src, $testFile, 85);
                echo "<tr><td>WebP saqlash</td><td>" . ($webpOk ? '✅ OK: ' . $testFile : '❌ Xato! imagewebp() ishlamadi') . "</td></tr>";
                if ($webpOk && file_exists($testFile)) {
                    echo "<tr><td>Natija fayl</td><td>✅ " . filesize($testFile) . " bytes</td></tr>";
                    @unlink($testFile);
                }
                imagedestroy($src);
            }
        }
    }
    echo "</table>";
}
?>

<form method="POST" enctype="multipart/form-data" style="margin-top:20px; padding:20px; border:2px dashed #999;">
    <p><b>Test rasm yuklang:</b></p>
    <input type="file" name="test_img" accept="image/*">
    <button type="submit" style="padding:10px 20px; background:#4CAF50; color:white; border:none; cursor:pointer;">Yuklash</button>
</form>
