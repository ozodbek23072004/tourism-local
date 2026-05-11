<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireAuth();

$errors = [];
$place = [
    'name_uz' => '', 'name_ru' => '', 'name_en' => '',
    'description_uz' => '', 'description_ru' => '',
    'category_id' => '', 'region_id' => '',
    'address' => '', 'latitude' => '', 'longitude' => '',
    'status' => 'active' // Default
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF FIRST
    verifyCsrf();

    $place['name_uz']        = sanitize($_POST['name_uz'] ?? '');
    $place['name_ru']        = sanitize($_POST['name_ru'] ?? '');
    $place['name_en']        = sanitize($_POST['name_en'] ?? '');
    $place['description_uz'] = cleanText($_POST['description_uz'] ?? '');
    $place['description_ru'] = cleanText($_POST['description_ru'] ?? '');
    $place['description_en'] = cleanText($_POST['description_en'] ?? '');
    $place['category_id']    = sanitize($_POST['category_id'] ?? '');
    $place['region_id']      = sanitize($_POST['region_id'] ?? '');
    $place['address']        = sanitize($_POST['address'] ?? '');
    $place['status']         = in_array($_POST['status'] ?? '', ['active', 'draft']) ? $_POST['status'] : 'draft';

    $map_link = trim($_POST['map_link'] ?? '');
    if ($map_link !== '') {
        $coords = parseGoogleMapsUrl($map_link);
        if ($coords) {
            $place['latitude'] = $coords['lat'];
            $place['longitude'] = $coords['lng'];
        } else {
            $errors['map_link'] = "Google Maps havolasi noto'g'ri yoki koordinata topilmadi.";
        }
    } else {
        $place['latitude'] = sanitize($_POST['latitude'] ?? '');
        $place['longitude'] = sanitize($_POST['longitude'] ?? '');
    }

    $rules = [
        'name_uz'   => 'required|max:255',
        'name_ru'   => 'required|max:255',
        'name_en'   => 'required|max:255'
    ];
    $errors = validate($rules, $place);

    // Image Upload
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploadResult = uploadImage('image', 'places');
        if ($uploadResult === false) {
            $errors['image'] = 'Rasm yuklashda xatolik. Format (JPG/PNG/WEBP) va hajmga (Max: 2MB) e\'tibor bering.';
        } else {
            $imagePath = $uploadResult;
        }
    } else {
        $errors['image'] = 'Rasm yuklash majburiy';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO places 
                (name_uz, name_ru, name_en, description_uz, description_ru, description_en, category_id, region_id, address, latitude, longitude, video_url, image, status)
                VALUES 
                (:name_uz, :name_ru, :name_en, :description_uz, :description_ru, :description_en, :category_id, :region_id, :address, :latitude, :longitude, :video_url, :image, :status)
            ");
            
            $stmt->execute([
                'name_uz'        => $place['name_uz'],
                'name_ru'        => $place['name_ru'],
                'name_en'        => $place['name_en'],
                'description_uz' => $place['description_uz'],
                'description_ru' => $place['description_ru'],
                'description_en' => $place['description_en'],
                'category_id'    => $place['category_id'] !== '' ? $place['category_id'] : null,
                'region_id'      => $place['region_id'] !== '' ? $place['region_id'] : null,
                'address'        => $place['address'],
                'latitude'       => $place['latitude'] !== '' ? $place['latitude'] : null,
                'longitude'      => $place['longitude'] !== '' ? $place['longitude'] : null,
                'video_url'      => sanitize($_POST['video_url'] ?? ''),
                'image'          => $imagePath,
                'status'         => $place['status']
            ]);

            clearPublicCache();
            flashMessage('success', "Joy muvaffaqiyatli qo'shildi!");
            redirect('index.php');
        } catch (PDOException $e) {
            $errors['db'] = APP_ENV === 'development'
                ? "DB xatoligi: " . $e->getMessage()
                : "Saqlashda xatolik yuz berdi. Qayta urinib ko'ring.";
        }
    }
}

// Fetch lookups
$stmtCat = $pdo->prepare("SELECT id, name_uz FROM categories ORDER BY name_uz"); $stmtCat->execute(); $categories = $stmtCat->fetchAll();
$stmtReg = $pdo->prepare("SELECT id, name_uz FROM regions ORDER BY name_uz"); $stmtReg->execute(); $regions = $stmtReg->fetchAll();

$pageTitle = "Yangi joy qo'shish";
require_once '../../includes/layout_header.php';
?>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
    
    <?php if (isset($errors['db'])): ?>
        <div class="mb-6 bg-red-50 text-red-600 p-4 rounded-lg border border-red-100">
            <?= $errors['db'] ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <?php require_once '../../includes/places_form.php'; ?>
        
        <div class="flex items-center space-x-4 border-t border-gray-100 pt-6 mt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors">
                Saqlash
            </button>
            <a href="index.php" class="text-gray-500 hover:text-gray-800 font-medium py-2.5 px-4 transition-colors">
                Bekor qilish
            </a>
        </div>
    </form>
</div>

<?php require_once '../../includes/layout_footer.php'; ?>
