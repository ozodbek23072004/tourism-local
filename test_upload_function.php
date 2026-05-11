<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Create a dummy image
$dummyPath = __DIR__ . '/public/uploads/dummy.jpg';
$img = imagecreatetruecolor(100, 100);
imagejpeg($img, $dummyPath);
imagedestroy($img);

$_FILES['test'] = [
    'name' => 'dummy.jpg',
    'type' => 'image/jpeg',
    'tmp_name' => $dummyPath,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($dummyPath),
];

echo "File size: " . filesize($dummyPath) . "\n";
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $dummyPath);
finfo_close($finfo);
echo "MIME type: " . $mimeType . "\n";

$imgInfo = @getimagesize($dummyPath);
echo "getimagesize: " . print_r($imgInfo, true) . "\n";

$res = uploadImage('test', 'test_folder');
echo "Upload result: " . var_export($res, true) . "\n";
