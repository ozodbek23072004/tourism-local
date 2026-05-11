<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/seo.php';

// Remove static HTML caching to allow dynamic user features (like favorites) to work properly.
// In a true luxury application, dynamic state is important. We can use Redis/Memcached for data caching later.

ob_start();

renderMeta([
    'title' => "Silk Road Explorer — O'zbekistonning eksklyuziv sayohat portali",
    'description' => __('hero_subtitle'),
    'url' => BASE_URL . 'public/index.php'
]);

// Efficiently fetch data for the luxury homepage
$regions = $pdo->query("SELECT * FROM regions ORDER BY RAND() LIMIT 4")->fetchAll();

$places = $pdo->query("SELECT p.*, r.name_uz as region_name_uz, r.name_ru as region_name_ru, r.name_en as region_name_en 
                       FROM places p 
                       LEFT JOIN regions r ON p.region_id = r.id 
                       WHERE p.status = 'active' 
                       ORDER BY p.views DESC LIMIT 4")->fetchAll();

$hotels = $pdo->query("SELECT h.*, r.name_uz as region_name_uz, r.name_ru as region_name_ru, r.name_en as region_name_en 
                       FROM hotels h 
                       LEFT JOIN regions r ON h.region_id = r.id 
                       ORDER BY h.stars DESC LIMIT 4")->fetchAll();

$restaurants = $pdo->query("SELECT res.*, r.name_uz as region_name_uz, r.name_ru as region_name_ru, r.name_en as region_name_en 
                            FROM restaurants res 
                            LEFT JOIN regions r ON res.region_id = r.id 
                            ORDER BY res.rating DESC LIMIT 4")->fetchAll();

$people = $pdo->query("SELECT * FROM people WHERE status = 'active' ORDER BY RAND() LIMIT 3")->fetchAll();

// Global stats
$totalPlaces = (int)$pdo->query("SELECT COUNT(*) FROM places WHERE status='active'")->fetchColumn();
$totalHotels = (int)$pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();
$totalRestaurants = (int)$pdo->query("SELECT COUNT(*) FROM restaurants")->fetchColumn();

// Hero slider images
$heroImages = getHeroImages($pdo, 'home');
if (empty($heroImages)) {
    $heroImages = ['https://images.unsplash.com/photo-1548698517-c87d6023cb36?q=80&w=2500'];
}

require_once 'includes/layout_header.php';
?>

<!-- Import Google Fonts for Luxury Editorial Look -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Inter', sans-serif; background-color: #FAF7F2; color: #1A0E05; }
    .font-editorial { font-family: 'Noto Serif', serif; }
    
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    /* Elegant underline animation */
    .hover-underline {
        position: relative;
    }
    .hover-underline::after {
        content: ''; position: absolute; width: 100%; transform: scaleX(0); height: 1px; bottom: -2px; left: 0;
        background-color: #C9922A; transform-origin: bottom right; transition: transform 0.4s cubic-bezier(0.86, 0, 0.07, 1);
    }
    .hover-underline:hover::after {
        transform: scaleX(1); transform-origin: bottom left;
    }
</style>

<!-- ============================================== -->
<!-- 1. CINEMATIC HERO SECTION                      -->
<!-- ============================================== -->
<section class="relative w-full h-[90vh] min-h-[700px] flex items-center justify-center overflow-hidden">
    <!-- Immersive Background Slider -->
    <div class="absolute inset-0 z-0 bg-[#1A0E05]">
        <img id="hero-bg-img" src="<?= $heroImages[0] ?>" 
             alt="Silk Road Heritage" 
             class="w-full h-full object-cover transform scale-105 motion-safe:animate-[pulse_25s_ease-in-out_infinite] origin-center opacity-70 transition-opacity duration-1000 ease-in-out">
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
    
    <!-- Luxury Gradients -->
    <div class="absolute inset-0 z-0 bg-gradient-to-b from-[#1A0E05]/60 via-transparent to-[#FAF7F2]"></div>
    <div class="absolute inset-0 z-0 bg-gradient-to-r from-[#1B3A6B]/50 to-transparent"></div>

    <div class="relative z-10 text-center px-4 max-w-5xl mx-auto w-full flex flex-col items-center mt-20">
        <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-[#C9922A] text-[10px] font-bold tracking-[0.2em] uppercase mb-8" data-aos="fade-down">
            <span class="material-symbols-outlined text-[14px]">diamond</span>
            Eksklyuziv Sayohat
        </div>

        <h1 class="font-editorial text-5xl md:text-7xl lg:text-[100px] font-bold text-white mb-6 leading-[1.05] drop-shadow-2xl" data-aos="fade-up" data-aos-delay="100">
            Buyuk Ipak<br>
            <span class="text-[#C9922A] italic font-medium">Yo'li Merosi</span>
        </h1>
        
        <p class="font-editorial text-white/90 text-xl md:text-2xl mb-14 drop-shadow-md max-w-2xl font-light" data-aos="fade-up" data-aos-delay="200">
            Ming yillik tarix, saroylar hashamati va zamonaviy qulayliklarni kashf eting.
        </p>

        <!-- Glassmorphism Live Search -->
        <div x-data="liveSearch()" class="w-full flex justify-center relative z-50" data-aos="fade-up" data-aos-delay="300">
            <form action="search.php" method="GET" class="w-full max-w-2xl relative group">
                <div class="flex items-center bg-white/10 backdrop-blur-xl border border-white/20 rounded-full overflow-hidden shadow-[0_30px_60px_-15px_rgba(0,0,0,0.5)] p-1.5 transition-transform duration-500 focus-within:scale-[1.02] focus-within:bg-white/20">
                    <span class="material-symbols-outlined text-white/50 ml-5 mr-3 text-xl">search</span>
                    <input x-model="query" @input.debounce.300ms="fetchResults" name="q" placeholder="Qayerga sayohat qilasiz?" type="text" autocomplete="off"
                           class="flex-1 bg-transparent text-white placeholder-white/60 py-4 text-lg focus:outline-none border-none font-medium">
                    <button type="submit" class="bg-[#C9922A] hover:bg-white text-[#1A0E05] px-10 py-4 rounded-full font-bold transition-colors shadow-lg flex items-center gap-2 text-sm tracking-widest uppercase">
                        Qidirish
                    </button>
                </div>
            </form>

            <!-- Search Dropdown -->
            <div x-show="results.length > 0 || loading" @click.away="results = []; query = ''" x-transition class="absolute top-[110%] left-1/2 -translate-x-1/2 w-full max-w-2xl bg-white/95 backdrop-blur-3xl rounded-3xl shadow-2xl overflow-hidden text-left border border-[#1A0E05]/10" style="display:none">
                <div x-show="loading" class="p-8 text-center text-[#1B3A6B] font-medium text-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined animate-spin text-[#C9922A]">refresh</span>
                    Izlanmoqda...
                </div>
                <template x-for="item in results" :key="item.url">
                    <a :href="item.url" class="group flex items-center gap-5 px-6 py-4 hover:bg-[#FAF7F2] border-b border-[#1A0E05]/5 last:border-0 transition-colors">
                        <div class="w-14 h-14 rounded-2xl overflow-hidden bg-[#EBE5DA] shrink-0">
                            <img :src="item.image ? item.image : 'https://placehold.co/80x80/1A0E05/C9922A?text=SRE'" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <h4 class="font-editorial font-bold text-[#1A0E05] text-lg truncate group-hover:text-[#C9922A] transition-colors" x-text="item.name"></h4>
                            <p class="text-xs text-[#1A0E05]/50 font-bold uppercase tracking-widest mt-1" x-text="item.type"></p>
                        </div>
                        <span class="material-symbols-outlined text-[#1A0E05]/20 group-hover:text-[#C9922A] transition-colors">arrow_forward</span>
                    </a>
                </template>
            </div>
        </div>
    </div>
</section>

<!-- ============================================== -->
<!-- 2. FEATURED DESTINATIONS (Editorial Layout)    -->
<!-- ============================================== -->
<section class="py-32 bg-[#FAF7F2] relative">
    <div class="max-w-[1600px] mx-auto px-6 lg:px-10 xl:px-16">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6" data-aos="fade-up">
            <div class="max-w-2xl">
                <span class="text-[#C9922A] font-bold text-[10px] uppercase tracking-[0.2em] mb-4 block">Kashfiyot</span>
                <h2 class="font-editorial text-4xl md:text-6xl font-bold text-[#1B3A6B] leading-tight">Maftunkor <br>Manzillar</h2>
            </div>
            <a href="places/index.php" class="hover-underline text-[#1A0E05] font-bold uppercase tracking-widest text-xs flex items-center gap-2 pb-1">
                Barcha joylar <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6 h-auto lg:h-[600px]">
            <?php foreach($places as $i => $place): 
                $pName = localizedField($place, 'name');
                $rName = localizedField($place, 'region_name');
                
                // Asymmetrical grid logic: 
                // First item takes 6 cols, Second takes 3, Third takes 3
                // This creates a beautiful magazine-like layout.
                $colSpan = ($i === 0) ? 'lg:col-span-6' : (($i === 3) ? 'lg:col-span-12 hidden' : 'lg:col-span-3');
                if($i > 2) break; // Only show top 3 in this asymmetrical grid
            ?>
            <a href="places/view.php?id=<?= $place['id'] ?>" class="group block relative overflow-hidden rounded-[2rem] bg-[#EBE5DA] <?= $colSpan ?> h-[400px] lg:h-full cursor-pointer shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-700" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                <img loading="lazy" src="<?= publicImage($place['image'], $pName) ?>" alt="<?= htmlspecialchars($pName) ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-1000 ease-out">
                
                <div class="absolute inset-0 bg-gradient-to-t from-[#1A0E05]/90 via-[#1A0E05]/20 to-transparent opacity-80 group-hover:opacity-95 transition-opacity duration-500"></div>
                
                <div class="absolute bottom-0 left-0 w-full p-8 lg:p-10 flex flex-col justify-end h-full">
                    <span class="text-[#C9922A] text-[10px] font-bold uppercase tracking-[0.2em] mb-3 flex items-center gap-2">
                        <span class="w-4 h-[1px] bg-[#C9922A]"></span> <?= htmlspecialchars($rName ?: "O'zbekiston") ?>
                    </span>
                    <h3 class="font-editorial font-bold text-white text-3xl md:text-4xl leading-tight mb-4"><?= htmlspecialchars($pName) ?></h3>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================== -->
<!-- 3. CURATED STAYS (Luxury Hotels)               -->
<!-- ============================================== -->
<?php if(!empty($hotels)): ?>
<section class="py-32 bg-white border-t border-[#1A0E05]/5">
    <div class="max-w-[1600px] mx-auto px-6 lg:px-10 xl:px-16">
        
        <div class="text-center max-w-3xl mx-auto mb-20" data-aos="fade-up">
            <span class="material-symbols-outlined text-4xl text-[#C9922A] mb-4">hotel_class</span>
            <h2 class="font-editorial text-4xl md:text-5xl font-bold text-[#1A0E05] mb-6">Qirollarga Xos Dam Olish</h2>
            <p class="text-[#1A0E05]/60 text-lg font-light">Tarixiy obidalar yonidagi eng hashamatli va shinam mehmonxonalar to'plami.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach($hotels as $i => $hotel): 
                $hName = localizedField($hotel, 'name');
            ?>
            <a href="hotels/view.php?id=<?= $hotel['id'] ?>" class="group block" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                <div class="h-80 rounded-[2rem] overflow-hidden mb-6 bg-[#FAF7F2] relative shadow-sm group-hover:shadow-xl transition-shadow duration-500">
                    <img loading="lazy" src="<?= publicImage($hotel['image'], $hName) ?>" alt="<?= htmlspecialchars($hName) ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                    
                    <?php if($hotel['price_from']): ?>
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1.5 rounded-xl border border-white/50 text-[#1B3A6B] font-bold text-sm shadow-lg">
                        $<?= number_format((float)$hotel['price_from'], 0, '.', ' ') ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="px-2">
                    <div class="flex items-center gap-1 text-[#C9922A] mb-2">
                        <?php for($s=0; $s<$hotel['stars']; $s++): ?>
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <?php endfor; ?>
                    </div>
                    <h3 class="font-editorial text-2xl font-bold text-[#1A0E05] mb-1 group-hover:text-[#1B3A6B] transition-colors"><?= htmlspecialchars($hName) ?></h3>
                    <p class="text-[#1A0E05]/50 text-sm font-medium uppercase tracking-widest"><?= htmlspecialchars(localizedField($hotel, 'region_name') ?: "O'zbekiston") ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-16" data-aos="fade-up">
            <a href="hotels/index.php" class="inline-flex items-center gap-3 bg-[#1A0E05] hover:bg-[#C9922A] text-white px-8 py-4 rounded-full font-bold uppercase tracking-widest text-xs transition-colors shadow-xl">
                Barcha Mehmonxonalar
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================== -->
<!-- 4. FINE DINING (Restaurants)                   -->
<!-- ============================================== -->
<?php if(!empty($restaurants)): ?>
<section class="py-32 bg-[#1A0E05] relative overflow-hidden">
    <!-- Subtle Pattern -->
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
    
    <div class="max-w-[1600px] mx-auto px-6 lg:px-10 xl:px-16 relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6" data-aos="fade-up">
            <div class="max-w-2xl">
                <span class="text-[#C9922A] font-bold text-[10px] uppercase tracking-[0.2em] mb-4 block">Gastronomiya</span>
                <h2 class="font-editorial text-4xl md:text-6xl font-bold text-white leading-tight">Pazandachilik<br>San'ati</h2>
            </div>
            <a href="restaurants/index.php" class="hover-underline text-white font-bold uppercase tracking-widest text-xs flex items-center gap-2 pb-1">
                Barcha restoranlar <span class="material-symbols-outlined text-sm text-[#C9922A]">arrow_forward</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach($restaurants as $i => $rest): 
                $rName = $rest['name'];
            ?>
            <a href="restaurants/view.php?id=<?= $rest['id'] ?>" class="group block bg-[#22130A] rounded-[2rem] overflow-hidden border border-white/5 hover:border-[#C9922A]/30 transition-colors h-full flex flex-col" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                <div class="h-56 relative overflow-hidden shrink-0">
                    <img loading="lazy" src="<?= publicImage($rest['image_path'], $rName) ?>" alt="<?= htmlspecialchars($rName) ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#22130A] to-transparent opacity-80"></div>
                    
                    <?php if($rest['rating'] > 0): ?>
                    <div class="absolute top-4 right-4 bg-black/50 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/10 text-white text-xs font-bold flex items-center gap-1">
                        <svg class="w-3 h-3 text-[#C9922A]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <?= number_format($rest['rating'], 1) ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="p-8 pt-0 flex-grow flex flex-col">
                    <h3 class="font-editorial text-2xl font-bold text-white mb-2 group-hover:text-[#C9922A] transition-colors"><?= htmlspecialchars($rName) ?></h3>
                    <p class="text-[#C9922A] text-xs uppercase tracking-widest font-semibold mb-4"><?= htmlspecialchars($rest['cuisine_type'] ?? 'Restoran') ?></p>
                    
                    <div class="mt-auto flex items-center gap-2 text-white/50 text-sm font-medium pt-4 border-t border-white/10">
                        <span class="material-symbols-outlined text-[16px]">location_on</span>
                        <?= htmlspecialchars(localizedField($rest, 'region_name') ?: "O'zbekiston") ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================== -->
<!-- 5. HERITAGE & CULTURE (People)                 -->
<!-- ============================================== -->
<?php if(!empty($people)): ?>
<section class="py-32 bg-[#FAF7F2]">
    <div class="max-w-[1600px] mx-auto px-6 lg:px-10 xl:px-16">
        <div class="flex flex-col lg:flex-row gap-16 items-center">
            
            <!-- Text Content -->
            <div class="w-full lg:w-1/3" data-aos="fade-right">
                <span class="text-[#C9922A] font-bold text-[10px] uppercase tracking-[0.2em] mb-4 block">Meros</span>
                <h2 class="font-editorial text-4xl md:text-5xl font-bold text-[#1B3A6B] mb-6 leading-tight">Buyuk<br>Allomalar</h2>
                <p class="text-[#1A0E05]/60 text-lg mb-10 font-light leading-relaxed">
                    Jahon sivilizatsiyasiga ulkan hissa qo'shgan olimlar, sarkardalar va mutafakkirlar hayoti bilan tanishing.
                </p>
                <a href="people/index.php" class="inline-flex items-center gap-3 bg-transparent hover:bg-[#1B3A6B] border border-[#1B3A6B] text-[#1B3A6B] hover:text-white px-8 py-4 rounded-full font-bold uppercase tracking-widest text-xs transition-colors">
                    Muzeyga kirish
                </a>
            </div>

            <!-- Image Grid -->
            <div class="w-full lg:w-2/3 grid grid-cols-1 sm:grid-cols-3 gap-6">
                <?php foreach($people as $i => $person): 
                    $perName = localizedField($person, 'name');
                    $mt = ($i === 1) ? 'sm:mt-12' : (($i === 2) ? 'sm:mt-24' : ''); // Staggered layout
                ?>
                <a href="people/view.php?id=<?= $person['id'] ?>" class="group block <?= $mt ?>" data-aos="fade-up" data-aos-delay="<?= $i * 150 ?>">
                    <div class="aspect-[3/4] rounded-[2rem] overflow-hidden relative shadow-lg group-hover:shadow-2xl transition-all duration-700">
                        <img loading="lazy" src="<?= publicImage($person['image'], $perName) ?>" alt="<?= htmlspecialchars($perName) ?>" class="w-full h-full object-cover filter grayscale group-hover:grayscale-0 transform group-hover:scale-105 transition-all duration-1000">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#1A0E05] via-transparent to-transparent opacity-90"></div>
                        
                        <div class="absolute bottom-6 left-6 right-6">
                            <span class="text-[#C9922A] font-bold text-[10px] uppercase tracking-widest mb-2 block">
                                <?= $person['born_year'] ?> - <?= $person['died_year'] ?: 'Hozirgacha' ?>
                            </span>
                            <h3 class="font-editorial font-bold text-white text-xl leading-tight"><?= htmlspecialchars($perName) ?></h3>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================== -->
<!-- 6. FINAL CTA                                   -->
<!-- ============================================== -->
<section class="py-32 bg-[#1B3A6B] relative overflow-hidden">
    <div class="absolute inset-0 opacity-20 pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/arabesque.png')]"></div>

    <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center relative z-10" data-aos="zoom-in">
        <h2 class="font-editorial text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">Sayohatni<br>Boshlang</h2>
        <p class="text-[#FAF7F2]/80 text-xl mb-12 max-w-2xl mx-auto font-light">
            Sizning unutilmas tajribangiz bitta tugma ortida.
        </p>
        <div class="flex flex-col sm:flex-row gap-6 justify-center">
            <a href="places/index.php" class="bg-[#C9922A] text-[#1A0E05] px-10 py-4 rounded-full font-bold tracking-widest text-xs uppercase hover:bg-white transition-colors shadow-2xl">
                Joylar bilan tanishish
            </a>
            <a href="map.php" class="bg-transparent text-white px-10 py-4 rounded-full font-bold tracking-widest text-xs uppercase border border-white/30 hover:border-[#C9922A] hover:text-[#C9922A] transition-colors">
                Xaritani ochish
            </a>
        </div>
    </div>
</section>

<!-- Include Alpine.js Component logic -->
<script>
function liveSearch() {
    return {
        query: '', 
        results: [], 
        loading: false,
        async fetchResults() {
            if (this.query.trim().length < 2) { 
                this.results = []; 
                return; 
            }
            this.loading = true;
            try {
                // Ensure API handles CORS or is relative correctly
                const res = await fetch(`<?= BASE_URL ?>public/api/search.php?q=${encodeURIComponent(this.query)}`);
                if(res.ok) {
                    this.results = await res.json();
                } else {
                    this.results = [];
                }
            } catch (e) { 
                console.error(e); 
                this.results = [];
            }
            this.loading = false;
        }
    }
}

</script>

<?php
require_once 'includes/layout_footer.php';
$output = ob_get_clean();
echo $output;
?>
