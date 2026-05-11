<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireAuth();

// Get filter params
$search = trim($_GET['search'] ?? '');
$categoryId = $_GET['category_id'] ?? '';
$regionId = $_GET['region_id'] ?? '';
$status = $_GET['status'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$perPage = 20;

$whereClauses = [];
$params = [];

if ($search !== '') {
    $whereClauses[] = "(p.name_uz LIKE :search OR p.name_ru LIKE :search OR p.name_en LIKE :search)";
    $params['search'] = "%{$search}%";
}
if ($categoryId !== '') {
    $whereClauses[] = "p.category_id = :category_id";
    $params['category_id'] = $categoryId;
}
if ($regionId !== '') {
    $whereClauses[] = "p.region_id = :region_id";
    $params['region_id'] = $regionId;
}
if ($status !== '') {
    $whereClauses[] = "p.status = :status";
    $params['status'] = $status;
}

$whereSql = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Count total rows
$countQuery = "SELECT COUNT(*) FROM places p $whereSql";
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

// Pagination
$paginator = paginate($total, $perPage, $page);

// Fetch places
$query = "
    SELECT p.id, p.name_uz, p.image, p.status, p.created_at,
           c.name_uz as category_name, r.name_uz as region_name
    FROM places p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN regions r ON p.region_id = r.id
    $whereSql
    ORDER BY p.id DESC
    LIMIT " . $paginator['offset'] . ", " . $perPage;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$places = $stmt->fetchAll();

// Fetch lookups for filters
$stmtCat = $pdo->prepare("SELECT id, name_uz FROM categories ORDER BY name_uz"); $stmtCat->execute(); $categories = $stmtCat->fetchAll();
$stmtReg = $pdo->prepare("SELECT id, name_uz FROM regions ORDER BY name_uz"); $stmtReg->execute(); $regions = $stmtReg->fetchAll();

$pageTitle = "Joylar ro'yxati";
require_once '../../includes/layout_header.php';
?>

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex-1 w-full">
        <form method="GET" action="" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Qidiruv..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none min-w-[200px]">
            
            <select name="category_id" class="px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                <option value="">Barcha kategoriyalar</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $categoryId == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name_uz']) ?></option>
                <?php endforeach; ?>
            </select>
            
            <select name="region_id" class="px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                <option value="">Barcha viloyatlar</option>
                <?php foreach ($regions as $reg): ?>
                    <option value="<?= $reg['id'] ?>" <?= $regionId == $reg['id'] ? 'selected' : '' ?>><?= htmlspecialchars($reg['name_uz']) ?></option>
                <?php endforeach; ?>
            </select>
            
            <select name="status" class="px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                <option value="">Barcha holatlar</option>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Faol (Active)</option>
                <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Qoralama (Draft)</option>
            </select>
            
            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-5 py-2 rounded-lg font-medium transition-colors">
                Filtrlash
            </button>
            <?php if ($search !== '' || $categoryId !== '' || $regionId !== '' || $status !== ''): ?>
                <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                    Tozalash
                </a>
            <?php endif; ?>
        </form>
    </div>
    
    <div>
        <a href="create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium transition-colors shadow-sm inline-flex items-center">
            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Yangi qo'shish
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-4">Rasm</th>
                    <th class="px-6 py-4">Nomi (UZ)</th>
                    <th class="px-6 py-4">Kategoriya / Viloyat</th>
                    <th class="px-6 py-4">Holati</th>
                    <th class="px-6 py-4">Sana</th>
                    <th class="px-6 py-4 text-right">Amallar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (count($places) > 0): ?>
                    <?php foreach ($places as $place): ?>
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <?= getImageHtml($place['image'] ?? '', $place['name_uz'] ?? 'X') ?>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-800">
                            <?= htmlspecialchars($place['name_uz']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div class="font-medium"><?= htmlspecialchars($place['category_name'] ?? '—') ?></div>
                            <div class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($place['region_name'] ?? '—') ?></div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <?php if ($place['status'] === 'active'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Faol</span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Qoralama</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?= date('d.m.Y H:i', strtotime($place['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end space-x-2">
                                <a href="edit.php?id=<?= $place['id'] ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Tahrirlash">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="delete.php" method="POST" class="inline-block" onsubmit="return confirm('Siz rostdan ham bu elementni o\'chirmoqchimisiz?');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrf()) ?>">
                                    <input type="hidden" name="id" value="<?= $place['id'] ?>">
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="O'chirish">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Hech qanday ma'lumot topilmadi.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php 
    $qs = $_GET;
    unset($qs['page']);
    $baseUrl = '?' . http_build_query($qs);
    $currentPage = $paginator['currentPage'];
    $totalPages = $paginator['totalPages'];
    require '../../includes/pagination.php';
    ?>
</div>

<?php require_once '../../includes/layout_footer.php'; ?>
