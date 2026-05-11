<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAuth();

// ── Statusni o'zgartirish ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verifyCsrf();
    $bookingId = (int)($_POST['booking_id'] ?? 0);
    $action    = $_POST['action'];
    
    if ($bookingId > 0 && in_array($action, ['confirmed', 'cancelled', 'pending'])) {
        $stmt = $pdo->prepare("UPDATE bookings SET status = :status WHERE id = :id");
        $stmt->execute(['status' => $action, 'id' => $bookingId]);
        flashMessage('success', "Bron #{$bookingId} holati o'zgartirildi: {$action}");
    }
    
    // Sahifani qayta yuklash (POST redirect pattern)
    $redirectUrl = 'bookings.php';
    if (!empty($_POST['filter_status'])) $redirectUrl .= '?status=' . urlencode($_POST['filter_status']);
    redirect($redirectUrl);
}

// ── O'chirish ────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    verifyCsrf();
    $deleteId = (int)$_GET['delete'];
    if ($deleteId > 0) {
        $pdo->prepare("DELETE FROM bookings WHERE id = :id")->execute(['id' => $deleteId]);
        flashMessage('success', "Bron #{$deleteId} o'chirildi");
    }
    redirect('bookings.php');
}

// ── Filtr va pagination ──────────────────────────────────────────
$filterStatus = $_GET['status'] ?? '';
$filterType   = $_GET['type'] ?? '';
$page         = max(1, (int)($_GET['page'] ?? 1));
$perPage      = 15;

$where = [];
$params = [];

if (in_array($filterStatus, ['pending', 'confirmed', 'cancelled'])) {
    $where[] = "b.status = :status";
    $params['status'] = $filterStatus;
}
if (in_array($filterType, ['hotel', 'restaurant'])) {
    $where[] = "b.entity_type = :type";
    $params['type'] = $filterType;
}

$whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Umumiy son
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM bookings b {$whereSql}");
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$paginator = paginate($totalRows, $perPage, $page);

// Bronlar ro'yxati
$sql = "
    SELECT b.*,
        CASE 
            WHEN b.entity_type = 'hotel' THEN (SELECT name_uz FROM hotels WHERE id = b.entity_id)
            WHEN b.entity_type = 'restaurant' THEN (SELECT name_uz FROM restaurants WHERE id = b.entity_id)
        END as entity_name
    FROM bookings b
    {$whereSql}
    ORDER BY 
        CASE b.status WHEN 'pending' THEN 0 WHEN 'confirmed' THEN 1 ELSE 2 END,
        b.created_at DESC
    LIMIT {$perPage} OFFSET {$paginator['offset']}
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

// Statistikalar
$stats = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(status = 'pending') as pending,
        SUM(status = 'confirmed') as confirmed,
        SUM(status = 'cancelled') as cancelled
    FROM bookings
")->fetch();

$csrfToken = generateCsrf();
$pageTitle = 'Bronlar boshqaruvi';
require_once '../includes/layout_header.php';
?>

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">🗓️ Bronlar boshqaruvi</h1>
        <p class="text-gray-500 text-sm mt-1">Mehmonxona va restoranlar uchun kelgan bronlar</p>
    </div>
</div>

<!-- Flash message -->
<?php $flash = getFlash('success'); if ($flash): ?>
<div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    <?= htmlspecialchars($flash) ?>
</div>
<?php endif; ?>

<!-- Statistik kartalar -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Jami</p>
        <p class="text-3xl font-bold text-gray-800"><?= $stats['total'] ?></p>
    </div>
    <a href="?status=pending" class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow <?= $filterStatus === 'pending' ? 'ring-2 ring-amber-400' : '' ?>">
        <p class="text-xs font-semibold text-amber-500 uppercase tracking-wider mb-1">Kutilmoqda</p>
        <p class="text-3xl font-bold text-amber-600"><?= $stats['pending'] ?></p>
    </a>
    <a href="?status=confirmed" class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow <?= $filterStatus === 'confirmed' ? 'ring-2 ring-green-400' : '' ?>">
        <p class="text-xs font-semibold text-green-500 uppercase tracking-wider mb-1">Tasdiqlangan</p>
        <p class="text-3xl font-bold text-green-600"><?= $stats['confirmed'] ?></p>
    </a>
    <a href="?status=cancelled" class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow <?= $filterStatus === 'cancelled' ? 'ring-2 ring-red-400' : '' ?>">
        <p class="text-xs font-semibold text-red-500 uppercase tracking-wider mb-1">Bekor qilingan</p>
        <p class="text-3xl font-bold text-red-600"><?= $stats['cancelled'] ?></p>
    </a>
</div>

<!-- Filtrlar -->
<div class="flex flex-wrap gap-2 mb-6">
    <a href="bookings.php" class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?= empty($filterStatus) && empty($filterType) ? 'bg-gray-800 text-white shadow' : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-400' ?>">Barchasi</a>
    <a href="?type=hotel" class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?= $filterType === 'hotel' ? 'bg-purple-600 text-white shadow' : 'bg-white text-gray-600 border border-gray-200 hover:border-purple-400' ?>">🏨 Mehmonxonalar</a>
    <a href="?type=restaurant" class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?= $filterType === 'restaurant' ? 'bg-emerald-600 text-white shadow' : 'bg-white text-gray-600 border border-gray-200 hover:border-emerald-400' ?>">🍽️ Restoranlar</a>
</div>

<!-- Bronlar jadvali -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/80">
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mehmon</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Joy</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sana</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mehmonlar</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Holat</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Amallar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (empty($bookings)): ?>
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center text-gray-400">
                        <svg class="mx-auto h-12 w-12 text-gray-200 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-sm">Hozircha bronlar yo'q</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($bookings as $b): ?>
                <tr class="hover:bg-gray-50/80 transition-colors">
                    <td class="px-5 py-4 text-sm text-gray-500 font-mono">#<?= $b['id'] ?></td>
                    <td class="px-5 py-4">
                        <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($b['guest_name']) ?></p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $b['guest_phone']) ?>" class="text-blue-500 hover:underline"><?= htmlspecialchars($b['guest_phone']) ?></a>
                            <?php if ($b['guest_email']): ?>
                                · <a href="mailto:<?= htmlspecialchars($b['guest_email']) ?>" class="text-blue-500 hover:underline"><?= htmlspecialchars($b['guest_email']) ?></a>
                            <?php endif; ?>
                        </p>
                        <?php if ($b['message']): ?>
                        <p class="text-xs text-gray-400 mt-1 max-w-xs truncate" title="<?= htmlspecialchars($b['message']) ?>">💬 <?= htmlspecialchars(mb_substr($b['message'], 0, 60)) ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-800">
                            <?= $b['entity_type'] === 'hotel' ? '🏨' : '🍽️' ?>
                            <?= htmlspecialchars($b['entity_name'] ?? 'Noma\'lum') ?>
                        </span>
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-600">
                        <p><?= date('d.m.Y', strtotime($b['check_in'])) ?></p>
                        <?php if ($b['check_out']): ?>
                        <p class="text-xs text-gray-400">→ <?= date('d.m.Y', strtotime($b['check_out'])) ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-600 text-center"><?= $b['guests_count'] ?></td>
                    <td class="px-5 py-4">
                        <?php
                        $statusStyles = [
                            'pending'   => 'bg-amber-100 text-amber-700',
                            'confirmed' => 'bg-green-100 text-green-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                        ];
                        $statusLabels = [
                            'pending'   => 'Kutilmoqda',
                            'confirmed' => 'Tasdiqlangan',
                            'cancelled' => 'Bekor qilingan',
                        ];
                        $st = $b['status'];
                        ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?= $statusStyles[$st] ?? '' ?>">
                            <?= $statusLabels[$st] ?? $st ?>
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <?php if ($st !== 'confirmed'): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                <input type="hidden" name="action" value="confirmed">
                                <input type="hidden" name="filter_status" value="<?= htmlspecialchars($filterStatus) ?>">
                                <button type="submit" class="p-2 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-colors" title="Tasdiqlash">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </form>
                            <?php endif; ?>
                            <?php if ($st !== 'cancelled'): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                <input type="hidden" name="action" value="cancelled">
                                <input type="hidden" name="filter_status" value="<?= htmlspecialchars($filterStatus) ?>">
                                <button type="submit" class="p-2 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition-colors" title="Bekor qilish">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                            <?php endif; ?>
                            <a href="bookings.php?delete=<?= $b['id'] ?>&csrf_token=<?= $csrfToken ?>" 
                               onclick="return confirm('Bu bronni o\'chirmoqchimisiz?')" 
                               class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-red-500 transition-colors" title="O'chirish">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($paginator['totalPages'] > 1): ?>
    <div class="px-5 py-4 border-t border-gray-100 flex justify-center">
        <?php
        $qs = $_GET;
        unset($qs['page']);
        $baseUrl = '?' . http_build_query($qs);
        $currentPage = $paginator['currentPage'];
        $totalPages = $paginator['totalPages'];
        require '../includes/pagination.php';
        ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/layout_footer.php'; ?>
