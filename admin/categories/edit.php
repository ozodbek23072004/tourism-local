<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
requireAuth();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('index.php');
$cat = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$cat->execute([$id]);
$cat = $cat->fetch();
if (!$cat) { flashMessage('error', 'Kategoriya topilmadi'); redirect('index.php'); }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $cat['name_uz'] = sanitize($_POST['name_uz'] ?? '');
    $cat['name_ru'] = sanitize($_POST['name_ru'] ?? '');
    $cat['name_en'] = sanitize($_POST['name_en'] ?? '');
    $cat['icon']    = sanitize($_POST['icon'] ?? '');
    $cat['slug']    = sanitize($_POST['slug'] ?? '');
    if (empty($cat['name_uz'])) $errors['name_uz'] = 'Nomi majburiy';
    if (empty($errors)) {
        $pdo->prepare("UPDATE categories SET name_uz=?, name_ru=?, name_en=?, icon=?, slug=? WHERE id=?")
            ->execute([$cat['name_uz'], $cat['name_ru'], $cat['name_en'], $cat['icon'] ?: null, $cat['slug'], $id]);
        clearPublicCache();
        flashMessage('success', "Kategoriya tahrirlandi!");
        redirect('index.php');
    }
}

$pageTitle = "Kategoriya tahrirlash";
require_once '../../includes/layout_header.php';
?>
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6">🏷️ Kategoriya tahrirlash</h2>
        <form method="POST" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= generateCsrf() ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomi (UZ) *</label>
                <input type="text" name="name_uz" value="<?= htmlspecialchars($cat['name_uz']) ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Nomi (RU)</label>
                <input type="text" name="name_ru" value="<?= htmlspecialchars($cat['name_ru']) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Nomi (EN)</label>
                <input type="text" name="name_en" value="<?= htmlspecialchars($cat['name_en']) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                <input type="text" name="icon" value="<?= htmlspecialchars($cat['icon'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <input type="text" name="slug" value="<?= htmlspecialchars($cat['slug']) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none"></div>
            </div>
            <div class="pt-4 border-t border-gray-100 flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg">Saqlash</button>
                <a href="index.php" class="text-gray-500 hover:text-gray-800 font-medium py-2.5 px-4">Bekor</a>
            </div>
        </form>
    </div>
</div>
<?php require_once '../../includes/layout_footer.php'; ?>
