<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
requireAuth();

// O'chirish
if (isset($_GET['delete'])) {
    verifyCsrf();
    $delId = (int)$_GET['delete'];
    // Bog'liq joylar bormi tekshirish
    $check = $pdo->prepare("SELECT COUNT(*) FROM places WHERE category_id = ?");
    $check->execute([$delId]);
    if ((int)$check->fetchColumn() > 0) {
        flashMessage('error', "Bu kategoriyaga bog'liq joylar bor. Avval ularni o'zgartiring.");
    } else {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$delId]);
        flashMessage('success', "Kategoriya o'chirildi!");
    }
    redirect('index.php');
}

$categories = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM places WHERE category_id = c.id) as places_count FROM categories c ORDER BY c.id DESC")->fetchAll();

$pageTitle = 'Kategoriyalar';
require_once '../../includes/layout_header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">🏷️ Kategoriyalar</h1>
    <a href="create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium text-sm transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Yangi kategoriya
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left">
        <thead><tr class="bg-gray-50/80">
            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">ID</th>
            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Nomi (UZ)</th>
            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Nomi (RU)</th>
            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Nomi (EN)</th>
            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Icon</th>
            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Joylar</th>
            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Amallar</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
        <?php if (empty($categories)): ?>
            <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">Kategoriyalar yo'q</td></tr>
        <?php else: ?>
            <?php foreach ($categories as $cat): ?>
            <tr class="hover:bg-gray-50/80">
                <td class="px-5 py-3 text-sm text-gray-500 font-mono"><?= $cat['id'] ?></td>
                <td class="px-5 py-3 text-sm font-semibold text-gray-800"><?= htmlspecialchars($cat['name_uz']) ?></td>
                <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($cat['name_ru']) ?></td>
                <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($cat['name_en']) ?></td>
                <td class="px-5 py-3 text-sm text-gray-400"><?= htmlspecialchars($cat['icon'] ?? '—') ?></td>
                <td class="px-5 py-3"><span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full"><?= $cat['places_count'] ?></span></td>
                <td class="px-5 py-3 text-right">
                    <a href="edit.php?id=<?= $cat['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium mr-3">Tahrirlash</a>
                    <a href="index.php?delete=<?= $cat['id'] ?>&csrf_token=<?= generateCsrf() ?>" onclick="return confirm('O\'chirilsinmi?')" class="text-red-500 hover:text-red-700 text-sm font-medium">O'chirish</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../../includes/layout_footer.php'; ?>
