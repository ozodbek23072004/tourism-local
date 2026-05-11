<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireAuth();

$errors = [];
$hotel = [
    'name_uz' => '', 'name_ru' => '', 'name_en' => '',
    'description_uz' => '', 'description_ru' => '', 'description_en' => '',
    'stars' => '1',
    'price_from' => '', 'phone' => '',
    'region_id' => '', 'address' => '',
    'status' => 'active'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hotel['name_uz'] = sanitize($_POST['name_uz'] ?? '');
    $hotel['name_ru'] = sanitize($_POST['name_ru'] ?? '');
    $hotel['name_en'] = sanitize($_POST['name_en'] ?? '');
    $hotel['description_uz'] = cleanText($_POST['description_uz'] ?? '');
    $hotel['description_ru'] = cleanText($_POST['description_ru'] ?? '');
    $hotel['description_en'] = cleanText($_POST['description_en'] ?? '');
    $hotel['stars'] = in_array($_POST['stars'] ?? '', ['1','2','3','4','5']) ? $_POST['stars'] : '1';
    $hotel['price_from'] = sanitize($_POST['price_from'] ?? '');
    $hotel['phone'] = sanitize($_POST['phone'] ?? '');
    $hotel['region_id'] = sanitize($_POST['region_id'] ?? '');
    $hotel['address'] = sanitize($_POST['address'] ?? '');
    $hotel['status'] = in_array($_POST['status'] ?? '', ['active', 'draft']) ? $_POST['status'] : 'draft';

    $map_link = trim($_POST['map_link'] ?? '');
    if ($map_link !== '') {
        $coords = parseGoogleMapsUrl($map_link);
        if ($coords) {
            $hotel['latitude'] = $coords['lat'];
            $hotel['longitude'] = $coords['lng'];
        } else {
            $errors['map_link'] = "Google Maps havolasi noto'g'ri yoki koordinata topilmadi.";
        }
    } else {
        $hotel['latitude'] = null;
        $hotel['longitude'] = null;
    }

    verifyCsrf();
    $rules = [
        'name_uz' => 'required|max:255',
        'price_from' => 'numeric|min:0'
    ];
    $errors = validate($rules, $hotel);

    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = uploadImage('image', 'hotels');
        if ($upload) $imagePath = $upload;
        else {
            global $uploadError;
            $errors['image'] = 'Xatolik: ' . ($uploadError ?: 'Format yoki hajm');
        }
    } else {
        $errors['image'] = 'Majburiy maydon';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO hotels (name_uz, name_ru, name_en, description_uz, description_ru, description_en, stars, price_from, phone, region_id, address, latitude, longitude, video_url, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $hotel['name_uz'], $hotel['name_ru'], $hotel['name_en'],
            $hotel['description_uz'], $hotel['description_ru'], $hotel['description_en'],
            $hotel['stars'],
            $hotel['price_from'] !== '' ? $hotel['price_from'] : null,
            $hotel['phone'],
            $hotel['region_id'] !== '' ? $hotel['region_id'] : null,
            $hotel['address'],
            $hotel['latitude'] !== '' ? $hotel['latitude'] : null,
            $hotel['longitude'] !== '' ? $hotel['longitude'] : null,
            sanitize($_POST['video_url'] ?? ''),
            $imagePath, $hotel['status']
        ]);
        flashMessage('success', "Qo'shildi!");
        redirect('index.php');
    }
}
$stmtReg = $pdo->prepare("SELECT id, name_uz FROM regions ORDER BY name_uz"); $stmtReg->execute(); $regions = $stmtReg->fetchAll();
$pageTitle = "Mehmonxona qo'shish";
require_once '../../includes/layout_header.php';
?>
<div class="bg-white rounded-xl shadow p-6"><form action="" method="POST" enctype="multipart/form-data">
    <?php require_once '../../includes/hotels_form.php'; ?>
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Saqlash</button>
</form></div>
<?php require_once '../../includes/layout_footer.php'; ?>
