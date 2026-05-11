<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../includes/functions.php';

$categoryId = $_GET['category_id'] ?? '';
$regionId = $_GET['region_id'] ?? '';
$search = $_GET['search'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$perPage = 15; // increased for masonry

$filters = [
    'category_id' => $categoryId,
    'region_id' => $regionId,
    'search' => $search
];

$totalRows = countRows($pdo, 'places', $filters);
$paginator = paginate($totalRows, $perPage, $page);
$places = getPlaces($pdo, $filters, $perPage, $paginator['offset']);

// Hero slider images
$heroImages = getHeroImages($pdo, 'places');
if (empty($heroImages)) {
    $heroImages = ['https://images.unsplash.com/photo-1548698517-c87d6023cb36?q=80&w=2500'];
}

$stmtCat = $pdo->prepare("SELECT id, name_uz, name_ru, name_en, icon FROM categories ORDER BY name_uz");
$stmtCat->execute();
$categories = $stmtCat->fetchAll();

$stmtReg = $pdo->prepare("SELECT id, name_uz, name_ru, name_en FROM regions ORDER BY name_uz");
$stmtReg->execute();
$regions = $stmtReg->fetchAll();

$lang = currentLang();
$pageTitle = __('nav_places');
require_once '../includes/seo.php';
renderMeta(['title' => $pageTitle . " | Silk Road Explorer"]);
require_once '../includes/layout_header.php';
?>

<!-- Import Google Fonts for Luxury Editorial Look -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Inter', sans-serif; background-color: #fcf9f4; color: #1c1c19; }
    .font-editorial { font-family: 'Noto Serif', serif; }
    .color-lapis { color: #1b3a6b; }
    .bg-lapis { background-color: #1b3a6b; }
    .color-gold { color: #ffc256; }
    .bg-gold { background-color: #ffc256; }
    
    /* Masonry Layout Setup */
    .masonry-grid {
        column-count: 1;
        column-gap: 1.5rem;
    }
    @media (min-width: 768px) { .masonry-grid { column-count: 2; } }
    @media (min-width: 1280px) { .masonry-grid { column-count: 3; } }
    
    .masonry-item {
        break-inside: avoid;
        margin-bottom: 1.5rem;
    }

    /* Micro-interactions */
    .hover-scale { transition: transform 300ms ease; }
    .hover-scale:hover { transform: scale(1.03); }
    
    /* Underline Animation */
    .animated-underline {
        position: relative;
        display: inline-block;
    }
    .animated-underline::after {
        content: '';
        position: absolute;
        width: 100%;
        transform: scaleX(0);
        height: 2px;
        bottom: 0;
        left: 0;
        background-color: #ffc256;
        transform-origin: bottom right;
        transition: transform 0.4s cubic-bezier(0.86, 0, 0.07, 1);
    }
    .animated-underline:hover::after {
        transform: scaleX(1);
        transform-origin: bottom left;
    }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #fcf9f4; }
    ::-webkit-scrollbar-thumb { background: #ffc256; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #7e5700; }
</style>

<!-- Full Viewport Cinematic Hero -->
<section class="relative h-screen w-full flex items-center justify-center overflow-hidden">
    <!-- Cinematic Background Slider -->
    <div class="absolute inset-0 z-0 bg-[#1A0E05]">
        <img id="hero-bg-img" src="<?= $heroImages[0] ?>" 
             alt="Silk Road" 
             class="w-full h-full object-cover transform scale-105 motion-safe:animate-[pulse_20s_ease-in-out_infinite] origin-center transition-opacity duration-1000 ease-in-out">
    </div>
    <script>
        (function() {
            const images = <?= json_encode($heroImages) ?>;
            let active = 0;
            const bgImg = document.getElementById('hero-bg-img');
            if (images.length > 1) {
                setInterval(() => {
                    active = (active + 1) % images.length;
                    if (bgImg) bgImg.src = images[active];
                }, 5000);
            }
        })();
    </script>
    
    <!-- Dark gradient overlay (bottom 60% fades to near-black lapis) -->
    <div class="absolute inset-0 z-0 bg-gradient-to-t from-[#1B3A6B]/90 via-[#1B3A6B]/40 to-black/20"></div>

    <div class="relative z-10 text-center px-4 max-w-5xl mx-auto w-full flex flex-col items-center mt-16">
        <!-- Breadcrumbs as Journey Path -->
        <div class="flex items-center gap-3 text-white/70 text-xs tracking-widest uppercase font-semibold mb-8" data-aos="fade-down">
            <a href="<?= BASE_URL ?>public/index.php" class="hover:text-[#ffc256] transition-colors">Explorer</a>
            <span class="w-1.5 h-1.5 rounded-full bg-[#ffc256]"></span>
            <span class="text-white"><?= __('nav_places') ?></span>
        </div>

        <h1 class="font-editorial text-6xl md:text-7xl lg:text-[80px] font-bold text-white mb-6 leading-tight drop-shadow-2xl" data-aos="fade-up" data-aos-delay="100">
            <span class="animated-underline pb-2"><?= __('nav_places') ?></span>
        </h1>
        
        <p class="font-editorial italic text-[#ffc256] text-xl md:text-2xl mb-10 drop-shadow-md max-w-3xl" data-aos="fade-up" data-aos-delay="200">
            "Sayohat – bu o‘zlikni anglash uchun qo‘yilgan qadamdir."
        </p>

        <!-- Glassmorphism Search Bar -->
        <div class="w-full flex justify-center" data-aos="fade-up" data-aos-delay="400">
            <form action="index.php" method="GET" class="w-full max-w-2xl relative">
                <div class="flex items-center bg-white/15 backdrop-blur-xl border border-white/30 rounded-full overflow-hidden shadow-2xl p-1.5 transition-transform duration-300 focus-within:scale-105 focus-within:bg-white/25">
                    <span class="material-symbols-outlined text-white/70 ml-4 mr-2 text-xl">search</span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="<?= __('search_placeholder') ?>..." 
                           class="flex-1 bg-transparent text-white placeholder-white/60 py-2.5 text-base focus:outline-none border-none font-medium">
                    <?php if($categoryId): ?><input type="hidden" name="category_id" value="<?= htmlspecialchars($categoryId) ?>"><?php endif; ?>
                    <?php if($regionId): ?><input type="hidden" name="region_id" value="<?= htmlspecialchars($regionId) ?>"><?php endif; ?>
                    <button type="submit" class="bg-[#ffc256] hover:bg-[#7e5700] text-[#1c1c19] hover:text-white px-6 py-2.5 rounded-full font-bold transition-all shadow-lg flex items-center gap-2 text-sm">
                        Izlash
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Main Experience Layout -->
<section class="py-20 relative z-20 bg-[#FAF7F2]">
    <div class="w-full max-w-[1600px] px-6 lg:px-10 xl:px-16 mx-auto" id="places-container">
        
        <!-- Regions Full-Bleed Scroll (Mini versions, since detailed regions need 400px, we'll make them horizontal pills for filters to save space here) -->
        <div class="mb-16 border-b border-[#1B3A6B]/10 pb-8" data-aos="fade-up">
            <h2 class="font-editorial text-3xl color-lapis font-bold mb-6">Manzillar</h2>
            <div class="flex items-center gap-3 overflow-x-auto pb-4 no-scrollbar">
                <a href="?<?= $categoryId ? 'category_id='.$categoryId.'&' : '' ?><?= $search ? 'search='.urlencode($search) : '' ?>" 
                   class="shrink-0 px-6 py-2.5 rounded-full text-sm transition-all border <?= empty($regionId) ? 'border-[#1b3a6b] bg-[#1b3a6b] text-white font-semibold shadow-md' : 'border-[#1b3a6b]/20 bg-white color-lapis hover:border-[#1b3a6b]/50' ?>">
                    Barchasi
                </a>
                <?php foreach($regions as $reg): ?>
                    <a href="?region_id=<?= $reg['id'] ?><?= $categoryId ? '&category_id='.$categoryId : '' ?><?= $search ? '&search='.urlencode($search) : '' ?>" 
                       class="shrink-0 px-6 py-2.5 rounded-full text-sm transition-all border <?= $regionId == $reg['id'] ? 'border-[#1b3a6b] bg-[#1b3a6b] text-white font-semibold shadow-md' : 'border-[#1b3a6b]/20 bg-white color-lapis hover:border-[#1b3a6b]/50' ?>">
                        <?= htmlspecialchars(localizedField($reg, 'name')) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-12">
            
            <!-- Left Sidebar: Icon-Forward Category Cards -->
            <aside class="w-full lg:w-1/4 shrink-0" data-aos="fade-right">
                <div class="sticky top-32">
                    <h3 class="font-editorial text-2xl color-lapis font-bold mb-6 flex items-center gap-2">
                        Tafsilotlar
                    </h3>
                    
                    <div class="grid grid-cols-2 lg:grid-cols-1 gap-4">
                        <!-- All Categories Card -->
                        <a href="?<?= $regionId ? 'region_id='.$regionId.'&' : '' ?><?= $search ? 'search='.urlencode($search) : '' ?>" 
                           class="group flex items-center p-4 bg-white rounded-2xl border border-[#1b3a6b]/10 hover:border-[#ffc256] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 <?= empty($categoryId) ? 'ring-2 ring-[#ffc256] shadow-lg' : '' ?>">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 <?= empty($categoryId) ? 'bg-[#ffc256] text-[#1c1c19]' : 'bg-[#fcf9f4] color-lapis group-hover:bg-[#ffc256] group-hover:text-[#1c1c19] transition-colors' ?>">
                                <span class="material-symbols-outlined">explore</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold <?= empty($categoryId) ? 'color-lapis' : 'text-gray-600 group-hover:color-lapis' ?>">Barchasi</h4>
                            </div>
                        </a>

                        <?php foreach($categories as $cat): ?>
                        <a href="?category_id=<?= $cat['id'] ?><?= $regionId ? '&region_id='.$regionId : '' ?><?= $search ? '&search='.urlencode($search) : '' ?>" 
                           class="group flex items-center p-4 bg-white rounded-2xl border border-[#1b3a6b]/10 hover:border-[#ffc256] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 <?= $categoryId == $cat['id'] ? 'ring-2 ring-[#ffc256] shadow-lg' : '' ?>">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 <?= $categoryId == $cat['id'] ? 'bg-[#ffc256] text-[#1c1c19]' : 'bg-[#fcf9f4] color-lapis group-hover:bg-[#ffc256] group-hover:text-[#1c1c19] transition-colors' ?>">
                                <?php if(!empty($cat['icon'])): ?>
                                    <span class="material-symbols-outlined"><?= htmlspecialchars($cat['icon']) ?></span>
                                <?php else: ?>
                                    <span class="material-symbols-outlined">account_balance</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-sm <?= $categoryId == $cat['id'] ? 'color-lapis' : 'text-gray-600 group-hover:color-lapis' ?>">
                                    <?= htmlspecialchars(localizedField($cat, 'name')) ?>
                                </h4>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>

            <!-- Right Content: Masonry Layout -->
            <main class="w-full lg:w-3/4">
                
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-[#1B3A6B]/10">
                    <span class="color-lapis font-semibold text-lg">
                        Natija: <span class="color-gold font-bold"><?= $totalRows ?></span> ta maskan
                    </span>
                    
                    <?php if(!empty($search) || !empty($categoryId) || !empty($regionId)): ?>
                        <a href="index.php" class="text-sm text-red-500 hover:text-red-700 font-medium flex items-center gap-1 transition-colors bg-red-50 px-3 py-1.5 rounded-full">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                            Tozalash
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (empty($places)): ?>
                    <div class="bg-white p-20 rounded-3xl border border-[#1B3A6B]/10 text-center shadow-lg" data-aos="zoom-in">
                        <span class="material-symbols-outlined text-6xl text-[#1B3A6B]/20 mb-6 block">auto_awesome_mosaic</span>
                        <h3 class="font-editorial text-3xl font-bold color-lapis mb-4"><?= __('no_results') ?></h3>
                        <p class="text-gray-500 mb-8 text-lg"><?= __('try_other_params') ?></p>
                        <a href="index.php" class="inline-flex items-center gap-2 bg-[#1B3A6B] text-white px-8 py-3 rounded-full font-bold hover:bg-[#1B3A6B]/90 transition-colors shadow-xl">
                            Qaytadan izlash
                        </a>
                    </div>
                <?php else: ?>
                    
                    <!-- CSS Masonry Grid -->
                    <div class="masonry-grid">
                        <?php 
                        // Assign deterministic random heights for masonry effect
                        $heights = ['h-[350px]', 'h-[450px]', 'h-[400px]', 'h-[500px]', 'h-[380px]'];
                        foreach($places as $i => $place): 
                            $hClass = $heights[$i % count($heights)];
                            $pName = localizedField($place, 'name');
                            $catName = localizedField($place, 'category_name');
                            
                            // Dynamic category badge colors
                            $badgeColor = 'bg-teal-600/90'; // Default
                            if(stripos($catName, 'tarix') !== false || stripos($catName, 'history') !== false) $badgeColor = 'bg-[#C9922A]/90';
                            if(stripos($catName, 'tabiat') !== false || stripos($catName, 'nature') !== false) $badgeColor = 'bg-emerald-600/90';
                        ?>
                            <div class="masonry-item relative rounded-3xl overflow-hidden group cursor-pointer shadow-sm hover:shadow-2xl transition-all duration-500 <?= $hClass ?>" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 100 ?>">
                                
                                <!-- Full Bleed Image -->
                                <img loading="lazy" src="<?= publicImage($place['image'], $pName) ?>" alt="<?= htmlspecialchars($pName) ?>" 
                                     class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out">
                                
                                <!-- Dark Gradient Overlay for Text -->
                                <div class="absolute inset-0 bg-gradient-to-t from-[#1B3A6B] via-[#1B3A6B]/40 to-transparent opacity-80 group-hover:opacity-90 transition-opacity duration-300"></div>
                                
                                <!-- Category Badge (Top Left) -->
                                <?php if($catName): ?>
                                <div class="absolute top-5 left-5 <?= $badgeColor ?> backdrop-blur-md px-4 py-1.5 rounded-full text-xs font-bold text-white uppercase tracking-widest shadow-lg">
                                    <?= htmlspecialchars($catName) ?>
                                </div>
                                <?php endif; ?>

                                <!-- Content Overlay (Bottom) -->
                                <div class="absolute bottom-0 left-0 right-0 p-6 flex flex-col justify-end">
                                    <h3 class="font-editorial font-bold text-white text-2xl lg:text-3xl mb-2 leading-tight drop-shadow-lg">
                                        <?= htmlspecialchars($pName) ?>
                                    </h3>
                                    
                                    <div class="flex items-center text-[#fcf9f4]/80 text-sm font-medium mb-0 group-hover:mb-4 transition-all duration-300">
                                        <span class="material-symbols-outlined text-[18px] text-[#ffc256] mr-1.5">location_on</span>
                                        <?= htmlspecialchars(localizedField($place, 'region_name') ?: __('unknown')) ?>
                                    </div>
                                    
                                    <!-- Slide up CTA Button -->
                                    <div class="overflow-hidden h-0 group-hover:h-12 transition-all duration-300 opacity-0 group-hover:opacity-100 flex items-center">
                                        <a href="view.php?id=<?= $place['id'] ?>" class="inline-flex items-center justify-center w-full bg-[#1b3a6b] text-white py-3 rounded-xl font-bold hover:bg-[#002452] transition-colors shadow-xl">
                                            Sayohat qilish
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($paginator['totalPages'] > 1): ?>
                        <div class="flex justify-center mt-12" data-aos="fade-up">
                            <?php 
                            $qs = $_GET;
                            unset($qs['page']);
                            $baseUrl = '?' . http_build_query($qs);
                            $currentPage = $paginator['currentPage'];
                            $totalPages = $paginator['totalPages'];
                            require '../../includes/pagination.php';
                            ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
            </main>
        </div>
    </div>
</section>

<!-- Intersection Observer Fade-in trigger (Fallback for AOS if needed) -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // AJAX Filtering
    document.body.addEventListener('click', function(e) {
        let url = null;
        
        const filterLink = e.target.closest('a[href^="?"]');
        if (filterLink && filterLink.closest('#places-container')) {
            url = filterLink.href;
        } else {
            const clearLink = e.target.closest('a[href="index.php"]');
            if (clearLink && clearLink.closest('#places-container')) {
                url = clearLink.href;
            }
        }

        if (url) {
            e.preventDefault();
            fetchContent(url);
        }
    });

    function fetchContent(url) {
        const container = document.getElementById('places-container');
        if (container) container.style.opacity = '0.5';

        fetch(url)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                if (container && doc.getElementById('places-container')) {
                    container.innerHTML = doc.getElementById('places-container').innerHTML;
                    container.style.opacity = '1';
                }

                if (typeof AOS !== 'undefined') {
                    setTimeout(() => AOS.refreshHard(), 50);
                }
                
                // history.pushState(null, '', url); // Removed so URL doesn't change to ?region_id=...
            })
            .catch(err => {
                console.error("AJAX Error:", err);
                container.style.opacity = '1';
                // Don't fallback to window.location.href to prevent page reload
            });
    }
});
</script>

<?php require_once '../includes/layout_footer.php'; ?>
