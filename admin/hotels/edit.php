<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../public/includes/image.php';

requireAuth();

$id = $_GET['id'] ?? null;
if (!$id) redirect('index.php');

$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$id]);
$hotel = $stmt->fetch();
if (!$hotel) {
    http_response_code(404);
    $pageTitle = "Sahifa topilmadi";
    require_once __DIR__ . '/../../includes/layout_header.php';
    echo '<div class="p-8 text-center text-gray-500">Yozuv topilmadi (#404)</div>';
    require_once __DIR__ . '/../../includes/layout_footer.php';
    exit;
}

$errors = [];
$uploadFolder = getEntityUploadFolder('hotels', (int)$id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    // === GALEREYA ===
    if (isset($_POST['add_gallery'])) {
        $currentCount = getGalleryCount($pdo, 'hotel', (int)$id);
        
        if ($currentCount >= 10) {
            flashMessage('error', "Galereya limiti (10 ta) to'lgan!");
        } else {
            $remainingSlots = 10 - $currentCount;
            $uploaded = 0;
            
            if (isset($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['name'])) {
                $paths = uploadMultipleImages('gallery_images', 'gallery/' . $uploadFolder, $remainingSlots);
                foreach ($paths as $path) {
                    addGalleryImage($pdo, 'hotel', (int)$id, $path);
                    $uploaded++;
                }
            }
            elseif (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $galleryPath = uploadImage('gallery_image', 'gallery/' . $uploadFolder);
                if ($galleryPath) {
                    addGalleryImage($pdo, 'hotel', (int)$id, $galleryPath);
                    $uploaded = 1;
                }
            }
            
            if ($uploaded > 0) {
                flashMessage('success', "$uploaded ta galereya rasmi qo'shildi!");
            } else {
                flashMessage('error', "Rasm yuklashda xatolik!");
            }
        }
        redirect("edit.php?id=$id");
    }

    // === VIDEO ===
    if (isset($_POST['upload_video'])) {
        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $videoPath = uploadVideo('video_file', 'videos/' . $uploadFolder);
            if ($videoPath) {
                $pdo->prepare("UPDATE hotels SET video_url = ? WHERE id = ?")->execute([BASE_URL . 'uploads/' . $videoPath, $id]);
                flashMessage('success', "Video yuklandi!");
            } else {
                flashMessage('error', "Video yuklashda xatolik!");
            }
        } elseif (!empty($_POST['video_url'])) {
            $pdo->prepare("UPDATE hotels SET video_url = ? WHERE id = ?")->execute([sanitize($_POST['video_url']), $id]);
            flashMessage('success', "Video havola saqlandi!");
        }
        redirect("edit.php?id=$id");
    }

    if (isset($_POST['delete_video'])) {
        $pdo->prepare("UPDATE hotels SET video_url = NULL WHERE id = ?")->execute([$id]);
        flashMessage('success', "Video o'chirildi!");
        redirect("edit.php?id=$id");
    }

    // === ASOSIY ===
    if (!isset($_POST['add_gallery']) && !isset($_POST['upload_video']) && !isset($_POST['delete_video'])) {
        $hotel['name_uz']        = sanitize($_POST['name_uz'] ?? '');
        $hotel['name_ru']        = sanitize($_POST['name_ru'] ?? '');
        $hotel['name_en']        = sanitize($_POST['name_en'] ?? '');
        $hotel['description_uz'] = cleanText($_POST['description_uz'] ?? '');
        $hotel['description_ru'] = cleanText($_POST['description_ru'] ?? '');
        $hotel['description_en'] = cleanText($_POST['description_en'] ?? '');
        $hotel['stars']          = in_array($_POST['stars'] ?? '', ['1','2','3','4','5']) ? $_POST['stars'] : '1';
        $hotel['price_from']     = sanitize($_POST['price_from'] ?? '');
        $hotel['phone']          = sanitize($_POST['phone'] ?? '');
        $hotel['region_id']      = sanitize($_POST['region_id'] ?? '');
        $hotel['address']        = sanitize($_POST['address'] ?? '');
        $hotel['status']         = in_array($_POST['status'] ?? '', ['active', 'draft']) ? $_POST['status'] : 'draft';

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

        $rules = ['name_uz' => 'required|max:255'];
        $errors = validate($rules, $hotel);

        $imagePath = $hotel['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = uploadImage('image', $uploadFolder);
            if ($upload) {
                if (!empty($hotel['image']) && !filter_var($hotel['image'], FILTER_VALIDATE_URL)) {
                    deleteImage($hotel['image']);
                }
                $imagePath = $upload;
            } else {
                $errors['image'] = "Rasm yuklashda xatolik!";
            }
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("UPDATE hotels SET name_uz=?, name_ru=?, name_en=?, description_uz=?, description_ru=?, description_en=?, stars=?, price_from=?, phone=?, region_id=?, address=?, latitude=?, longitude=?, image=?, status=? WHERE id=?");
            $stmt->execute([
                $hotel['name_uz'], $hotel['name_ru'], $hotel['name_en'],
                $hotel['description_uz'], $hotel['description_ru'], $hotel['description_en'],
                $hotel['stars'],
                $hotel['price_from'] !== '' ? $hotel['price_from'] : null,
                $hotel['phone'],
                $hotel['region_id']  !== '' ? $hotel['region_id']  : null,
                $hotel['address'],
                $hotel['latitude']   !== '' ? $hotel['latitude']   : null,
                $hotel['longitude']  !== '' ? $hotel['longitude']  : null,
                $imagePath, $hotel['status'], $id
            ]);
            clearPublicCache();
            flashMessage('success', "Mehmonxona muvaffaqiyatli tahrirlandi!");
            redirect('index.php');
        }
    }
}

// === GALEREYA O'CHIRISH ===
if (isset($_GET['delete_gallery'])) {
    $gid = (int)$_GET['delete_gallery'];
    deleteGalleryImage($pdo, $gid, 'hotel', (int)$id);
    flashMessage('success', "Galereya rasmi o'chirildi!");
    redirect("edit.php?id=$id");
}

$stmtH = $pdo->prepare("SELECT * FROM hotels WHERE id = ?"); $stmtH->execute([$id]); $hotel = $stmtH->fetch() ?: $hotel;
$stmtReg = $pdo->prepare("SELECT id, name_uz FROM regions ORDER BY name_uz"); $stmtReg->execute(); $regions = $stmtReg->fetchAll();
$gallery = getEntityGallery($pdo, 'hotel', (int)$id);
$galleryCount = count($gallery);

$pageTitle = "Mehmonxonani tahrirlash";
require_once '../../includes/layout_header.php';

$flashSuccess = getFlash('success');
$flashError = getFlash('error');
?>

<?php if ($flashSuccess): ?>
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
    <span class="material-symbols-outlined text-lg">check_circle</span> <?= htmlspecialchars($flashSuccess) ?>
</div>
<?php endif; ?>
<?php if ($flashError): ?>
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
    <span class="material-symbols-outlined text-lg">error</span> <?= htmlspecialchars($flashError) ?>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-500">edit_note</span> Asosiy ma'lumotlar
            </h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <?php require_once '../../includes/hotels_form.php'; ?>
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors">Saqlash</button>
                    <a href="index.php" class="ml-4 text-gray-500 hover:text-gray-800 font-medium">Bekor qilish</a>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Joriy rasm -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-green-500 text-lg">image</span> Joriy rasm
            </h3>
            <div class="aspect-video rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                <img src="<?= publicImage($hotel['image'], $hotel['name_uz']) ?>" class="w-full h-full object-cover">
            </div>
        </div>

        <!-- Video -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-red-500 text-lg">play_circle</span> Video
            </h3>
            <?php if (!empty($hotel['video_url'])): ?>
            <div class="aspect-video rounded-lg overflow-hidden bg-black mb-3">
                <?php if (strpos($hotel['video_url'], 'youtube.com') !== false || strpos($hotel['video_url'], 'youtu.be') !== false):
                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $hotel['video_url'], $m);
                ?>
                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/<?= $m[1] ?? '' ?>" frameborder="0" allowfullscreen></iframe>
                <?php else: ?>
                    <video controls class="w-full h-full"><source src="<?= htmlspecialchars($hotel['video_url']) ?>" type="video/mp4"></video>
                <?php endif; ?>
            </div>
            <form action="" method="POST" class="inline"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrf()) ?>">
                <button type="submit" name="delete_video" class="text-red-500 text-xs hover:text-red-700 font-semibold" onclick="return confirm('O\'chirilsinmi?')">🗑 O'chirish</button>
            </form>
            <?php else: ?>
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-3">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrf()) ?>">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Video fayl (MP4/WebM)</label>
                <input type="file" name="video_file" accept="video/mp4,video/webm" class="text-xs file:bg-red-50 file:text-red-700 file:border-0 file:rounded-lg file:px-3 file:py-1.5 w-full"></div>
                <div class="flex items-center gap-2 text-xs text-gray-400"><div class="flex-1 h-px bg-gray-200"></div>yoki<div class="flex-1 h-px bg-gray-200"></div></div>
                <div><input type="text" name="video_url" placeholder="https://youtube.com/watch?v=..." class="w-full px-3 py-2 border rounded-lg text-sm outline-none border-gray-300"></div>
                <button type="submit" name="upload_video" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-bold">🎬 Qo'shish</button>
            </form>
            <?php endif; ?>
        </div>

        <!-- Galereya -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-500 text-lg">photo_library</span> Galereya
                </span>
                <span class="text-xs font-normal px-2 py-0.5 rounded-full <?= $galleryCount >= 10 ? 'bg-red-100 text-red-600' : 'bg-purple-100 text-purple-600' ?>">
                    <?= $galleryCount ?>/10
                </span>
            </h3>
            
            <?php if ($galleryCount < 10): ?>
            <form action="" method="POST" enctype="multipart/form-data" class="mb-4 pb-4 border-b border-gray-100">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrf()) ?>">
                <label class="block text-xs font-medium text-gray-600 mb-2">Yangi rasm qo'shish (<?= 10 - $galleryCount ?> ta joy qoldi)</label>
                <div class="space-y-2">
                    <input type="file" name="gallery_images[]" accept="image/*" multiple class="text-xs file:bg-purple-50 file:text-purple-700 file:border-0 file:rounded-lg file:px-3 file:py-1.5 w-full">
                    <p class="text-[10px] text-gray-400">Bir nechta rasm tanlash mumkin</p>
                    <button type="submit" name="add_gallery" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-xs font-bold">📸 Yuklash</button>
                </div>
            </form>
            <?php else: ?>
            <div class="mb-4 pb-4 border-b border-gray-100">
                <p class="text-xs text-red-500 bg-red-50 px-3 py-2 rounded-lg text-center">⚠️ Galereya limiti (10 ta) to'lgan</p>
            </div>
            <?php endif; ?>
            
            <div class="grid grid-cols-2 gap-3">
                <?php if (empty($gallery)): ?>
                    <p class="col-span-2 text-center text-gray-400 text-xs py-4 italic">Rasmlar yo'q</p>
                <?php else: ?>
                    <?php foreach ($gallery as $g): ?>
                    <div class="relative group aspect-square rounded-lg overflow-hidden border border-gray-100">
                        <img src="<?= publicImage($g['image_path']) ?>" class="w-full h-full object-cover">
                        <a href="?id=<?= $id ?>&delete_gallery=<?= $g['id'] ?>" class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-lg" onclick="return confirm('O\'chirilsinmi?')">×</a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/layout_footer.php'; ?>
