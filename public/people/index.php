<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../includes/functions.php';

$regionId = $_GET['region_id'] ?? '';
$era = $_GET['era'] ?? '';
$search = $_GET['search'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$perPage = 15;

$eraFilters = [
    '9-11' => [800, 1099],
    '14-15' => [1300, 1499],
    '16-19' => [1500, 1899]
];

$filters = [
    'region_id' => $regionId, 
    'search' => $search, 
    'era_range' => $eraFilters[$era] ?? null
];
$totalRows = countRows($pdo, 'people', $filters);
$paginator = paginate($totalRows, $perPage, $page);
$people = getPeople($pdo, $filters, $perPage, $paginator['offset']);

// Hero slider images
$heroImages = getHeroImages($pdo, 'people');
if (empty($heroImages)) {
    $heroImages = ['https://images.unsplash.com/photo-1627998634994-6b9487decf0d?q=80&w=2500'];
}

$stmtReg = $pdo->prepare("SELECT id, name_uz, name_ru, name_en FROM regions ORDER BY name_uz");
$stmtReg->execute();
$regions = $stmtReg->fetchAll();

$lang = currentLang();
$pageTitle = __('nav_people');
require_once '../includes/seo.php';
renderMeta(['title' => $pageTitle . ' | Silk Road Explorer']);
require_once '../includes/layout_header.php';
?>

<style>
/* Faint arabesque geometric pattern */
.bg-arabesque {
    background-image: url('data:image/svg+xml;utf8,<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><path d="M20 0l20 20-20 20L0 20z" fill="rgba(255,255,255,0.04)" fill-rule="evenodd"/></svg>');
    animation: drift 60s linear infinite;
}
@keyframes drift {
    from { background-position: 0 0; }
    to { background-position: -400px 0; }
}

.search-focus:focus-within {
    border-color: #C9922A;
    box-shadow: 0 0 0 1px #C9922A;
}

/* Fallback styling */
.person-card.no-image .card-image-area {
  background: linear-gradient(135deg, #2C1A0E 0%, #1A0E05 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}
.person-card.no-image .card-image-area::after {
  content: attr(data-initial);
  font-family: 'Playfair Display', serif;
  font-size: 80px;
  color: rgba(201, 146, 42, 0.6);
}

/* Staggered card animation */
.stagger-card {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.6s forwards;
}
@keyframes fadeInUp {
    to { opacity: 1; transform: translateY(0); }
}

/* Carousel placeholder text */
.placeholder-carousel {
    position: relative;
    display: inline-block;
    min-width: 150px;
}
.placeholder-carousel span {
    position: absolute;
    left: 0;
    top: -10px;
    opacity: 0;
    white-space: nowrap;
    animation: cyclePlaceholder 6s infinite;
}
.placeholder-carousel span:nth-child(2) { animation-delay: 2s; }
.placeholder-carousel span:nth-child(3) { animation-delay: 4s; }

@keyframes cyclePlaceholder {
    0%, 25% { opacity: 0; transform: translateY(5px); }
    33%, 58% { opacity: 1; transform: translateY(0); }
    66%, 100% { opacity: 0; transform: translateY(-5px); }
}

/* Custom Grid */
.grid-museum {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 2rem;
}
</style>

<!-- HERO SECTION -->
<section class="relative pt-32 pb-24 bg-[#2C1A0E] overflow-hidden">
    <!-- Cinematic Background Slider -->
    <div class="absolute inset-0 z-0 bg-[#2C1A0E]">
        <img id="hero-bg-img" src="<?= $heroImages[0] ?>" 
             alt="Historical Figure" 
             class="w-full h-full object-cover transform scale-105 motion-safe:animate-[pulse_20s_ease-in-out_infinite] opacity-30 blend-luminosity transition-opacity duration-1000 ease-in-out">
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

    <div class="absolute inset-0 bg-arabesque opacity-60 z-0"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-[#2C1A0E] via-transparent to-[#1A0E05] z-0"></div>
    
    <div class="relative max-w-7xl mx-auto px-6 lg:px-8 flex flex-col items-center text-center">
        <!-- Breadcrumb / Label -->
        <div class="flex flex-col items-center gap-4" data-aos="fade-up">
            <span class="text-xs font-bold text-[#C9922A] tracking-[0.2em] uppercase">O'zbekiston tarixi</span>
            <div class="h-[2px] w-[120px] bg-[#C9922A] opacity-80"></div>
        </div>
        
        <h1 class="font-display text-5xl md:text-[64px] font-bold text-white mt-8 mb-6 leading-tight" data-aos="fade-up" data-aos-delay="100">
            <?= __('nav_people') ?>
        </h1>
        
        <!-- Search -->
        <form action="index.php" method="GET" class="w-full max-w-[600px] mt-6" data-aos="fade-up" data-aos-delay="200">
            <div class="flex items-center bg-white/10 backdrop-blur-md border border-white/20 rounded-xl px-4 py-1.5 h-[52px] transition-all search-focus">
                <span class="material-symbols-outlined text-white/50 mr-3">search</span>
                
                <div class="relative flex-1 h-full flex items-center">
                    <?php if(empty($search)): ?>
                    <div class="placeholder-carousel text-white/40 text-sm absolute inset-y-0 flex items-center pointer-events-none">
                        <span>Ibn Sino...</span>
                        <span>Amir Temur...</span>
                        <span>Ulug'bek...</span>
                    </div>
                    <?php endif; ?>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           class="w-full bg-transparent text-white placeholder-transparent focus:outline-none border-none text-sm z-10"
                           onfocus="this.previousElementSibling ? this.previousElementSibling.style.display='none' : null"
                           onblur="if(this.value=='' && this.previousElementSibling) this.previousElementSibling.style.display='flex'">
                </div>
                
                <?php if($regionId): ?><input type="hidden" name="region_id" value="<?= htmlspecialchars($regionId) ?>"><?php endif; ?>
                <?php if($era): ?><input type="hidden" name="era" value="<?= htmlspecialchars($era) ?>"><?php endif; ?>
                
                <button type="submit" class="bg-[#C9922A] text-[#1A0E05] px-5 py-2 rounded-lg font-bold text-sm shadow-lg shadow-[#C9922A]/20 hover:bg-amber-400 active:scale-95 transition-all">
                    <?= __('search') ?>
                </button>
            </div>
        </form>

        <!-- Stats -->
        <div class="flex items-center gap-4 mt-10 text-xs font-semibold text-[#C9922A]" data-aos="fade-up" data-aos-delay="300">
            <span class="bg-[#C9922A]/10 px-3 py-1.5 rounded-full border border-[#C9922A]/30">3 ta buyuk shaxs</span>
            <div class="w-1.5 h-1.5 rounded-full bg-[#C9922A]"></div>
            <span class="bg-[#C9922A]/10 px-3 py-1.5 rounded-full border border-[#C9922A]/30">3 ta davr</span>
            <div class="w-1.5 h-1.5 rounded-full bg-[#C9922A]"></div>
            <span class="bg-[#C9922A]/10 px-3 py-1.5 rounded-full border border-[#C9922A]/30">1000+ yil tarix</span>
        </div>
    </div>
</section>

<section class="py-12 bg-silk-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-6 lg:px-8" id="people-container">
        
        <!-- Filters -->
        <div class="mb-10 grid grid-cols-1 xl:grid-cols-2 gap-6 items-center" data-aos="fade-up" id="filter-bar">
            
            <!-- Era Filter (Secondary) -->
            <div class="flex items-center w-full overflow-hidden">
                <h3 class="text-xs font-bold text-silk-400 uppercase tracking-widest mr-4 shrink-0">Davr:</h3>
                <div class="flex overflow-x-auto whitespace-nowrap hide-scrollbar gap-2 pb-2 xl:pb-0 w-full">
                    <a href="?<?= http_build_query(array_merge($_GET, ['era' => ''])) ?>" 
                       class="px-4 py-2 rounded-xl text-[13px] font-semibold transition-all <?= empty($era) ? 'bg-[#1A0E05] text-[#C9922A] shadow-md border border-[#C9922A]/30' : 'bg-white text-silk-600 border border-silk-200 hover:border-[#C9922A]' ?>">
                        Barchasi
                    </a>
                    <?php 
                    $eraOptions = [
                        '9-11' => 'IX–XI asr',
                        '14-15' => 'XIV–XV asr',
                        '16-19' => 'XVI–XIX asr'
                    ];
                    foreach($eraOptions as $eraKey => $eraLabel): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['era' => $eraKey])) ?>" 
                           class="px-4 py-2 rounded-xl text-[13px] font-semibold transition-all <?= $era === $eraKey ? 'bg-[#1A0E05] text-[#C9922A] shadow-md border border-[#C9922A]/30' : 'bg-white text-silk-600 border border-silk-200 hover:border-[#C9922A]' ?>">
                            <?= $eraLabel ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Region Filter (Underlined style) -->
            <div class="flex items-center w-full overflow-hidden">
                <h3 class="text-xs font-bold text-silk-400 uppercase tracking-widest mr-4 shrink-0"><?= __('regions') ?>:</h3>
                <div class="flex overflow-x-auto whitespace-nowrap hide-scrollbar gap-6 px-1 pb-2 xl:pb-0 w-full">
                    <a href="?<?= http_build_query(array_merge($_GET, ['region_id' => ''])) ?>" 
                       class="text-[13px] font-semibold transition-all pb-1 border-b-2 shrink-0 <?= empty($regionId) ? 'text-[#1A0E05] border-[#C9922A]' : 'text-silk-400 border-transparent hover:text-silk-800' ?>">
                        <?= __('all') ?>
                    </a>
                    <?php foreach($regions as $reg): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['region_id' => $reg['id']])) ?>" 
                           class="text-[13px] font-semibold transition-all pb-1 border-b-2 shrink-0 <?= $regionId == $reg['id'] ? 'text-[#1A0E05] border-[#C9922A]' : 'text-silk-400 border-transparent hover:text-silk-800' ?>">
                            <?= htmlspecialchars(localizedField($reg, 'name')) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if(!empty($search) || !empty($regionId) || !empty($era)): ?>
        <div class="mb-6 flex items-center gap-3" data-aos="fade-in">
            <span class="text-sm text-silk-500"><?= __('active_filters') ?>:</span>
            <?php if(!empty($search)): ?>
                <span class="inline-flex items-center gap-1 bg-[#C9922A]/20 text-[#1A0E05] px-3 py-1 rounded-lg text-xs font-semibold">
                    "<?= htmlspecialchars($search) ?>"
                </span>
            <?php endif; ?>
            <a href="index.php" class="text-xs text-red-500 hover:text-red-700 font-semibold transition-colors"><?= __('clear_all') ?></a>
        </div>
        <?php endif; ?>

        <?php if (empty($people)): ?>
            <div class="bg-white p-16 rounded-3xl border border-silk-200/60 text-center flex flex-col items-center" data-aos="zoom-in">
                <!-- Stylized telescope SVG -->
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#C9922A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-6 opacity-80">
                    <path d="M14 6l6 6M20 6l-6 6M11.66 11.66a2 2 0 1 0-2.83 2.83l-5.66 5.66a2 2 0 1 0 2.83 2.83l5.66-5.66z"/>
                    <path d="M20 4a2 2 0 0 0-2.83 0L14.34 6.83a2 2 0 0 0 0 2.83l4 4a2 2 0 0 0 2.83 0L24 10.83a2 2 0 0 0 0-2.83z"/>
                </svg>
                <h3 class="font-display text-2xl font-bold text-[#1A0E05] mb-3">Hech narsa topilmadi</h3>
                <p class="text-silk-500 mb-6"><?= __('try_other_params') ?></p>
                <a href="index.php" class="bg-[#1A0E05] text-[#C9922A] px-6 py-2.5 rounded-lg font-bold hover:bg-black transition-colors"><?= __('clear_filters') ?></a>
            </div>
        <?php else: ?>
            <div class="grid-museum mb-12">
                <?php foreach($people as $i => $person): 
                    $perName = localizedField($person, 'name');
                    // Compute era
                    $by = $person['born_year'];
                    $perEra = '';
                    if($by >= 800 && $by < 1200) $perEra = 'IX-XI asr';
                    elseif($by >= 1300 && $by < 1500) $perEra = 'XIV-XV asr';
                    elseif($by >= 1500 && $by < 1900) $perEra = 'XVI-XIX asr';
                ?>
                    <a href="view.php?id=<?= $person['id'] ?>" class="group block stagger-card person-card" style="animation-delay: <?= ($i % 15) * 80 ?>ms">
                        <div class="relative w-full h-[380px] rounded-xl overflow-hidden bg-[#1A0E05] border border-[#C9922A]/30 group-hover:border-[#C9922A]/80 transition-all duration-500 shadow-lg">
                            
                            <!-- Image -->
                            <div class="card-image-area absolute inset-x-0 top-0 w-full h-[75%]" data-initial="<?= mb_strtoupper(mb_substr($perName, 0, 1)) ?>">
                                <img loading="lazy" src="<?= publicImage($person['image'], $perName) ?>" alt="<?= htmlspecialchars($perName) ?>" 
                                     class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-700"
                                     onerror="this.onerror=null; this.style.display='none'; this.closest('.person-card').classList.add('no-image')">
                            </div>
                                 
                            <!-- Faded Gradient -->
                            <div class="absolute inset-x-0 bottom-0 h-[60%] bg-gradient-to-t from-[#1A0E05] via-[#1A0E05]/90 to-transparent"></div>

                            <!-- Era Badge -->
                            <?php if($perEra): ?>
                            <div class="absolute top-4 right-4 bg-[#C9922A]/90 text-[#1A0E05] px-3 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase shadow-md">
                                <?= $perEra ?>
                            </div>
                            <?php endif; ?>

                            <!-- Text Overlay -->
                            <div class="absolute inset-x-0 bottom-0 p-5 flex flex-col justify-end">
                                <h3 class="font-display font-bold text-white text-lg mb-1 leading-tight"><?= htmlspecialchars($perName) ?></h3>
                                <p class="text-[#C9922A] text-[13px] font-semibold mb-2 tracking-wide">
                                    <?= $person['born_year'] ?> — <?= $person['died_year'] ?: __('present') ?>
                                </p>
                                <p class="text-white/70 text-[12px] line-clamp-2 leading-relaxed">
                                    <?= htmlspecialchars(mb_substr(strip_tags(localizedField($person, 'bio')), 0, 100)) ?>...
                                </p>
                            </div>

                            <!-- Hover Pill -->
                            <div class="absolute inset-x-0 -bottom-10 flex justify-center group-hover:bottom-4 transition-all duration-300 opacity-0 group-hover:opacity-100 z-10">
                                <span class="bg-[#C9922A] text-[#1A0E05] text-[11px] font-bold px-4 py-1.5 rounded-full uppercase tracking-wider shadow-lg">
                                    Ko'rish →
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($paginator['totalPages'] > 1): ?>
                <div class="flex justify-center mt-10" data-aos="fade-up">
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
    // AJAX Filtering
    document.body.addEventListener('click', function(e) {
        let url = null;
        
        const filterLink = e.target.closest('a[href^="?"]');
        if (filterLink && filterLink.closest('#people-container')) {
            url = filterLink.href;
        } else {
            const clearLink = e.target.closest('a[href="index.php"]');
            if (clearLink && clearLink.closest('#people-container')) {
                url = clearLink.href;
            }
        }

        if (url) {
            e.preventDefault();
            fetchContent(url);
        }
    });

    function fetchContent(url) {
        const container = document.getElementById('people-container');
        if (container) container.style.opacity = '0.5';

        fetch(url)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                if (container && doc.getElementById('people-container')) {
                    container.innerHTML = doc.getElementById('people-container').innerHTML;
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
