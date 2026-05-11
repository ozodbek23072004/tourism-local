<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAuth();

$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    if ($action === 'approve') {
        $pdo->prepare("UPDATE reviews SET status = 'approved' WHERE id = :id")->execute(['id' => $id]);
        header("Location: reviews.php?msg=approved"); exit;
    }
    if ($action === 'delete') {
        $pdo->prepare("DELETE FROM reviews WHERE id = :id")->execute(['id' => $id]);
        header("Location: reviews.php?msg=deleted"); exit;
    }
}

$status = $_GET['status'] ?? 'pending';
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE status = :status ORDER BY created_at DESC");
$stmt->execute(['status' => $status]);
$reviews = $stmt->fetchAll();

$pageTitle = 'Sharhlar boshqaruvi';
require_once '../includes/layout_header.php';
?>

<div class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-silk-900 mb-2"><?= $pageTitle ?></h1>
            <p class="text-silk-500">Foydalanuvchilar tomonidan qoldirilgan sharhlarni moderatsiya qilish.</p>
        </div>
        <div class="flex gap-2">
            <a href="?status=pending" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all <?= $status === 'pending' ? 'bg-amber-500 text-silk-950 shadow-lg' : 'bg-white text-silk-600 border border-silk-200' ?>">
                Kutilmoqda
            </a>
            <a href="?status=approved" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all <?= $status === 'approved' ? 'bg-amber-500 text-silk-950 shadow-lg' : 'bg-white text-silk-600 border border-silk-200' ?>">
                Tasdiqlangan
            </a>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="mb-6 p-4 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-200 text-sm font-medium">
            Amal muvaffaqiyatli bajarildi.
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-silk-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-silk-50 text-xs font-bold text-silk-400 uppercase tracking-wider">
                    <th class="px-6 py-4">Foydalanuvchi</th>
                    <th class="px-6 py-4">Bo'lim / ID</th>
                    <th class="px-6 py-4">Baho</th>
                    <th class="px-6 py-4">Sharh</th>
                    <th class="px-6 py-4">Sana</th>
                    <th class="px-6 py-4 text-right">Amallar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-silk-100">
                <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-silk-400 text-sm italic">
                            Hozircha sharhlar mavjud emas.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): ?>
                        <tr class="hover:bg-silk-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-silk-900 text-sm"><?= htmlspecialchars($rev['author_name']) ?></div>
                                <div class="text-[10px] text-silk-400 uppercase tracking-tighter">IP: <?= $rev['ip_address'] ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded bg-silk-100 text-silk-600 text-[10px] font-bold uppercase"><?= $rev['entity_type'] ?></span>
                                <span class="text-xs font-mono text-silk-400">#<?= $rev['entity_id'] ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-0.5">
                                    <?php for($i=1;$i<=5;$i++): ?>
                                        <span class="text-xs <?= $i <= $rev['rating'] ? 'text-amber-400' : 'text-silk-200' ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-silk-600 leading-relaxed max-w-md"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                            </td>
                            <td class="px-6 py-4 text-xs text-silk-400">
                                <?= date('d.m.Y H:i', strtotime($rev['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <?php if ($rev['status'] === 'pending'): ?>
                                        <a href="?action=approve&id=<?= $rev['id'] ?>&status=<?= $status ?>" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Tasdiqlash">
                                            <span class="material-symbols-outlined text-lg">check_circle</span>
                                        </a>
                                    <?php endif; ?>
                                    <a href="?action=delete&id=<?= $rev['id'] ?>&status=<?= $status ?>" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="O'chirish" onclick="return confirm('Ishonchingiz komilmi?')">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/layout_footer.php'; ?>
