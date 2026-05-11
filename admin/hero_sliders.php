<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../public/includes/image.php';

requireAuth();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // === RASM FAYL YUKLASH ===
    if (isset($_POST['add_image'])) {
        $pageKey = $_POST['page_key'] ?? 'home';
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        
        if (isset($_FILES['slider_image']) && $_FILES['slider_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadPath = uploadImage('slider_image', 'hero/' . $pageKey);
            if ($uploadPath) {
                $stmt = $pdo->prepare("INSERT INTO hero_sliders (page_key, image_path, sort_order) VALUES (?, ?, ?)");
                if ($stmt->execute([$pageKey, $uploadPath, $sortOrder])) {
                    $success = "Rasm muvaffaqiyatli yuklandi va qo'shildi!";
                } else {
                    $error = "Ma'lumotlar bazasiga saqlashda xatolik.";
                }
            } else {
                $error = "Rasm yuklashda xatolik. Format (JPG/PNG/WEBP/GIF) va hajmga (max 5MB) e'tibor bering.";
            }
        } else {
            $error = "Iltimos, rasm faylini tanlang.";
        }
    }

    // === RASM O'CHIRISH ===
    if (isset($_POST['delete_image'])) {
        $deleteId = (int)$_POST['id'];
        // Avval rasm yo'lini olish va faylni o'chirish
        $stmt = $pdo->prepare("SELECT image_path FROM hero_sliders WHERE id = ?");
        $stmt->execute([$deleteId]);
        $slider = $stmt->fetch();
        if ($slider) {
            // Lokal faylni o'chirish
            if (!filter_var($slider['image_path'], FILTER_VALIDATE_URL)) {
                deleteImage($slider['image_path']);
            }
            $pdo->prepare("DELETE FROM hero_sliders WHERE id = ?")->execute([$deleteId]);
            $success = "Rasm o'chirildi.";
        }
    }

    // === TARTIBNI SAQLASH ===
    if (isset($_POST['update_order'])) {
        foreach ($_POST['orders'] as $id => $order) {
            $stmt = $pdo->prepare("UPDATE hero_sliders SET sort_order = ? WHERE id = ?");
            $stmt->execute([(int)$order, (int)$id]);
        }
        $success = "Tartib yangilandi.";
    }
}

$pageKeyFilter = $_GET['page_key'] ?? 'home';
$stmt = $pdo->prepare("SELECT * FROM hero_sliders WHERE page_key = ? ORDER BY sort_order ASC");
$stmt->execute([$pageKeyFilter]);
$sliders = $stmt->fetchAll();

$pageTitle = 'Hero Sliderlar Boshqaruvi';
require_once '../includes/layout_header.php';
?>

<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <h1 class="text-2xl font-bold text-gray-800">Hero Sliderlar</h1>
    <div class="flex flex-wrap gap-2">
        <a href="?page_key=home" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $pageKeyFilter == 'home' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-600 border hover:bg-gray-50' ?>">🏠 Bosh sahifa</a>
        <a href="?page_key=places" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $pageKeyFilter == 'places' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-600 border hover:bg-gray-50' ?>">📍 Joylar</a>
        <a href="?page_key=people" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $pageKeyFilter == 'people' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-600 border hover:bg-gray-50' ?>">👤 Shaxslar</a>
        <a href="?page_key=restaurants" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $pageKeyFilter == 'restaurants' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-600 border hover:bg-gray-50' ?>">🍽 Restoranlar</a>
        <a href="?page_key=hotels" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $pageKeyFilter == 'hotels' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-600 border hover:bg-gray-50' ?>">🏨 Mehmonxonalar</a>
    </div>
</div>

<?php if ($error): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-lg">error</span> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-lg">check_circle</span> <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Yangi rasm yuklash formasi -->
    <div class="bg-white p-6 rounded-xl shadow-sm border h-fit">
        <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-500">add_photo_alternate</span>
            Yangi rasm yuklash
        </h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="page_key" value="<?= htmlspecialchars($pageKeyFilter) ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rasm fayli *</label>
                <input type="file" name="slider_image" accept="image/jpeg,image/png,image/webp,image/gif" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-500 mt-1">JPG, PNG, WEBP, GIF. Maks: 5MB.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tartib raqami</label>
                <input type="number" name="sort_order" value="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <button type="submit" name="add_image" class="w-full bg-blue-600 text-white font-bold py-2.5 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">upload</span> Yuklash
            </button>
        </form>
        
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400 text-center">
                📁 Rasmlar <code class="bg-gray-100 px-1 rounded">uploads/hero/<?= htmlspecialchars($pageKeyFilter) ?>/</code> papkasiga saqlanadi
            </p>
        </div>
    </div>

    <!-- Rasmlar ro'yxati -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b flex items-center justify-between">
            <h3 class="font-bold text-gray-700 flex items-center gap-2">
                <span class="material-symbols-outlined text-orange-500">collections</span>
                <?= htmlspecialchars(ucfirst($pageKeyFilter)) ?> sahifasi rasmlari
            </h3>
            <span class="text-xs text-gray-400 bg-white px-3 py-1 rounded-full border"><?= count($sliders) ?> ta rasm</span>
        </div>
        
        <?php if (count($sliders) > 0): ?>
        <form method="POST">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 p-6">
                <?php foreach ($sliders as $slider): ?>
                <div class="relative group rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                    <!-- Rasm -->
                    <div class="aspect-video">
                        <img src="<?= publicImage($slider['image_path'], $pageKeyFilter) ?>" 
                             class="w-full h-full object-cover"
                             onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22120%22><rect fill=%22%23f3f4f6%22 width=%22200%22 height=%22120%22/><text x=%22100%22 y=%2260%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22 font-size=%2211%22 fill=%22%23999%22>Yuklanmadi</text></svg>'">
                    </div>
                    
                    <!-- Controls overlay -->
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors flex items-center justify-center">
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                            <form method="POST" class="inline" onsubmit="return confirm('O\'chirishga aminmisiz?')">
                                <input type="hidden" name="id" value="<?= $slider['id'] ?>">
                                <button type="submit" name="delete_image" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-lg transition">
                                    🗑 O'chirish
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Sort order -->
                    <div class="p-2 flex items-center justify-between bg-white border-t">
                        <span class="text-[10px] text-gray-400 truncate max-w-[100px]" title="<?= htmlspecialchars($slider['image_path']) ?>">
                            📁 <?= htmlspecialchars(basename($slider['image_path'])) ?>
                        </span>
                        <input type="number" name="orders[<?= $slider['id'] ?>]" value="<?= $slider['sort_order'] ?>" 
                               class="w-12 border rounded px-1.5 py-0.5 text-xs text-center focus:ring-1 focus:ring-blue-400 outline-none">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="p-4 bg-gray-50 text-right border-t">
                <button type="submit" name="update_order" class="bg-gray-800 text-white px-6 py-2 rounded-lg font-bold hover:bg-black transition text-sm">
                    💾 Tartibni saqlash
                </button>
            </div>
        </form>
        <?php else: ?>
        <div class="p-12 text-center">
            <span class="material-symbols-outlined text-5xl text-gray-300 mb-3 block">image</span>
            <p class="text-gray-400 text-sm">Bu sahifa uchun hali rasm qo'shilmagan.</p>
            <p class="text-gray-300 text-xs mt-1">Chapdan rasm yuklang →</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/layout_footer.php'; ?>
