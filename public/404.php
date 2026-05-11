<?php
$pageTitle = "404 — Sahifa topilmadi";
require_once __DIR__ . '/includes/seo.php';
renderMeta(['title' => $pageTitle . ' | Silk Road Explorer']);
require_once __DIR__ . '/includes/layout_header.php';
?>

<section class="min-h-[70vh] flex items-center justify-center bg-gradient-to-b from-silk-50 to-white py-24">
    <div class="text-center px-6 max-w-lg mx-auto">
        <!-- 404 number -->
        <div class="relative mb-8">
            <span class="text-[10rem] md:text-[12rem] font-black text-silk-100 leading-none select-none block">404</span>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-24 h-24 bg-gradient-to-br from-amber-400 to-amber-600 rounded-3xl flex items-center justify-center shadow-2xl shadow-amber-500/30 rotate-12">
                    <span class="material-symbols-outlined text-white text-4xl -rotate-12">explore_off</span>
                </div>
            </div>
        </div>

        <h1 class="font-display text-3xl md:text-4xl font-bold text-silk-900 mb-4">Sahifa topilmadi</h1>
        <p class="text-silk-500 mb-10 leading-relaxed">Kechirasiz, siz qidirayotgan sahifa mavjud emas yoki boshqa joyga ko'chirilgan.</p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= BASE_URL ?>public/index.php" class="bg-gradient-to-r from-amber-500 to-amber-600 text-silk-950 px-8 py-3.5 rounded-xl font-bold text-sm hover:from-amber-400 hover:to-amber-500 transition-all shadow-xl shadow-amber-500/25 active:scale-95 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">home</span>
                Bosh sahifa
            </a>
            <a href="<?= BASE_URL ?>public/places/index.php" class="bg-white text-silk-700 px-8 py-3.5 rounded-xl font-bold text-sm border-2 border-silk-200 hover:border-amber-300 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">explore</span>
                Joylarni ko'rish
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/layout_footer.php'; ?>
