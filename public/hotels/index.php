<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../includes/functions.php';

$regionId = $_GET['region_id'] ?? '';
$stars = $_GET['stars'] ?? '';
$search = $_GET['search'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$perPage = 12;

$filters = ['region_id' => $regionId, 'stars' => $stars, 'search' => $search];
$totalRows = countRows($pdo, 'hotels', $filters);
$paginator = paginate($totalRows, $perPage, $page);
$hotels = getHotels($pdo, $filters, $perPage, $paginator['offset']);

// Hero slider images
$heroImages = getHeroImages($pdo, 'hotels');
if (empty($heroImages)) {
    $heroImages = ['https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2500'];
}

$stmtReg = $pdo->prepare("SELECT id, name_uz, name_ru, name_en FROM regions ORDER BY name_uz");
$stmtReg->execute();
$regions = $stmtReg->fetchAll();

$lang = currentLang();
$pageTitle = __('nav_hotels');
require_once '../includes/seo.php';
renderMeta(['title' => $pageTitle . ' | Silk Road Explorer']);
require_once '../includes/layout_header.php';

function renderStars($count) {
    $count = (int)$count;
    $html = '';
    for($i = 1; $i <= 5; $i++) {
        $color = $i <= $count ? 'text-[#C9922A]' : 'text-gray-300';
        $html .= '<svg class="w-4 h-4 ' . $color . ' drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
    }
    return '<div class="flex items-center gap-0.5">' . $html . '</div>';
}
?>

<!-- Import Google Fonts for Luxury Editorial Look -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Inter', sans-serif; background-color: #FAF7F2; color: #1A0E05; }
    .font-editorial { font-family: 'Noto Serif', serif; }
    
    .animated-underline { position: relative; display: inline-block; }
    .animated-underline::after {
        content: ''; position: absolute; width: 100%; transform: scaleX(0); height: 2px; bottom: 0; left: 0;
        background-color: #C9922A; transform-origin: bottom right; transition: transform 0.4s cubic-bezier(0.86, 0, 0.07, 1);
    }
    .animated-underline:hover::after { transform: scaleX(1); transform-origin: bottom left; }

    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<!-- Full Viewport Cinematic Hero -->
<section class="relative h-[70vh] min-h-[500px] w-full flex items-center justify-center overflow-hidden">
    <!-- Cinematic Background Slider -->
    <div class="absolute inset-0 z-0 bg-[#1A0E05]">
        <img id="hero-bg-img" src="<?= $heroImages[0] ?>" 
             alt="Luxury Hotel" 
             class="w-full h-full object-cover transform scale-105 motion-safe:animate-[pulse_20s_ease-in-out_infinite] opacity-60 blend-luminosity transition-opacity duration-1000 ease-in-out">
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
    
    <!-- Elegant Gradient Overlay -->
    <div class="absolute inset-0 z-0 bg-gradient-to-t from-[#1A0E05] via-[#1A0E05]/40 to-transparent"></div>
    <div class="absolute inset-0 z-0 bg-gradient-to-r from-[#1B3A6B]/80 to-transparent"></div>

    <div class="relative z-10 text-center px-4 max-w-5xl mx-auto w-full flex flex-col items-center mt-12">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-3 text-white/70 text-xs tracking-widest uppercase font-semibold mb-6" data-aos="fade-down">
            <a href="<?= BASE_URL ?>public/index.php" class="hover:text-[#C9922A] transition-colors">Explorer</a>
            <span class="w-1.5 h-1.5 rounded-full bg-[#C9922A]"></span>
            <span class="text-white"><?= __('nav_hotels') ?></span>
        </div>

        <h1 class="font-editorial text-5xl md:text-7xl font-bold text-white mb-6 leading-tight drop-shadow-2xl" data-aos="fade-up" data-aos-delay="100">
            <?= __('nav_hotels') ?>
        </h1>
        
        <p class="font-editorial italic text-[#C9922A] text-xl md:text-2xl mb-12 drop-shadow-md max-w-2xl" data-aos="fade-up" data-aos-delay="200">
            <?= __('hotels_subtitle') ?>
        </p>

        <!-- Glassmorphism Search Bar -->
        <div class="w-full flex justify-center" data-aos="fade-up" data-aos-delay="400">
            <form action="index.php" method="GET" class="w-full max-w-2xl relative">
                <div class="flex items-center bg-white/10 backdrop-blur-xl border border-white/20 rounded-full overflow-hidden shadow-2xl p-1.5 transition-transform duration-300 focus-within:scale-105 focus-within:bg-white/20">
                    <span class="material-symbols-outlined text-white/70 ml-5 mr-3 text-xl">search</span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="<?= __('search_hotel_placeholder') ?>" 
                           class="flex-1 bg-transparent text-white placeholder-white/60 py-3 text-base focus:outline-none border-none font-medium">
                    <?php if($regionId): ?><input type="hidden" name="region_id" value="<?= htmlspecialchars($regionId) ?>"><?php endif; ?>
                    <?php if($stars): ?><input type="hidden" name="stars" value="<?= htmlspecialchars($stars) ?>"><?php endif; ?>
                    <button type="submit" class="bg-[#C9922A] hover:bg-white text-[#1A0E05] px-8 py-3 rounded-full font-bold transition-all shadow-lg flex items-center gap-2 text-sm uppercase tracking-wider">
                        <?= __('search') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Main Experience Layout -->
<section class="py-20 relative z-20 bg-[#FAF7F2] min-h-screen">
    <div class="max-w-[1600px] mx-auto px-6 lg:px-10 xl:px-16" id="hotels-container">

        <!-- Elegant Filter Bar -->
        <div class="mb-12 border-b border-[#1A0E05]/10 pb-8 grid grid-cols-1 xl:grid-cols-2 gap-8 items-start" data-aos="fade-up" id="filter-bar">
            
            <!-- Stars Filter -->
            <div class="w-full overflow-hidden">
                <h3 class="font-editorial text-lg text-[#1B3A6B] font-bold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#C9922A]">star</span>
                    Daraja
                </h3>
                <div class="flex overflow-x-auto whitespace-nowrap hide-scrollbar gap-3 pb-2">
                    <a href="?<?= $regionId ? 'region_id='.$regionId.'&' : '' ?><?= $search ? 'search='.urlencode($search) : '' ?>" 
                       class="px-5 py-2 rounded-full text-sm font-semibold transition-all border <?= empty($stars) ? 'bg-[#1B3A6B] text-white border-[#1B3A6B] shadow-md' : 'bg-white text-[#1A0E05]/70 border-[#1A0E05]/20 hover:border-[#1B3A6B] hover:text-[#1A0E05]' ?>">
                        Barchasi
                    </a>
                    <?php for($i = 5; $i >= 1; $i--): ?>
                        <a href="?stars=<?= $i ?><?= $regionId ? '&region_id='.$regionId : '' ?><?= $search ? '&search='.urlencode($search) : '' ?>" 
                           class="px-5 py-2 rounded-full text-sm font-semibold transition-all flex items-center gap-1.5 border <?= $stars == $i ? 'bg-[#1B3A6B] text-white border-[#1B3A6B] shadow-md' : 'bg-white text-[#1A0E05]/70 border-[#1A0E05]/20 hover:border-[#1B3A6B] hover:text-[#1A0E05]' ?>">
                            <?= $i ?> <svg class="w-4 h-4 <?= $stars == $i ? 'text-[#C9922A]' : 'text-gray-400' ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Regions Filter -->
            <div class="w-full overflow-hidden">
                <h3 class="font-editorial text-lg text-[#1B3A6B] font-bold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#C9922A]">location_on</span>
                    Viloyatlar
                </h3>
                <div class="flex overflow-x-auto whitespace-nowrap hide-scrollbar gap-3 pb-2">
                    <a href="?<?= $stars ? 'stars='.$stars.'&' : '' ?><?= $search ? 'search='.urlencode($search) : '' ?>" 
                       class="px-5 py-2 rounded-full text-sm font-semibold transition-all border <?= empty($regionId) ? 'bg-[#1B3A6B] text-white border-[#1B3A6B] shadow-md' : 'bg-white text-[#1A0E05]/70 border-[#1A0E05]/20 hover:border-[#1B3A6B] hover:text-[#1A0E05]' ?>">
                        Barchasi
                    </a>
                    <?php foreach($regions as $reg): ?>
                        <a href="?region_id=<?= $reg['id'] ?><?= $stars ? '&stars='.$stars : '' ?><?= $search ? '&search='.urlencode($search) : '' ?>" 
                           class="px-5 py-2 rounded-full text-sm font-semibold transition-all border <?= $regionId == $reg['id'] ? 'bg-[#1B3A6B] text-white border-[#1B3A6B] shadow-md' : 'bg-white text-[#1A0E05]/70 border-[#1A0E05]/20 hover:border-[#1B3A6B] hover:text-[#1A0E05]' ?>">
                            <?= htmlspecialchars(localizedField($reg, 'name')) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Results Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <span class="font-editorial text-xl text-[#1A0E05] font-semibold">
                Natija: <span class="text-[#C9922A] font-bold"><?= $totalRows ?></span> ta mehmonxona
            </span>
            
            <?php if(!empty($search) || !empty($regionId) || !empty($stars)): ?>
                <a href="index.php" class="text-sm text-red-600 hover:text-red-800 font-semibold flex items-center gap-1 transition-colors bg-red-50/50 hover:bg-red-50 px-4 py-2 rounded-full border border-red-100 self-start md:self-auto">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    Filtrlarni tozalash
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($hotels)): ?>
            <div class="bg-white p-20 rounded-3xl border border-[#1A0E05]/10 text-center shadow-2xl" data-aos="zoom-in">
                <span class="material-symbols-outlined text-6xl text-[#1B3A6B]/20 mb-6 block">hotel_class</span>
                <h3 class="font-editorial text-3xl font-bold text-[#1B3A6B] mb-4"><?= __('no_results') ?></h3>
                <p class="text-[#1A0E05]/60 mb-8 text-lg"><?= __('try_other_params') ?></p>
                <a href="index.php" class="inline-flex items-center gap-2 bg-[#1B3A6B] text-white px-8 py-3 rounded-full font-bold hover:bg-[#002452] transition-colors shadow-xl">
                    <?= __('clear_filters') ?>
                </a>
            </div>
        <?php else: ?>
            <!-- Luxury Hotel Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-16">
                <?php foreach($hotels as $i => $hotel): 
                    $hName = localizedField($hotel, 'name');
                ?>
                    <a href="view.php?id=<?= $hotel['id'] ?>" class="group block bg-white rounded-3xl overflow-hidden border border-[#1B3A6B]/10 hover:border-[#C9922A]/50 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 flex flex-col h-full" data-aos="fade-up" data-aos-delay="<?= ($i % 4) * 100 ?>">
                        
                        <!-- Image Container -->
                        <div class="h-64 overflow-hidden relative bg-[#F5F1EA] shrink-0">
                            <img loading="lazy" src="<?= publicImage($hotel['image'], $hName) ?>" alt="<?= htmlspecialchars($hName) ?>" 
                                 class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out"
                                 onerror="this.src='https://placehold.co/600x400/1B3A6B/C9922A.jpg?text=<?= urlencode($hName) ?>'">
                            
                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-[#1B3A6B]/80 via-[#1B3A6B]/10 to-transparent opacity-70 group-hover:opacity-90 transition-opacity duration-500"></div>

                            <!-- Top Right: Price Badge -->
                            <?php if($hotel['price_from']): ?>
                            <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-xl text-sm font-bold text-[#1A0E05] shadow-lg flex flex-col items-end">
                                <span class="text-[9px] text-[#1A0E05]/50 uppercase tracking-widest leading-none mb-0.5">Boshlang'ich</span>
                                <span class="text-[#1B3A6B] leading-none">$<?= number_format((float)$hotel['price_from'], 0, '.', ' ') ?></span>
                            </div>
                            <?php endif; ?>

                            <!-- Bottom Left: Rating -->
                            <div class="absolute bottom-4 left-4">
                                <div class="bg-white/20 backdrop-blur-md px-3 py-1.5 rounded-xl border border-white/20 shadow-lg">
                                    <?= renderStars($hotel['stars']) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Content Details -->
                        <div class="p-6 flex-grow flex flex-col">
                            <h3 class="font-editorial text-2xl font-bold text-[#1B3A6B] mb-3 leading-tight group-hover:text-[#C9922A] transition-colors">
                                <?= htmlspecialchars($hName) ?>
                            </h3>
                            
                            <div class="mt-auto flex items-center text-sm font-medium text-[#1A0E05]/60 border-t border-[#1B3A6B]/5 pt-4">
                                <span class="material-symbols-outlined text-[18px] text-[#C9922A] mr-1.5">location_on</span>
                                <?= htmlspecialchars(localizedField($hotel, 'region_name') ?: __('unknown')) ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($paginator['totalPages'] > 1): ?>
                <div class="flex justify-center mt-12" data-aos="fade-up">
                    <?php 
                    $qs = $_GET; unset($qs['page']);
                    $baseUrl = '?' . http_build_query($qs);
                    $currentPage = $paginator['currentPage'];
                    $totalPages = $paginator['totalPages'];
                    require '../../includes/pagination.php';
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // AJAX Filtering for seamless experience
    document.body.addEventListener('click', function(e) {
        let url = null;
        
        const filterLink = e.target.closest('a[href^="?"]');
        if (filterLink && filterLink.closest('#hotels-container')) {
            url = filterLink.href;
        } else {
            const clearLink = e.target.closest('a[href="index.php"]');
            if (clearLink && clearLink.closest('#hotels-container')) {
                url = clearLink.href;
            }
        }

        if (url) {
            e.preventDefault();
            fetchContent(url);
        }
    });

    function fetchContent(url) {
        const container = document.getElementById('hotels-container');
        if (container) container.style.opacity = '0.5';

        fetch(url)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                if (container && doc.getElementById('hotels-container')) {
                    container.innerHTML = doc.getElementById('hotels-container').innerHTML;
                    container.style.opacity = '1';
                }

                if (typeof AOS !== 'undefined') {
                    setTimeout(() => AOS.refreshHard(), 50);
                }
            })
            .catch(err => {
                console.error("AJAX Error:", err);
                container.style.opacity = '1';
            });
    }
});
</script>

<?php require_once '../includes/layout_footer.php'; ?>
