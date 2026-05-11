<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireAuth();

$errors = [];
$restaurant = [
    'name_uz' => '', 'name_ru' => '', 'name_en' => '',
    'description_uz' => '', 'description_ru' => '', 'description_en' => '',
    'cuisine_type' => '',
    'price_range' => 'low', 'phone' => '', 'working_hours' => '',
    'region_id' => '', 'address' => '', 'latitude' => '', 'longitude' => '',
    'status' => 'active'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $restaurant['name_uz'] = sanitize($_POST['name_uz'] ?? '');
    $restaurant['name_ru'] = sanitize($_POST['name_ru'] ?? '');
    $restaurant['name_en'] = sanitize($_POST['name_en'] ?? '');
    $restaurant['description_uz'] = cleanText($_POST['description_uz'] ?? '');
    $restaurant['description_ru'] = cleanText($_POST['description_ru'] ?? '');
    $restaurant['description_en'] = cleanText($_POST['description_en'] ?? '');
    $restaurant['cuisine_type'] = sanitize($_POST['cuisine_type'] ?? '');
    $restaurant['price_range'] = in_array($_POST['price_range'] ?? '', ['low', 'mid', 'high']) ? $_POST['price_range'] : 'low';
    $restaurant['phone'] = sanitize($_POST['phone'] ?? '');
    $restaurant['working_hours'] = sanitize($_POST['working_hours'] ?? '');
    $restaurant['region_id'] = sanitize($_POST['region_id'] ?? '');
    $restaurant['address'] = sanitize($_POST['address'] ?? '');
    $restaurant['status'] = in_array($_POST['status'] ?? '', ['active', 'draft']) ? $_POST['status'] : 'draft';

    $map_link = trim($_POST['map_link'] ?? '');
    if ($map_link !== '') {
        $coords = parseGoogleMapsUrl($map_link);
        if ($coords) {
            $restaurant['latitude'] = $coords['lat'];
            $restaurant['longitude'] = $coords['lng'];
        } else {
            $errors['map_link'] = "Google Maps havolasi noto'g'ri yoki koordinata topilmadi.";
        }
    } else {
        $restaurant['latitude'] = null;
        $restaurant['longitude'] = null;
    }

    verifyCsrf();
    $rules = [
        'name_uz' => 'required|max:255'
    ];
    $errors = validate($rules, $restaurant);

    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = uploadImage('image', 'restaurants');
        if ($upload) $imagePath = $upload;
        else {
            global $uploadError;
            $errors['image'] = 'Xatolik: ' . ($uploadError ?: 'Format yoki hajm');
        }
    } else {
        $errors['image'] = 'Majburiy maydon';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO restaurants (name_uz, name_ru, name_en, description_uz, description_ru, description_en, cuisine_type, price_range, phone, working_hours, region_id, address, latitude, longitude, video_url, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $restaurant['name_uz'], $restaurant['name_ru'], $restaurant['name_en'],
            $restaurant['description_uz'], $restaurant['description_ru'], $restaurant['description_en'],
            $restaurant['cuisine_type'], $restaurant['price_range'],
            $restaurant['phone'], $restaurant['working_hours'],
            $restaurant['region_id'] !== '' ? $restaurant['region_id'] : null,
            $restaurant['address'],
            $restaurant['latitude'] !== '' ? $restaurant['latitude'] : null,
            $restaurant['longitude'] !== '' ? $restaurant['longitude'] : null,
            sanitize($_POST['video_url'] ?? ''),
            $imagePath, $restaurant['status']
        ]);
        flashMessage('success', "Qo'shildi!");
        redirect('index.php');
    }
}
$stmtReg = $pdo->prepare("SELECT id, name_uz FROM regions ORDER BY name_uz"); $stmtReg->execute(); $regions = $stmtReg->fetchAll();
$pageTitle = "Restoran qo'shish";
require_once '../../includes/layout_header.php';
?>
<div class="bg-white rounded-xl shadow p-6"><form action="" method="POST" enctype="multipart/form-data">
    <?php require_once '../../includes/restaurants_form.php'; ?>
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Saqlash</button>
</form></div>
<?php require_once '../../includes/layout_footer.php'; ?>
