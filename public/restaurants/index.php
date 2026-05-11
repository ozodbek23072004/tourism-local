<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../includes/functions.php';

$price = $_GET['price'] ?? 'all';
$region_id = $_GET['region_id'] ?? null;

$sql = "SELECT r.*, reg.name_uz as region_name_uz, reg.name_ru as region_name_ru, reg.name_en as region_name_en 
        FROM restaurants r
        LEFT JOIN regions reg ON r.region_id = reg.id
        WHERE 1=1";
$params = [];

if ($price !== 'all') {
    $sql .= " AND r.price_level = :price";
    $params['price'] = $price;
}
if ($region_id) {
    $sql .= " AND r.region_id = :region_id";
    $params['region_id'] = $region_id;
}
$sql .= " ORDER BY r.rating DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$restaurants = $stmt->fetchAll();

// Regions
$stmtReg = $pdo->query("SELECT id, name_uz, name_ru, name_en FROM regions ORDER BY name_uz");
$regions = $stmtReg->fetchAll();

// Stats
$totalRestsDB = $pdo->query("SELECT COUNT(*) FROM restaurants")->fetchColumn();
$totalRegionsDB = $pdo->query("SELECT COUNT(DISTINCT region_id) FROM restaurants")->fetchColumn();

// Hero slider images
$heroImages = getHeroImages($pdo, 'restaurants');
if (empty($heroImages)) {
    $heroImages = ['https://images.unsplash.com/photo-1514933651103-005eec06c04b?q=80&w=2500'];
}

$lang = currentLang();
$pageTitle = __('nav_restaurants');
require_once '../includes/seo.php';
renderMeta(['title' => $pageTitle . ' | Silk Road Explorer']);
require_once '../includes/layout_header.php';

function getPriceSymbols($level) {
    if ($level === 'low') return '$';
    if ($level === 'high') return '$$$';
    return '$$';
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
             alt="Luxury Dining" 
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
    <div class="absolute inset-0 z-0 bg-gradient-to-t from-[#1A0E05] via-[#1A0E05]/60 to-[#1A0E05]/20"></div>

    <div class="relative z-10 text-center px-4 max-w-5xl mx-auto w-full flex flex-col items-center mt-12">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-3 text-white/70 text-xs tracking-widest uppercase font-semibold mb-6" data-aos="fade-down">
            <a href="<?= BASE_URL ?>public/index.php" class="hover:text-[#C9922A] transition-colors">Explorer</a>
            <span class="w-1.5 h-1.5 rounded-full bg-[#C9922A]"></span>
            <span class="text-white"><?= __('nav_restaurants') ?></span>
        </div>

        <h1 class="font-editorial text-5xl md:text-7xl font-bold text-white mb-6 leading-tight drop-shadow-2xl" data-aos="fade-up" data-aos-delay="100">
            <?= __('nav_restaurants') ?>
        </h1>
        
        <p class="font-editorial italic text-[#C9922A] text-xl md:text-2xl mb-12 drop-shadow-md max-w-2xl" data-aos="fade-up" data-aos-delay="200">
            <?= __('restaurants_subtitle') ?>
        </p>

        <!-- Stat Pills -->
        <div class="flex flex-wrap items-center justify-center gap-6" data-aos="fade-up" data-aos-delay="300">
            <div class="flex flex-col items-center">
                <span class="text-3xl font-editorial font-bold text-white"><?= $totalRestsDB ?></span>
                <span class="text-[10px] uppercase tracking-widest text-white/60">Restoran</span>
            </div>
            <div class="w-[1px] h-10 bg-white/20"></div>
            <div class="flex flex-col items-center">
                <span class="text-3xl font-editorial font-bold text-white"><?= $totalRegionsDB ?></span>
                <span class="text-[10px] uppercase tracking-widest text-white/60">Viloyat</span>
            </div>
            <div class="w-[1px] h-10 bg-white/20"></div>
            <div class="flex flex-col items-center">
                <span class="text-3xl font-editorial font-bold text-[#C9922A]">$$$</span>
                <span class="text-[10px] uppercase tracking-widest text-[#C9922A]/70">Premium</span>
            </div>
        </div>
    </div>
</section>

<!-- Main Experience Layout -->
<section class="py-20 relative z-20 bg-[#FAF7F2] min-h-screen">
    <div class="max-w-[1600px] mx-auto px-6 lg:px-10 xl:px-16" id="restaurants-container">

        <!-- Elegant Filter Bar -->
        <div class="mb-12 border-b border-[#1A0E05]/10 pb-8 grid grid-cols-1 xl:grid-cols-2 gap-8 items-start" data-aos="fade-up" id="filter-bar">
            
            <!-- Price Filters -->
            <div class="w-full overflow-hidden">
                <h3 class="font-editorial text-lg text-[#1A0E05] font-bold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#C9922A]">payments</span>
                    Narx darajasi
                </h3>
                <div class="flex overflow-x-auto whitespace-nowrap hide-scrollbar gap-3 pb-2">
                    <a href="?price=all<?= $region_id ? '&region_id='.$region_id : '' ?>" 
                       class="px-6 py-2.5 rounded-full text-sm font-semibold transition-all border <?= $price === 'all' ? 'bg-[#1A0E05] text-[#C9922A] border-[#1A0E05] shadow-lg' : 'bg-white text-[#1A0E05]/70 border-[#1A0E05]/20 hover:border-[#C9922A] hover:text-[#1A0E05]' ?>">
                        Barchasi
                    </a>
                    <a href="?price=low<?= $region_id ? '&region_id='.$region_id : '' ?>" 
                       class="px-6 py-2.5 rounded-full text-sm font-semibold transition-all border <?= $price === 'low' ? 'bg-[#1A0E05] text-[#C9922A] border-[#1A0E05] shadow-lg' : 'bg-white text-[#1A0E05]/70 border-[#1A0E05]/20 hover:border-[#C9922A] hover:text-[#1A0E05]' ?>">
                        $ <span class="font-normal opacity-80 ml-1"><?= __('price_low') ?></span>
                    </a>
                    <a href="?price=mid<?= $region_id ? '&region_id='.$region_id : '' ?>" 
                       class="px-6 py-2.5 rounded-full text-sm font-semibold transition-all border <?= $price === 'mid' ? 'bg-[#1A0E05] text-[#C9922A] border-[#1A0E05] shadow-lg' : 'bg-white text-[#1A0E05]/70 border-[#1A0E05]/20 hover:border-[#C9922A] hover:text-[#1A0E05]' ?>">
                        $$ <span class="font-normal opacity-80 ml-1"><?= __('price_mid') ?></span>
                    </a>
                    <a href="?price=high<?= $region_id ? '&region_id='.$region_id : '' ?>" 
                       class="px-6 py-2.5 rounded-full text-sm font-semibold transition-all border <?= $price === 'high' ? 'bg-[#1A0E05] text-[#C9922A] border-[#1A0E05] shadow-lg' : 'bg-white text-[#1A0E05]/70 border-[#1A0E05]/20 hover:border-[#C9922A] hover:text-[#1A0E05]' ?>">
                        $$$ <span class="font-normal opacity-80 ml-1"><?= __('price_high') ?></span>
                    </a>
                </div>
            </div>

            <!-- Regions Filter -->
            <div class="w-full overflow-hidden">
                <h3 class="font-editorial text-lg text-[#1A0E05] font-bold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#C9922A]">location_on</span>
                    Viloyatlar
                </h3>
                <div class="flex overflow-x-auto whitespace-nowrap hide-scrollbar gap-3 pb-2">
                    <a href="?<?= $price !== 'all' ? 'price='.$price : '' ?>" 
                       class="px-6 py-2.5 rounded-full text-sm font-semibold transition-all border <?= empty($region_id) ? 'bg-[#1A0E05] text-[#C9922A] border-[#1A0E05] shadow-lg' : 'bg-white text-[#1A0E05]/70 border-[#1A0E05]/20 hover:border-[#C9922A] hover:text-[#1A0E05]' ?>">
                        Barchasi
                    </a>
                    <?php foreach($regions as $reg): ?>
                        <a href="?region_id=<?= $reg['id'] ?><?= $price !== 'all' ? '&price='.$price : '' ?>" 
                           class="px-6 py-2.5 rounded-full text-sm font-semibold transition-all border <?= $region_id == $reg['id'] ? 'bg-[#1A0E05] text-[#C9922A] border-[#1A0E05] shadow-lg' : 'bg-white text-[#1A0E05]/70 border-[#1A0E05]/20 hover:border-[#C9922A] hover:text-[#1A0E05]' ?>">
                            <?= htmlspecialchars(localizedField($reg, 'name')) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Results Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <span class="font-editorial text-xl text-[#1A0E05] font-semibold">
                Natija: <span class="text-[#C9922A] font-bold"><?= count($restaurants) ?></span> ta maskan
            </span>
            
            <?php if($price !== 'all' || !empty($region_id)): ?>
                <a href="index.php" class="text-sm text-red-600 hover:text-red-800 font-semibold flex items-center gap-1 transition-colors bg-red-50/50 hover:bg-red-50 px-4 py-2 rounded-full border border-red-100 self-start md:self-auto">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    Filtrlarni tozalash
                </a>
            <?php endif; ?>
        </div>

        <div id="restaurant-grid-wrapper">
        <?php if (empty($restaurants)): ?>
            <div class="bg-white p-20 rounded-3xl border border-[#1A0E05]/10 text-center shadow-2xl" data-aos="zoom-in">
                <span class="material-symbols-outlined text-6xl text-[#1A0E05]/20 mb-6 block">restaurant_menu</span>
                <h3 class="font-editorial text-3xl font-bold text-[#1A0E05] mb-4"><?= __('no_results') ?></h3>
                <p class="text-[#1A0E05]/60 mb-8 text-lg"><?= __('try_other_params') ?></p>
                <a href="index.php" class="inline-flex items-center gap-2 bg-[#1A0E05] text-[#C9922A] px-8 py-3 rounded-full font-bold hover:bg-black transition-colors shadow-xl">
                    <?= __('clear_filters') ?>
                </a>
            </div>
        <?php else: ?>
            <!-- Luxury Dining Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-16">
                <?php foreach($restaurants as $i => $rest): 
                    $rName = $rest['name'];
                    $rRegion = localizedField($rest, 'region_name'); 
                ?>
                    <div class="group block bg-white rounded-[2rem] overflow-hidden border border-[#1A0E05]/5 hover:border-[#C9922A]/30 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 flex flex-col h-full cursor-pointer" data-aos="fade-up" data-aos-delay="<?= ($i % 4) * 100 ?>">
                        
                        <!-- Image Container -->
                        <div class="h-64 overflow-hidden relative bg-[#EBE5DA] shrink-0">
                            <img loading="lazy" src="<?= publicImage($rest['image_path'], $rName) ?>" alt="<?= htmlspecialchars($rName) ?>" 
                                 class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out"
                                 onerror="this.src='https://placehold.co/600x400/2C1A0E/C9922A.jpg?text=<?= urlencode($rName) ?>'">
                            
                            <!-- Soft Gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-[#1A0E05]/80 via-transparent to-transparent opacity-60 group-hover:opacity-80 transition-opacity duration-300"></div>

                            <!-- Top Right: Rating -->
                            <?php if($rest['rating'] > 0): ?>
                            <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-xl text-sm font-bold text-[#1A0E05] shadow-lg flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#C9922A]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <?= number_format($rest['rating'], 1) ?>
                            </div>
                            <?php endif; ?>

                            <!-- Bottom Left: Price Level -->
                            <div class="absolute bottom-4 left-4">
                                <div class="bg-[#1A0E05]/80 backdrop-blur-md px-3 py-1.5 rounded-xl text-xs font-bold tracking-widest text-[#C9922A] border border-[#C9922A]/30 shadow-lg">
                                    <?= getPriceSymbols($rest['price_level']) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Content Details -->
                        <div class="p-6 flex-grow flex flex-col">
                            <h3 class="font-editorial text-2xl font-bold text-[#1A0E05] mb-1 leading-tight group-hover:text-[#C9922A] transition-colors">
                                <?= htmlspecialchars($rName) ?>
                            </h3>
                            <p class="text-xs font-semibold uppercase tracking-widest text-[#1A0E05]/50 mb-4">
                                <?= htmlspecialchars($rest['cuisine_type'] ?? 'Restoran') ?>
                            </p>
                            
                            <p class="text-[#1A0E05]/60 text-sm mb-6 line-clamp-2 leading-relaxed">
                                <?= htmlspecialchars($rest['description']) ?>
                            </p>
                            
                            <div class="mt-auto space-y-3 text-sm font-medium text-[#1A0E05]/60 border-t border-[#1A0E05]/5 pt-4">
                                <?php if($rest['working_hours']): ?>
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-[#C9922A]/70">schedule</span>
                                    <span><?= htmlspecialchars($rest['working_hours']) ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if($rRegion): ?>
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-[#C9922A]/70">location_on</span>
                                    <span><?= htmlspecialchars($rRegion) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('click', function(e) {
        let url = null;
        
        // Handle filter links
        const filterLink = e.target.closest('a[href^="?"]');
        if (filterLink && filterLink.closest('#restaurants-container')) {
            url = filterLink.href;
        } else {
            // Handle 'Clear filters' links
            const clearLink = e.target.closest('a[href="index.php"]');
            if (clearLink && clearLink.closest('#restaurants-container')) {
                url = clearLink.href;
            }
        }

        if (url) {
            e.preventDefault();
            fetchContent(url);
        }
    });

    function fetchContent(url) {
        const container = document.getElementById('restaurants-container');
        if (container) container.style.opacity = '0.5';

        fetch(url)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                if (container && doc.getElementById('restaurants-container')) {
                    container.innerHTML = doc.getElementById('restaurants-container').innerHTML;
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
