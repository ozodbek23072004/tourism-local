<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../public/includes/image.php';
requireAuth();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('index.php');
$r = $pdo->prepare("SELECT * FROM regions WHERE id = ?");
$r->execute([$id]);
$r = $r->fetch();
if (!$r) { flashMessage('error', 'Viloyat topilmadi'); redirect('index.php'); }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $r['name_uz'] = sanitize($_POST['name_uz'] ?? '');
    $r['name_ru'] = sanitize($_POST['name_ru'] ?? '');
    $r['name_en'] = sanitize($_POST['name_en'] ?? '');
    $r['slug']    = sanitize($_POST['slug'] ?? '');
    
    if (empty($r['name_uz'])) $errors['name_uz'] = 'Nomi majburiy';
    
    $imagePath = $r['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = uploadImage('image', 'regions');
        if ($upload) {
            if ($r['image']) deleteImage($r['image']);
            $imagePath = $upload;
        } else {
            $errors['image'] = "Rasm yuklashda xatolik!";
        }
    }

    if (empty($errors)) {
        $pdo->prepare("UPDATE regions SET name_uz=?, name_ru=?, name_en=?, slug=?, image=? WHERE id=?")
            ->execute([$r['name_uz'], $r['name_ru'], $r['name_en'], $r['slug'], $imagePath, $id]);
        clearPublicCache();
        flashMessage('success', "Viloyat tahrirlandi!");
        redirect('index.php');
    }
}

$pageTitle = "Viloyatni tahrirlash";
require_once '../../includes/layout_header.php';
?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">🗺️ Viloyat tahrirlash</h2>
            <form method="POST" enctype="multipart/form-data" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= generateCsrf() ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomi (UZ) *</label>
                    <input type="text" name="name_uz" value="<?= htmlspecialchars($r['name_uz']) ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Nomi (RU)</label>
                    <input type="text" name="name_ru" value="<?= htmlspecialchars($r['name_ru']) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Nomi (EN)</label>
                    <input type="text" name="name_en" value="<?= htmlspecialchars($r['name_en']) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Rasm yuklash</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="<?= htmlspecialchars($r['slug']) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none"></div>
                </div>
                <div class="pt-4 border-t border-gray-100 flex gap-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg">Saqlash</button>
                    <a href="index.php" class="text-gray-500 hover:text-gray-800 font-medium py-2.5 px-4">Bekor qilish</a>
                </div>
            </form>
        </div>
    </div>
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4">Joriy rasm</h3>
            <div class="aspect-video rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                <img src="<?= publicImage($r['image'], $r['name_uz']) ?>" class="w-full h-full object-cover">
            </div>
        </div>
    </div>
</div>
<?php require_once '../../includes/layout_footer.php'; ?>
