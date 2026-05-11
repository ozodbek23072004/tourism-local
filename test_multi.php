<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Test uploadMultipleImages
$dummyPath1 = __DIR__ . '/public/uploads/dummy1.jpg';
$img1 = imagecreatetruecolor(100, 100); imagejpeg($img1, $dummyPath1); imagedestroy($img1);

$dummyPath2 = __DIR__ . '/public/uploads/dummy2.png';
$img2 = imagecreatetruecolor(100, 100); imagepng($img2, $dummyPath2); imagedestroy($img2);

$_FILES['gallery_images'] = [
    'name' => ['dummy1.jpg', 'dummy2.png'],
    'type' => ['image/jpeg', 'image/png'],
    'tmp_name' => [$dummyPath1, $dummyPath2],
    'error' => [UPLOAD_ERR_OK, UPLOAD_ERR_OK],
    'size' => [filesize($dummyPath1), filesize($dummyPath2)],
];

$res = uploadMultipleImages('gallery_images', 'gallery/test');
echo "Multi Upload result: \n";
print_r($res);
