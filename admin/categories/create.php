<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
requireAuth();

$errors = [];
$cat = ['name_uz' => '', 'name_ru' => '', 'name_en' => '', 'icon' => '', 'slug' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $cat['name_uz'] = sanitize($_POST['name_uz'] ?? '');
    $cat['name_ru'] = sanitize($_POST['name_ru'] ?? '');
    $cat['name_en'] = sanitize($_POST['name_en'] ?? '');
    $cat['icon']    = sanitize($_POST['icon'] ?? '');
    $cat['slug']    = sanitize($_POST['slug'] ?? '');

    if (empty($cat['name_uz'])) $errors['name_uz'] = 'Nomi (UZ) majburiy';
    if (empty($cat['slug'])) $cat['slug'] = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $cat['name_en'] ?: $cat['name_uz']));

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name_uz, name_ru, name_en, icon, slug) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$cat['name_uz'], $cat['name_ru'], $cat['name_en'], $cat['icon'] ?: null, $cat['slug']]);
        flashMessage('success', "Kategoriya qo'shildi!");
        redirect('index.php');
    }
}

$pageTitle = "Yangi kategoriya";
require_once '../../includes/layout_header.php';
?>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6">🏷️ Yangi kategoriya</h2>
        <form method="POST" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= generateCsrf() ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomi (UZ) *</label>
                <input type="text" name="name_uz" value="<?= htmlspecialchars($cat['name_uz']) ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none">
                <?php if (isset($errors['name_uz'])): ?><p class="text-red-500 text-xs mt-1"><?= $errors['name_uz'] ?></p><?php endif; ?>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomi (RU)</label>
                    <input type="text" name="name_ru" value="<?= htmlspecialchars($cat['name_ru']) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomi (EN)</label>
                    <input type="text" name="name_en" value="<?= htmlspecialchars($cat['name_en']) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Material icon nomi)</label>
                    <input type="text" name="icon" value="<?= htmlspecialchars($cat['icon']) ?>" placeholder="museum, mosque, castle..." class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="<?= htmlspecialchars($cat['slug']) ?>" placeholder="avtomatik" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none">
                </div>
            </div>
            <div class="pt-4 border-t border-gray-100 flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors">Saqlash</button>
                <a href="index.php" class="text-gray-500 hover:text-gray-800 font-medium py-2.5 px-4">Bekor qilish</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/layout_footer.php'; ?>
