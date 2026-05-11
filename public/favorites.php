<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once 'includes/functions.php';

$lang = currentLang();
$pageTitle = __('nav_favorites');
require_once 'includes/seo.php';
renderMeta(['title' => $pageTitle . ' | Silk Road Explorer']);
require_once 'includes/layout_header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$sessionId = session_id();

// Fetch favorites
$favorites = [];

// Places
$stmt = $pdo->prepare("
    SELECT p.id, p.name_uz, p.name_ru, p.name_en, p.image, p.description_uz, p.description_ru, p.description_en, 'place' as type
    FROM favorites f JOIN places p ON f.entity_id = p.id
    WHERE f.session_id = :sid AND f.entity_type = 'place' AND p.status = 'active'
    ORDER BY f.created_at DESC
");
$stmt->execute(['sid' => $sessionId]);
foreach($stmt->fetchAll() as $row) {
    $row['link'] = 'places/view.php?id=' . $row['id'];
    $row['type_label'] = __('type_place');
    $favorites[] = $row;
}

// People
$stmt = $pdo->prepare("
    SELECT p.id, p.name_uz, p.name_ru, p.name_en, p.image, NULL as description_uz, 'person' as type
    FROM favorites f JOIN people p ON f.entity_id = p.id
    WHERE f.session_id = :sid AND f.entity_type = 'person' AND p.status = 'active'
    ORDER BY f.created_at DESC
");
$stmt->execute(['sid' => $sessionId]);
foreach($stmt->fetchAll() as $row) {
    $row['link'] = 'people/view.php?id=' . $row['id'];
    $row['type_label'] = __('type_person');
    $favorites[] = $row;
}

// Hotels
$stmt = $pdo->prepare("
    SELECT h.id, h.name_uz, h.name_ru, h.name_en, h.image, h.description_uz, 'hotel' as type
    FROM favorites f JOIN hotels h ON f.entity_id = h.id
    WHERE f.session_id = :sid AND f.entity_type = 'hotel' AND h.status = 'active'
    ORDER BY f.created_at DESC
");
$stmt->execute(['sid' => $sessionId]);
foreach($stmt->fetchAll() as $row) {
    $row['link'] = 'hotels/view.php?id=' . $row['id'];
    $row['type_label'] = __('type_hotel');
    $favorites[] = $row;
}

// Restaurants
$stmt = $pdo->prepare("
    SELECT r.id, r.name as name_uz, NULL as name_ru, NULL as name_en, r.image_path as image, NULL as description_uz, 'restaurant' as type
    FROM favorites f JOIN restaurants r ON f.entity_id = r.id
    WHERE f.session_id = :sid AND f.entity_type = 'restaurant'
    ORDER BY f.created_at DESC
");
$stmt->execute(['sid' => $sessionId]);
foreach($stmt->fetchAll() as $row) {
    $row['link'] = 'restaurants/view.php?id=' . $row['id'];
    $row['type_label'] = __('type_restaurant');
    $favorites[] = $row;
}
?>

<!-- Page Header -->
<section class="pt-28 pb-16 bg-gradient-to-b from-silk-950 to-silk-900">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center">
        <h1 class="font-display text-4xl md:text-5xl font-bold text-white mb-4" data-aos="fade-up"><?= __('nav_favorites') ?></h1>
        <p class="text-white/50 max-w-xl mx-auto text-lg" data-aos="fade-up" data-aos-delay="100"><?= __('no_favorites_subtitle') ?? 'Sizga yoqqan va saqlab qo\'yilgan barcha maskanlar.' ?></p>
    </div>
</section>

<section class="py-12 bg-silk-50 min-h-[50vh]">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <?php if (empty($favorites)): ?>
            <div class="bg-white p-20 rounded-3xl border border-silk-200/60 text-center" data-aos="zoom-in">
                <div class="w-24 h-24 bg-silk-50 rounded-full flex items-center justify-center mx-auto mb-8 border border-silk-100">
                    <span class="material-symbols-outlined text-4xl text-silk-300">favorite</span>
                </div>
                <h3 class="text-2xl font-bold text-silk-900 mb-4"><?= __('no_favorites') ?></h3>
                <p class="text-silk-500 mb-8 max-w-sm mx-auto"><?= __('no_favorites_hint') ?? 'Sayohat paytida o\'zingizga yoqqan joylarni yurakcha tugmasi orqali saqlab qo\'yishingiz mumkin.' ?></p>
                <a href="<?= BASE_URL ?>public/places/index.php" class="bg-gradient-to-r from-amber-500 to-amber-600 text-silk-950 px-8 py-3.5 rounded-xl font-bold shadow-lg shadow-amber-500/25 active:scale-95 transition-all">
                    <?= __('cta_places') ?>
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" x-data="{
                async removeFav(type, id, el) {
                    const r = await fetch('<?= BASE_URL ?>public/api/favorites.php', {
                        method: 'POST', headers: {'Content-Type':'application/json'},
                        body: JSON.stringify({type, id})
                    });
                    const d = await r.json();
                    if (!d.is_favorite) {
                        el.closest('.fav-card').classList.add('opacity-0', 'scale-90');
                        setTimeout(() => {
                            el.closest('.fav-card').remove();
                            window.dispatchEvent(new CustomEvent('favorite-updated'));
                        }, 300);
                    }
                }
            }">
                <?php foreach($favorites as $i => $item): 
                    $name = localizedField($item, 'name');
                    $desc = localizedField($item, 'description');
                ?>
                    <div class="fav-card group bg-white rounded-2xl overflow-hidden border border-silk-200/60 card-hover flex flex-col h-full transition-all duration-300" data-aos="fade-up" data-aos-delay="<?= ($i % 4) * 100 ?>">
                        <div class="h-48 overflow-hidden relative bg-silk-100 shrink-0">
                            <img src="<?= publicImage($item['image']) ?>" alt="<?= htmlspecialchars($name) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute top-3 left-3 bg-white/95 backdrop-blur px-2.5 py-1 rounded-lg text-[10px] font-bold text-amber-700 uppercase tracking-wider shadow-sm">
                                <?= $item['type_label'] ?>
                            </div>
                            <button @click="removeFav('<?= $item['type'] ?>', <?= $item['id'] ?>, $el)" class="absolute top-3 right-3 w-8 h-8 bg-silk-950/50 backdrop-blur text-white rounded-full flex items-center justify-center hover:bg-red-500 transition-colors shadow-lg">
                                <span class="material-symbols-outlined text-sm">close</span>
                            </button>
                        </div>
                        <div class="p-5 flex-grow flex flex-col">
                            <h3 class="font-bold text-silk-900 text-base mb-2 leading-tight"><?= htmlspecialchars($name) ?></h3>
                            <?php if ($desc): ?>
                            <p class="text-silk-400 text-xs line-clamp-2 mb-4 leading-relaxed"><?= htmlspecialchars(mb_substr(strip_tags($desc), 0, 80)) ?></p>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>public/<?= $item['link'] ?>" class="mt-auto inline-flex items-center gap-1.5 text-amber-600 font-bold text-xs hover:text-amber-700 transition-colors">
                                <?= __('see_all') ?>
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/layout_footer.php'; ?>
