<?php
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$baseUrl = $baseUrl ?? '?';

if ($totalPages <= 1) return;

$startPage = max(1, $currentPage - 2);
$endPage = min($totalPages, $startPage + 4);
if ($endPage - $startPage < 4) {
    $startPage = max(1, $endPage - 4);
}
?>
<div class="flex items-center justify-center gap-1.5">
    <?php if ($currentPage > 1): ?>
        <a href="<?= htmlspecialchars($baseUrl) . '&page=' . ($currentPage - 1) ?>" class="inline-flex items-center gap-1 px-4 py-2.5 border border-silk-200 rounded-xl text-sm font-medium bg-white text-silk-600 hover:border-amber-300 hover:text-amber-700 transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Oldingi
        </a>
    <?php endif; ?>

    <?php if ($startPage > 1): ?>
        <a href="<?= htmlspecialchars($baseUrl) . '&page=1' ?>" class="w-10 h-10 flex items-center justify-center border border-silk-200 rounded-xl text-sm font-medium bg-white text-silk-600 hover:border-amber-300 hover:text-amber-700 transition-all shadow-sm">1</a>
        <?php if ($startPage > 2): ?>
            <span class="w-10 h-10 flex items-center justify-center text-silk-300 text-sm">…</span>
        <?php endif; ?>
    <?php endif; ?>

    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
        <a href="<?= htmlspecialchars($baseUrl) . '&page=' . $i ?>" class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-semibold transition-all shadow-sm <?= $i === $currentPage ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-white border-amber-500 shadow-amber-500/20' : 'border border-silk-200 bg-white text-silk-600 hover:border-amber-300 hover:text-amber-700' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($endPage < $totalPages): ?>
        <?php if ($endPage < $totalPages - 1): ?>
            <span class="w-10 h-10 flex items-center justify-center text-silk-300 text-sm">…</span>
        <?php endif; ?>
        <a href="<?= htmlspecialchars($baseUrl) . '&page=' . $totalPages ?>" class="w-10 h-10 flex items-center justify-center border border-silk-200 rounded-xl text-sm font-medium bg-white text-silk-600 hover:border-amber-300 hover:text-amber-700 transition-all shadow-sm"><?= $totalPages ?></a>
    <?php endif; ?>

    <?php if ($currentPage < $totalPages): ?>
        <a href="<?= htmlspecialchars($baseUrl) . '&page=' . ($currentPage + 1) ?>" class="inline-flex items-center gap-1 px-4 py-2.5 border border-silk-200 rounded-xl text-sm font-medium bg-white text-silk-600 hover:border-amber-300 hover:text-amber-700 transition-all shadow-sm">
            Keyingi
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    <?php endif; ?>
</div>
