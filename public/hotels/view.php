<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../includes/functions.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit; }

$stmt = $pdo->prepare("SELECT h.*, reg.name_uz as region_name, reg.name_ru as region_name_ru, reg.name_en as region_name_en FROM hotels h LEFT JOIN regions reg ON h.region_id = reg.id WHERE h.id = :id AND h.status = 'active'");
$stmt->execute(['id' => $id]);
$hotel = $stmt->fetch();

if (!$hotel) { http_response_code(404); require_once '../404.php'; exit; }

try { $pdo->prepare("UPDATE hotels SET views = views + 1 WHERE id = :id")->execute(['id' => $id]); } catch (PDOException $e) {}

// Gallery
$gallery = $pdo->prepare("SELECT * FROM gallery WHERE entity_type = 'hotel' AND entity_id = :id ORDER BY id DESC");
$gallery->execute(['id' => $id]);
$galleryImages = $gallery->fetchAll();

// Related hotels
$stmtRelated = $pdo->prepare("SELECT h.id, h.name_uz, h.name_ru, h.name_en, h.image, h.stars, h.price_from FROM hotels h WHERE h.region_id = :region_id AND h.id != :id AND h.status = 'active' ORDER BY h.id DESC LIMIT 4");
$stmtRelated->execute(['region_id' => $hotel['region_id'], 'id' => $hotel['id']]);
$related = $stmtRelated->fetchAll();

// Nearby restaurants
$nearbyRests = [];
if ($hotel['region_id']) {
    $sr = $pdo->prepare("SELECT id, name_uz, name_ru, name_en, image, cuisine_type FROM restaurants WHERE region_id = :rid AND status = 'active' LIMIT 3");
    $sr->execute(['rid' => $hotel['region_id']]);
    $nearbyRests = $sr->fetchAll();
}

// Reviews stats
$revStats = $pdo->prepare("SELECT AVG(rating) as avg_r, COUNT(*) as cnt FROM reviews WHERE entity_type='hotel' AND entity_id=:id AND status='approved'");
$revStats->execute(['id' => $id]);
$reviewStats = $revStats->fetch();

$lang = currentLang();
$hotelName = localizedField($hotel, 'name');
$hotelDesc = localizedField($hotel, 'description');
$regName = localizedField($hotel, 'region_name');

$pageTitle = htmlspecialchars($hotelName);
require_once '../includes/seo.php';
$desc = mb_substr(strip_tags($hotelDesc), 0, 160);
$img = $hotel['image'] ? BASE_URL . trim(UPLOAD_DIR, '/') . '/' . ltrim($hotel['image'], '/') : null;
renderMeta(['title' => $pageTitle . ' | Silk Road Explorer', 'description' => $desc, 'image' => $img]);
require_once '../includes/layout_header.php';

$currentUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

function renderStarsV($count) {
    $count = (int)$count;
    $html = '';
    for($i = 1; $i <= 5; $i++) {
        $color = $i <= $count ? 'text-amber-400' : 'text-white/20';
        $html .= '<svg class="w-5 h-5 ' . $color . '" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
    }
    return '<div class="flex items-center gap-0.5">' . $html . '</div>';
}
?>

<!-- Hero -->
<section class="relative h-[50vh] min-h-[400px] lg:h-[60vh] bg-silk-950 overflow-hidden">
    <img src="<?= publicImage($hotel['image'], $hotelName) ?>" alt="<?= htmlspecialchars($hotelName) ?>" class="w-full h-full object-cover opacity-50">
    <div class="absolute inset-0 bg-gradient-to-t from-silk-950 via-silk-950/50 to-transparent"></div>
    
    <div class="absolute bottom-0 left-0 w-full">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 pb-12">
            <div class="flex items-center gap-2 text-amber-400/60 text-sm mb-6" data-aos="fade-up">
                <a href="<?= BASE_URL ?>public/index.php" class="hover:text-amber-400 transition-colors"><?= __('home') ?></a>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="index.php" class="hover:text-amber-400 transition-colors"><?= __('nav_hotels') ?></a>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-amber-400"><?= htmlspecialchars(mb_substr($hotelName, 0, 30)) ?></span>
            </div>

            <div class="flex flex-wrap items-center gap-3 mb-5" data-aos="fade-up" data-aos-delay="100">
                <div class="bg-black/30 backdrop-blur-md px-3 py-2 rounded-lg border border-white/10">
                    <?= renderStarsV($hotel['stars']) ?>
                </div>
                <?php if($hotel['price_from']): ?>
                <span class="bg-amber-500 text-silk-950 px-4 py-1.5 rounded-lg text-xs font-bold shadow-lg shadow-amber-500/25">
                    <?= number_format((float)$hotel['price_from'], 0, '.', ' ') ?> <?= __('price_from') ?>
                </span>
                <?php endif; ?>
                <?php if($regName): ?>
                <span class="bg-white/10 backdrop-blur text-white px-4 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1.5 border border-white/10">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                    <?= htmlspecialchars($regName) ?>
                </span>
                <?php endif; ?>
                <span class="bg-white/10 backdrop-blur text-white/70 px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1.5 border border-white/10">
                    <span class="material-symbols-outlined text-xs">visibility</span>
                    <?= number_format($hotel['views'] ?? 0) ?>
                </span>
                <?php if($reviewStats['cnt'] > 0): ?>
                <span class="bg-amber-500/20 backdrop-blur text-amber-300 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1 border border-amber-500/20">
                    ★ <?= round($reviewStats['avg_r'], 1) ?> (<?= $reviewStats['cnt'] ?>)
                </span>
                <?php endif; ?>
            </div>
            
            <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold text-white" data-aos="fade-up" data-aos-delay="200"><?= htmlspecialchars($hotelName) ?></h1>

            <!-- Action buttons -->
            <div class="flex flex-wrap gap-3 mt-6" data-aos="fade-up" data-aos-delay="300"
                 x-data="{ isFav: false, copied: false }"
                 x-init="fetch('<?= BASE_URL ?>public/api/favorites.php?type=hotel&id=<?= $id ?>').then(r=>r.json()).then(d=>isFav=d.is_favorite)">
                <button @click="fetch('<?= BASE_URL ?>public/api/favorites.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({type:'hotel',id:<?= $id ?>})}).then(r=>r.json()).then(d=>isFav=d.is_favorite)"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all border"
                        :class="isFav ? 'bg-amber-500 text-silk-950 border-amber-500' : 'bg-white/10 text-white border-white/15 hover:border-amber-400'">
                    <span class="material-symbols-outlined text-base" :style="isFav && 'font-variation-settings: \'FILL\' 1'">favorite</span>
                    <span x-text="isFav ? '<?= __('remove_favorite') ?>' : '<?= __('add_favorite') ?>'"></span>
                </button>
                <button @click="navigator.clipboard.writeText('<?= $currentUrl ?>'); copied=true; setTimeout(()=>copied=false, 2000)"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-white/10 text-white border border-white/15 hover:border-amber-400 transition-all">
                    <span class="material-symbols-outlined text-base">share</span>
                    <span x-text="copied ? '<?= __('copied') ?>' : '<?= __('share') ?>'"></span>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section class="py-16 bg-white" x-data="{ tab: '<?= $lang ?>' }">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col lg:flex-row gap-12">
        
        <!-- Main -->
        <div class="flex-1 min-w-0" data-aos="fade-up">
            <div class="mb-10 border-b border-silk-200">
                <nav class="-mb-px flex gap-1">
                    <button @click="tab='uz'" :class="tab==='uz' ? 'border-amber-500 text-amber-700 bg-amber-50' : 'border-transparent text-silk-400 hover:text-silk-600'" class="px-5 py-3 border-b-2 font-semibold text-sm rounded-t-lg transition-all"><?= __('lang_uz') ?></button>
                    <button @click="tab='ru'" :class="tab==='ru' ? 'border-amber-500 text-amber-700 bg-amber-50' : 'border-transparent text-silk-400 hover:text-silk-600'" class="px-5 py-3 border-b-2 font-semibold text-sm rounded-t-lg transition-all"><?= __('lang_ru') ?></button>
                    <button @click="tab='en'" :class="tab==='en' ? 'border-amber-500 text-amber-700 bg-amber-50' : 'border-transparent text-silk-400 hover:text-silk-600'" class="px-5 py-3 border-b-2 font-semibold text-sm rounded-t-lg transition-all"><?= __('lang_en') ?></button>
                </nav>
            </div>
            <article class="prose prose-lg max-w-none text-silk-700 leading-relaxed mb-12">
                <div x-show="tab==='uz'"><?= nl2br(htmlspecialchars($hotel['description_uz'] ?? __('no_info'))) ?></div>
                <div x-show="tab==='ru'" style="display:none"><?= nl2br(htmlspecialchars($hotel['description_ru'] ?? __('no_info'))) ?></div>
                <div x-show="tab==='en'" style="display:none"><?= nl2br(htmlspecialchars($hotel['description_en'] ?? __('no_info'))) ?></div>
            </article>

            <!-- Map Section -->
            <?php if ($hotel['latitude'] && $hotel['longitude']): ?>
            <div class="mt-12 pt-8 border-t border-silk-200" data-aos="fade-up">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-silk-900 text-xl flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500 text-2xl">location_on</span> <?= __('map') ?>
                    </h3>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $hotel['latitude'] ?>,<?= $hotel['longitude'] ?>" target="_blank" 
                       class="flex items-center gap-2 bg-gradient-to-r from-amber-500 to-amber-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-amber-500/20 hover:scale-105 active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-base">directions</span> <?= __('directions') ?>
                    </a>
                </div>
                <div class="rounded-3xl overflow-hidden shadow-2xl border border-silk-200 relative group">
                    <div id="detail-map" class="w-full h-[450px]" style="z-index:1"></div>
                </div>
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <script>
                document.addEventListener('DOMContentLoaded', function(){
                    const m = L.map('detail-map',{zoomControl:false,scrollWheelZoom:false}).setView([<?= $hotel['latitude'] ?>,<?= $hotel['longitude'] ?>], 15);
                    L.control.zoom({position:'bottomright'}).addTo(m);
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',{attribution:'&copy; OSM &copy; CARTO',subdomains:'abcd',maxZoom:19}).addTo(m);
                    
                    const customIcon = L.divIcon({
                        className: '',
                        html: `<div style="display:flex; align-items:center; justify-content:center; width:48px; height:48px; background:linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius:50%; border:4px solid white; box-shadow:0 10px 25px rgba(139,92,246,0.5);">
                                 <span class="material-symbols-outlined" style="color:white; font-size:24px;">hotel</span>
                               </div>`,
                        iconSize: [48, 48],
                        iconAnchor: [24, 48],
                        popupAnchor: [0, -50]
                    });

                    L.marker([<?= $hotel['latitude'] ?>,<?= $hotel['longitude'] ?>], {icon: customIcon})
                     .addTo(m)
                     .bindPopup(`
                        <div style="text-align:center; padding:5px;">
                            <h4 style="font-weight:bold; font-size:16px; margin:0 0 5px 0; color:#2a1f18;"><?= htmlspecialchars(addslashes($hotelName)) ?></h4>
                            <p style="font-size:12px; color:#666; margin:0;"><?= htmlspecialchars(addslashes($hotel['address'] ?? '')) ?></p>
                        </div>
                     `, {
                         closeButton: false,
                         className: 'premium-popup'
                     }).openPopup();
                });
                </script>
                <style>
                    .premium-popup .leaflet-popup-content-wrapper { border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); padding: 10px; }
                    .premium-popup .leaflet-popup-tip { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
                </style>
            </div>
            <?php endif; ?>

            <!-- Video Player -->
            <?php if (!empty($hotel['video_url'])): ?>
            <div class="mt-12 pt-8 border-t border-silk-200" data-aos="fade-up">
                <h3 class="font-bold text-silk-900 text-lg mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500">play_circle</span> <?= __('video_tour') ?>
                </h3>
                <div class="rounded-3xl overflow-hidden shadow-2xl bg-black aspect-video border border-silk-200">
                    <?php if (strpos($hotel['video_url'], 'youtube.com') !== false || strpos($hotel['video_url'], 'youtu.be') !== false): 
                        $ytId = '';
                        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $hotel['video_url'], $match)) {
                            $ytId = $match[1];
                        }
                    ?>
                        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/<?= $ytId ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    <?php else: ?>
                        <video controls class="w-full h-full"><source src="<?= htmlspecialchars($hotel['video_url']) ?>" type="video/mp4"></video>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Gallery Carousel -->
            <?php if (!empty($galleryImages)): ?>
            <div class="mt-12 pt-8 border-t border-silk-200" data-aos="fade-up" x-data="{ 
                current: 0, 
                total: <?= count($galleryImages) ?>,
                next() { this.current = (this.current + 1) % this.total },
                prev() { this.current = (this.current - 1 + this.total) % this.total }
            }">
                <h3 class="font-bold text-silk-900 text-lg mb-6 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500">photo_library</span> <?= __('gallery') ?>
                    </span>
                    <span class="text-xs font-normal text-silk-400" x-text="(current+1) + ' / <?= count($galleryImages) ?>'"></span>
                </h3>
                
                <div class="relative rounded-2xl overflow-hidden bg-silk-100 aspect-[16/9] max-h-[400px]">
                    <?php foreach($galleryImages as $idx => $gi): ?>
                    <div x-show="current === <?= $idx ?>" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute inset-0 flex items-center justify-center cursor-pointer" onclick="openLightbox('<?= publicImage($gi['image_path']) ?>')">
                        <img src="<?= publicImage($gi['image_path']) ?>" class="max-w-full max-h-full object-contain">
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (count($galleryImages) > 1): ?>
                    <button @click="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/40 hover:bg-black/60 text-white rounded-full flex items-center justify-center backdrop-blur transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/40 hover:bg-black/60 text-white rounded-full flex items-center justify-center backdrop-blur transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <?php endif; ?>
                </div>
                
                <?php if (count($galleryImages) > 1): ?>
                <div class="flex gap-2 mt-3 overflow-x-auto hide-scrollbar pb-1">
                    <?php foreach($galleryImages as $idx => $gi): ?>
                    <div @click="current = <?= $idx ?>" class="shrink-0 w-20 h-14 rounded-lg overflow-hidden cursor-pointer border-2 transition-all" :class="current === <?= $idx ?> ? 'border-amber-500 opacity-100' : 'border-transparent opacity-50 hover:opacity-75'">
                        <img src="<?= publicImage($gi['image_path']) ?>" class="w-full h-full object-cover">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Reviews Section -->
            <div class="mt-12 pt-8 border-t border-silk-200" x-data="reviewForm()" x-init="loadReviews()">
                <h3 class="font-bold text-silk-900 text-lg mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500">rate_review</span>
                    <?= __('reviews') ?>
                    <span class="bg-amber-100 text-amber-700 text-xs px-2.5 py-0.5 rounded-full font-bold" x-text="reviewCount"></span>
                </h3>

                <div class="bg-silk-50 rounded-2xl p-6 border border-silk-200/60 mb-8">
                    <h4 class="font-semibold text-silk-800 text-sm mb-4"><?= __('write_review') ?></h4>
                    <div class="flex flex-col gap-4">
                        <input x-model="newName" type="text" placeholder="<?= __('your_name') ?>" class="bg-white border border-silk-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500/30 focus:border-amber-400">
                        <div class="star-rating">
                            <template x-for="i in [5,4,3,2,1]" :key="i">
                                <span>
                                    <input type="radio" :id="'star'+i" :value="i" x-model.number="newRating">
                                    <label :for="'star'+i" @click="newRating=i">★</label>
                                </span>
                            </template>
                        </div>
                        <textarea x-model="newComment" rows="3" placeholder="<?= __('your_comment') ?>" class="bg-white border border-silk-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500/30 focus:border-amber-400 resize-none"></textarea>
                        <div class="flex items-center gap-3">
                            <button @click="submitReview()" :disabled="submitting" class="bg-gradient-to-r from-amber-500 to-amber-600 text-silk-950 px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-amber-500/20 active:scale-95 disabled:opacity-50"><?= __('submit_review') ?></button>
                            <span x-show="successMsg" x-transition class="text-emerald-600 text-sm font-medium" x-text="successMsg"></span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <template x-for="rev in reviews" :key="rev.id">
                        <div class="bg-white rounded-xl p-5 border border-silk-200/60">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-silk-800 text-sm" x-text="rev.author_name"></span>
                                <span class="text-xs text-silk-400" x-text="new Date(rev.created_at).toLocaleDateString()"></span>
                            </div>
                            <div class="flex gap-0.5 mb-2">
                                <template x-for="s in 5"><span class="text-sm" :class="s <= rev.rating ? 'text-amber-400' : 'text-silk-200'">★</span></template>
                            </div>
                            <p class="text-silk-600 text-sm leading-relaxed" x-text="rev.comment"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="w-full lg:w-80 shrink-0 space-y-6" data-aos="fade-left">
            <!-- Info -->
            <div class="bg-silk-50 rounded-2xl p-6 border border-silk-200/60">
                <h3 class="font-bold text-silk-900 text-sm uppercase tracking-wider mb-5 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500 text-lg">info</span>
                    <?= __('info') ?>
                </h3>
                <ul class="space-y-4">
                    <?php if (!empty($hotel['phone'])): ?>
                    <li class="flex items-start gap-3 text-silk-700">
                        <span class="material-symbols-outlined text-amber-500 text-lg mt-0.5">call</span>
                        <div>
                            <p class="text-xs font-semibold text-silk-400 uppercase tracking-wider mb-0.5"><?= __('phone') ?></p>
                            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $hotel['phone']) ?>" class="text-sm text-amber-600 font-semibold hover:text-amber-700"><?= htmlspecialchars($hotel['phone']) ?></a>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($hotel['address'])): ?>
                    <li class="flex items-start gap-3 text-silk-700">
                        <span class="material-symbols-outlined text-amber-500 text-lg mt-0.5">location_on</span>
                        <div>
                            <p class="text-xs font-semibold text-silk-400 uppercase tracking-wider mb-0.5"><?= __('address') ?></p>
                            <p class="text-sm"><?= htmlspecialchars($hotel['address']) ?></p>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Booking Form -->
            <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 rounded-2xl p-6 border border-amber-200/60" x-data="bookingForm()" data-aos="fade-up">
                <h3 class="font-bold text-silk-900 text-sm uppercase tracking-wider mb-5 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500 text-lg">calendar_month</span>
                    <?= __('book_hotel') ?>
                </h3>
                <div class="space-y-3">
                    <input x-model="form.guest_name" type="text" placeholder="<?= __('guest_name') ?> *" class="w-full bg-white border border-silk-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500/30 focus:border-amber-400">
                    <input x-model="form.guest_phone" type="tel" placeholder="<?= __('guest_phone') ?> *" class="w-full bg-white border border-silk-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500/30 focus:border-amber-400">
                    <input x-model="form.guest_email" type="email" placeholder="<?= __('guest_email') ?>" class="w-full bg-white border border-silk-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500/30 focus:border-amber-400">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[10px] font-bold text-silk-400 uppercase tracking-wider mb-1 block"><?= __('check_in') ?></label>
                            <input x-model="form.check_in" type="date" :min="today" class="w-full bg-white border border-silk-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-500/30 focus:border-amber-400">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-silk-400 uppercase tracking-wider mb-1 block"><?= __('check_out') ?></label>
                            <input x-model="form.check_out" type="date" :min="form.check_in || today" class="w-full bg-white border border-silk-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-500/30 focus:border-amber-400">
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-silk-400 uppercase tracking-wider mb-1 block"><?= __('guests_count') ?></label>
                        <select x-model="form.guests_count" class="w-full bg-white border border-silk-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500/30 focus:border-amber-400">
                            <template x-for="i in 10"><option :value="i" x-text="i + ' <?= __('guests_count') ?>'"></option></template>
                        </select>
                    </div>
                    <textarea x-model="form.message" rows="2" placeholder="<?= __('booking_message') ?>" class="w-full bg-white border border-silk-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500/30 focus:border-amber-400 resize-none"></textarea>
                    <button @click="submitBooking()" :disabled="submitting" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 text-silk-950 font-bold py-3.5 rounded-xl shadow-lg shadow-amber-500/25 active:scale-[0.98] transition-all disabled:opacity-50 text-sm flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-base">event_available</span>
                        <?= __('book_now') ?>
                    </button>
                    <p x-show="successMsg" x-transition class="text-emerald-600 text-xs font-semibold text-center bg-emerald-50 rounded-lg py-2" x-text="successMsg"></p>
                    <p x-show="errorMsg" x-transition class="text-red-600 text-xs font-semibold text-center bg-red-50 rounded-lg py-2" x-text="errorMsg"></p>
                </div>
            </div>


            <!-- Nearby Restaurants -->
            <?php if (!empty($nearbyRests)): ?>
            <div class="bg-silk-50 rounded-2xl p-6 border border-silk-200/60">
                <h3 class="font-bold text-silk-900 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500 text-lg">restaurant</span> <?= __('nearby_rests') ?>
                </h3>
                <div class="space-y-3">
                    <?php foreach($nearbyRests as $nr): ?>
                    <a href="<?= BASE_URL ?>public/restaurants/view.php?id=<?= $nr['id'] ?>" class="flex items-center gap-3 group">
                        <div class="w-12 h-12 rounded-lg overflow-hidden bg-silk-200 shrink-0">
                            <img src="<?= publicImage($nr['image'], localizedField($nr, 'name')) ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-silk-800 truncate group-hover:text-amber-700 transition-colors"><?= htmlspecialchars(localizedField($nr, 'name')) ?></p>
                            <p class="text-xs text-silk-400"><?= htmlspecialchars($nr['cuisine_type'] ?? '') ?></p>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Related -->
<?php if (count($related) > 0): ?>
<section class="py-16 bg-silk-50 border-t border-silk-200/60">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <h2 class="font-display text-2xl font-bold text-silk-900 mb-8" data-aos="fade-up"><?= __('related_hotels') ?></h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach($related as $i => $rel): ?>
                <a href="view.php?id=<?= $rel['id'] ?>" class="bg-white rounded-2xl overflow-hidden border border-silk-200/60 card-hover group" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                    <div class="h-44 overflow-hidden bg-silk-100">
                        <img loading="lazy" src="<?= publicImage($rel['image'], localizedField($rel, 'name')) ?>" alt="<?= htmlspecialchars(localizedField($rel, 'name')) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-silk-900 text-sm mb-1 group-hover:text-amber-700 transition-colors line-clamp-1"><?= htmlspecialchars(localizedField($rel, 'name')) ?></h3>
                        <div class="flex items-center justify-between mt-2">
                            <div class="flex">
                                <?php for($s=1;$s<=5;$s++): ?>
                                <span class="text-[10px] <?= $s<=(int)$rel['stars'] ? 'text-amber-400' : 'text-silk-200' ?>">★</span>
                                <?php endfor; ?>
                            </div>
                            <?php if($rel['price_from']): ?>
                            <p class="text-[10px] text-amber-600 font-bold"><?= number_format((float)$rel['price_from'], 0, '.', ' ') ?> <?= __('price_from') ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
function reviewForm() {
    return {
        reviews: [], reviewCount: 0, newName: '', newRating: 5, newComment: '', submitting: false, successMsg: '',
        async loadReviews() {
            const r = await fetch('<?= BASE_URL ?>public/api/reviews.php?type=hotel&id=<?= $id ?>');
            const d = await r.json();
            this.reviews = d.reviews || [];
            this.reviewCount = d.total || 0;
        },
        async submitReview() {
            if (this.newComment.length < 3) return;
            this.submitting = true;
            await fetch('<?= BASE_URL ?>public/api/reviews.php', {
                method: 'POST', headers: {'Content-Type':'application/json'},
                body: JSON.stringify({type:'hotel', id:<?= $id ?>, name:this.newName, rating:this.newRating, comment:this.newComment})
            });
            this.successMsg = '<?= __('review_pending') ?>';
            this.newComment = ''; this.newName = ''; this.newRating = 5;
            this.submitting = false;
            setTimeout(() => this.successMsg = '', 4000);
        }
    }
}
function bookingForm() {
    return {
        today: new Date().toISOString().split('T')[0],
        form: { guest_name:'', guest_phone:'', guest_email:'', check_in:'', check_out:'', guests_count:1, message:'' },
        submitting: false, successMsg: '', errorMsg: '',
        async submitBooking() {
            this.errorMsg = ''; this.successMsg = '';
            if (!this.form.guest_name || !this.form.guest_phone || !this.form.check_in) {
                this.errorMsg = '<?= __('booking_fill_all') ?>'; return;
            }
            this.submitting = true;
            try {
                const r = await fetch('<?= BASE_URL ?>public/api/bookings.php', {
                    method:'POST', headers:{'Content-Type':'application/json'},
                    body: JSON.stringify({...this.form, entity_type:'hotel', entity_id:<?= $id ?>})
                });
                const d = await r.json();
                if (d.success) {
                    this.successMsg = '<?= __('booking_success') ?>';
                    this.form = {guest_name:'',guest_phone:'',guest_email:'',check_in:'',check_out:'',guests_count:1,message:''};
                } else {
                    this.errorMsg = (d.errors && d.errors[0]) || '<?= __('booking_error') ?>';
                }
            } catch(e) { this.errorMsg = '<?= __('booking_error') ?>'; }
            this.submitting = false;
            setTimeout(() => { this.successMsg = ''; this.errorMsg = ''; }, 6000);
        }
    }
}
</script>

<?php require_once '../includes/layout_footer.php'; ?>
