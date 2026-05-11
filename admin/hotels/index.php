<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireAuth();

$search = trim($_GET['search'] ?? '');
$regionId = $_GET['region_id'] ?? '';
$status = $_GET['status'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$perPage = 20;

$whereClauses = [];
$params = [];

if ($search !== '') {
    $whereClauses[] = "(h.name_uz LIKE :search)";
    $params['search'] = "%{$search}%";
}
if ($regionId !== '') {
    $whereClauses[] = "h.region_id = :region_id";
    $params['region_id'] = $regionId;
}
if ($status !== '') {
    $whereClauses[] = "h.status = :status";
    $params['status'] = $status;
}

$whereSql = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM hotels h $whereSql");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

$paginator = paginate($total, $perPage, $page);

$query = "
    SELECT h.id, h.name_uz, h.stars, h.price_from, h.image, h.status, h.created_at,
           reg.name_uz as region_name
    FROM hotels h
    LEFT JOIN regions reg ON h.region_id = reg.id
    $whereSql
    ORDER BY h.id DESC
    LIMIT " . $paginator['offset'] . ", " . $perPage;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$hotels = $stmt->fetchAll();

$stmtReg = $pdo->prepare("SELECT id, name_uz FROM regions ORDER BY name_uz"); $stmtReg->execute(); $regions = $stmtReg->fetchAll();

$pageTitle = "Mehmonxonalar";
require_once '../../includes/layout_header.php';
?>

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex-1 w-full">
        <form method="GET" action="" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Qidiruv..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            
            <select name="region_id" class="px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">Barcha viloyatlar</option>
                <?php foreach ($regions as $reg): ?>
                    <option value="<?= $reg['id'] ?>" <?= $regionId == $reg['id'] ? 'selected' : '' ?>><?= htmlspecialchars($reg['name_uz']) ?></option>
                <?php endforeach; ?>
            </select>
            
            <select name="status" class="px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">Barcha holatlar</option>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Faol</option>
                <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Qoralama</option>
            </select>
            
            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-5 py-2 rounded-lg font-medium">Filtrlash</button>
            <?php if ($search !== '' || $regionId !== '' || $status !== ''): ?>
                <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">Tozalash</a>
            <?php endif; ?>
        </form>
    </div>
    
    <div>
        <a href="create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium inline-flex items-center">
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
                    <th class="px-6 py-4">Nomi</th>
                    <th class="px-6 py-4">Yulduzlar</th>
                    <th class="px-6 py-4">Boshlang'ich narx</th>
                    <th class="px-6 py-4">Viloyat</th>
                    <th class="px-6 py-4">Holati</th>
                    <th class="px-6 py-4 text-right">Amallar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (count($hotels) > 0): ?>
                    <?php foreach ($hotels as $item): ?>
                    <tr class="hover:bg-gray-50/80 group">
                        <td class="px-6 py-4">
                            <?= getImageHtml($item['image'] ?? '', $item['name_uz'] ?? 'X') ?>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-800">
                            <?= htmlspecialchars($item['name_uz'] ?? '') ?>
                        </td>
                        <td class="px-6 py-4 text-yellow-500 text-lg">
                            <?= str_repeat('★', (int)$item['stars']) ?><span class="text-gray-300"><?= str_repeat('★', 5 - (int)$item['stars']) ?></span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-700">
                            $<?= number_format((float)$item['price_from'], 2) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <?= htmlspecialchars($item['region_name'] ?? '—') ?>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <?php if ($item['status'] === 'active'): ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Faol</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Qoralama</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end space-x-2">
                                <a href="edit.php?id=<?= $item['id'] ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">Tahrirlash</a>
                                <form action="delete.php" method="POST" class="inline-block" onsubmit="return confirm('O\'chirishni tasdiqlaysizmi?');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrf()) ?>">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg">O'chirish</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">Ma'lumot topilmadi.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
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
