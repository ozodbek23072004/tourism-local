<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
requireAuth();

if (isset($_GET['delete'])) {
    verifyCsrf();
    $delId = (int)$_GET['delete'];
    
    // Bog'liqlikni tekshirish
    $count = 0;
    $count += $pdo->prepare("SELECT COUNT(*) FROM places WHERE region_id=?")->execute([$delId]) ? $pdo->query("SELECT COUNT(*) FROM places WHERE region_id=$delId")->fetchColumn() : 0;
    $count += $pdo->prepare("SELECT COUNT(*) FROM people WHERE region_id=?")->execute([$delId]) ? $pdo->query("SELECT COUNT(*) FROM people WHERE region_id=$delId")->fetchColumn() : 0;
    $count += $pdo->prepare("SELECT COUNT(*) FROM hotels WHERE region_id=?")->execute([$delId]) ? $pdo->query("SELECT COUNT(*) FROM hotels WHERE region_id=$delId")->fetchColumn() : 0;
    $count += $pdo->prepare("SELECT COUNT(*) FROM restaurants WHERE region_id=?")->execute([$delId]) ? $pdo->query("SELECT COUNT(*) FROM restaurants WHERE region_id=$delId")->fetchColumn() : 0;
    
    if ($count > 0) {
        flashMessage('error', "Bu viloyatga bog'liq ma'lumotlar bor. Avval ularni o'zgartiring.");
    } else {
        $reg = $pdo->query("SELECT image FROM regions WHERE id=$delId")->fetch();
        if ($reg && $reg['image']) deleteImage($reg['image']);
        $pdo->prepare("DELETE FROM regions WHERE id = ?")->execute([$delId]);
        flashMessage('success', "Viloyat o'chirildi!");
    }
    redirect('index.php');
}

$regions = $pdo->query("
    SELECT r.*, 
    (SELECT COUNT(*) FROM places WHERE region_id=r.id) as p_count,
    (SELECT COUNT(*) FROM hotels WHERE region_id=r.id) as h_count
    FROM regions r ORDER BY r.id DESC
")->fetchAll();

$pageTitle = 'Viloyatlar';
require_once '../../includes/layout_header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">🗺️ Viloyatlar</h1>
    <a href="create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium text-sm transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Yangi viloyat
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left">
        <thead><tr class="bg-gray-50/80">
            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">ID</th>
            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Nomi (UZ)</th>
            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Nomi (RU)</th>
            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Nomi (EN)</th>
            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Rasm</th>
            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Joylar / Mehmonxonalar</th>
            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Amallar</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
        <?php if (empty($regions)): ?>
            <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">Viloyatlar yo'q</td></tr>
        <?php else: ?>
            <?php foreach ($regions as $r): ?>
            <tr class="hover:bg-gray-50/80">
                <td class="px-5 py-3 text-sm text-gray-500 font-mono"><?= $r['id'] ?></td>
                <td class="px-5 py-3 text-sm font-semibold text-gray-800"><?= htmlspecialchars($r['name_uz']) ?></td>
                <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($r['name_ru']) ?></td>
                <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($r['name_en']) ?></td>
                <td class="px-5 py-3 text-sm text-gray-400">
                    <?php if ($r['image']): ?>
                        <div class="w-10 h-10 rounded overflow-hidden"><img src="<?= publicImage($r['image'], $r['name_uz']) ?>" class="w-full h-full object-cover"></div>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td class="px-5 py-3">
                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full mr-1" title="Joylar"><?= $r['p_count'] ?></span>
                    <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full" title="Mehmonxonalar"><?= $r['h_count'] ?></span>
                </td>
                <td class="px-5 py-3 text-right">
                    <a href="edit.php?id=<?= $r['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium mr-3">Tahrirlash</a>
                    <a href="index.php?delete=<?= $r['id'] ?>&csrf_token=<?= generateCsrf() ?>" onclick="return confirm('O\'chirilsinmi?')" class="text-red-500 hover:text-red-700 text-sm font-medium">O'chirish</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require_once '../../includes/layout_footer.php'; ?>
