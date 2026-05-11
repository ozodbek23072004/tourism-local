<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once 'includes/functions.php';

$q = trim($_GET['q'] ?? '');

if ($q === '') {
    header("Location: " . BASE_URL . "public/index.php");
    exit;
}

if (mb_strlen($q) > 100) {
    $q = mb_substr($q, 0, 100);
}

$searchTerm  = "%{$q}%";
$useFulltext = mb_strlen($q) >= 3; // FULLTEXT needs min 3 chars by default

// 1. Places
if ($useFulltext) {
    $stmtPlaces = $pdo->prepare("
        SELECT id, name_uz as name, image, 'Joy' as type, 'places/view.php?id=' as link_base
        FROM places WHERE status = 'active'
        AND MATCH(name_uz, name_ru, name_en, description_uz, description_ru, description_en) AGAINST (:q IN BOOLEAN MODE)
        LIMIT 20
    ");
    $stmtPlaces->execute(['q' => $q . '*']);
} else {
    $stmtPlaces = $pdo->prepare("
        SELECT id, name_uz as name, image, 'Joy' as type, 'places/view.php?id=' as link_base
        FROM places WHERE status = 'active' AND (name_uz LIKE :q OR name_ru LIKE :q OR name_en LIKE :q)
        LIMIT 20
    ");
    $stmtPlaces->execute(['q' => $searchTerm]);
}
$places = $stmtPlaces->fetchAll();

// 2. People
if ($useFulltext) {
    $stmtPeople = $pdo->prepare("
        SELECT id, name_uz as name, image, 'Tarixiy shaxs' as type, 'people/view.php?id=' as link_base
        FROM people WHERE status = 'active'
        AND MATCH(name_uz, name_ru, name_en, bio_uz, bio_ru, bio_en) AGAINST (:q IN BOOLEAN MODE)
        LIMIT 20
    ");
    $stmtPeople->execute(['q' => $q . '*']);
} else {
    $stmtPeople = $pdo->prepare("
        SELECT id, name_uz as name, image, 'Tarixiy shaxs' as type, 'people/view.php?id=' as link_base
        FROM people WHERE status = 'active' AND (name_uz LIKE :q OR name_ru LIKE :q OR name_en LIKE :q)
        LIMIT 20
    ");
    $stmtPeople->execute(['q' => $searchTerm]);
}
$people = $stmtPeople->fetchAll();

// 3. Restaurants
if ($useFulltext) {
    $stmtRest = $pdo->prepare("
        SELECT id, name_uz as name, image, 'Restoran' as type, 'restaurants/view.php?id=' as link_base
        FROM restaurants WHERE status = 'active'
        AND MATCH(name_uz, name_ru, name_en, description_uz, description_ru, description_en) AGAINST (:q IN BOOLEAN MODE)
        LIMIT 20
    ");
    $stmtRest->execute(['q' => $q . '*']);
} else {
    $stmtRest = $pdo->prepare("
        SELECT id, name_uz as name, image, 'Restoran' as type, 'restaurants/view.php?id=' as link_base
        FROM restaurants WHERE status = 'active' AND (name_uz LIKE :q OR name_ru LIKE :q OR name_en LIKE :q)
        LIMIT 20
    ");
    $stmtRest->execute(['q' => $searchTerm]);
}
$restaurants = $stmtRest->fetchAll();

// 4. Hotels
if ($useFulltext) {
    $stmtHotels = $pdo->prepare("
        SELECT id, name_uz as name, image, 'Mehmonxona' as type, 'hotels/view.php?id=' as link_base
        FROM hotels WHERE status = 'active'
        AND MATCH(name_uz, name_ru, name_en, description_uz, description_ru, description_en) AGAINST (:q IN BOOLEAN MODE)
        LIMIT 20
    ");
    $stmtHotels->execute(['q' => $q . '*']);
} else {
    $stmtHotels = $pdo->prepare("
        SELECT id, name_uz as name, image, 'Mehmonxona' as type, 'hotels/view.php?id=' as link_base
        FROM hotels WHERE status = 'active' AND (name_uz LIKE :q OR name_ru LIKE :q OR name_en LIKE :q)
        LIMIT 20
    ");
    $stmtHotels->execute(['q' => $searchTerm]);
}
$hotels = $stmtHotels->fetchAll();

$totalResults = count($places) + count($people) + count($restaurants) + count($hotels);

// Helper for highlighting
function highlightSearchTerm($text, $term) {
    if (empty($term)) return htmlspecialchars($text);
    $termSafe = preg_quote($term, '/');
    // Case-insensitive replace with <mark>
    $highlighted = preg_replace("/($termSafe)/iu", "<mark class='bg-yellow-200 text-gray-900 rounded px-1'>$1</mark>", htmlspecialchars($text));
    return $highlighted;
}

$pageTitle = "Qidiruv: " . htmlspecialchars($q);
require_once 'includes/seo.php';
renderMeta(['title' => $pageTitle . ' | Silk Road Explorer']);
require_once 'includes/layout_header.php';
?>

<!-- Page Header -->
<section class="pt-28 pb-16 bg-gradient-to-b from-silk-950 to-silk-900">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center gap-2 text-amber-400/60 text-sm mb-4">
            <a href="<?= BASE_URL ?>public/index.php" class="hover:text-amber-400 transition-colors">Bosh sahifa</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-amber-400">Qidiruv</span>
        </div>
        <h1 class="font-display text-3xl md:text-4xl font-bold text-white mb-3">Qidiruv natijalari</h1>
        <p class="text-white/50 text-lg mb-8">
            "<span class="text-amber-400 font-medium"><?= htmlspecialchars($q) ?></span>" bo'yicha 
            <span class="text-white font-semibold"><?= $totalResults ?></span> ta natija topildi
        </p>
        <form action="search.php" method="GET" class="max-w-2xl flex gap-3">
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-white/30">search</span>
                <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" class="w-full pl-12 pr-4 py-3.5 bg-white/10 backdrop-blur border border-white/15 rounded-xl text-white placeholder-white/30 focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500/50 text-sm" required placeholder="Qidirish...">
            </div>
            <button type="submit" class="bg-gradient-to-r from-amber-500 to-amber-600 text-silk-950 px-6 py-3.5 rounded-xl font-bold text-sm shadow-lg shadow-amber-500/25 active:scale-95">Qidirish</button>
        </form>
    </div>
</section>

<section class="py-12 bg-silk-50 min-h-[50vh]">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <?php if ($totalResults === 0): ?>
            <div class="bg-white rounded-3xl border border-silk-200/60 p-16 text-center">
                <div class="w-20 h-20 bg-silk-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-3xl text-silk-300">search_off</span>
                </div>
                <h2 class="font-display text-2xl font-bold text-silk-900 mb-4">Hech narsa topilmadi</h2>
                <p class="text-silk-500 mb-8 max-w-md mx-auto">Qidiruv so'zini o'zgartirib ko'ring yoki bo'limlardan birini tanlang.</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="places/index.php" class="bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold py-2.5 px-6 rounded-xl transition-colors border border-amber-200 text-sm">Joylar</a>
                    <a href="people/index.php" class="bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold py-2.5 px-6 rounded-xl transition-colors border border-amber-200 text-sm">Shaxslar</a>
                    <a href="restaurants/index.php" class="bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold py-2.5 px-6 rounded-xl transition-colors border border-amber-200 text-sm">Restoranlar</a>
                </div>
            </div>
        <?php else: ?>
            <div class="space-y-14">
                
                <?php 
                $sections = [
                    ['data' => $places, 'title' => 'Joylar', 'icon' => 'location_on'],
                    ['data' => $people, 'title' => 'Tarixiy shaxslar', 'icon' => 'person'],
                    ['data' => $restaurants, 'title' => 'Restoranlar', 'icon' => 'restaurant'],
                    ['data' => $hotels, 'title' => 'Mehmonxonalar', 'icon' => 'hotel'],
                ];
                foreach($sections as $section):
                    if (count($section['data']) === 0) continue;
                ?>
                <section>
                    <h2 class="text-lg font-bold text-silk-900 mb-6 flex items-center gap-3">
                        <span class="w-8 h-8 bg-gradient-to-br from-amber-400 to-amber-600 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-base"><?= $section['icon'] ?></span>
                        </span>
                        <?= $section['title'] ?>
                        <span class="bg-amber-100 text-amber-700 text-xs py-1 px-3 rounded-full font-bold"><?= count($section['data']) ?></span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <?php foreach($section['data'] as $item): ?>
                            <a href="<?= BASE_URL ?>public/<?= $item['link_base'] ?><?= $item['id'] ?>" class="flex bg-white rounded-xl border border-silk-200/60 overflow-hidden hover:border-amber-300 transition-all group card-hover">
                                <div class="w-24 h-24 bg-silk-100 shrink-0">
                                    <img src="<?= publicImage($item['image'], $item['name']) ?>" alt="" class="w-full h-full object-cover">
                                </div>
                                <div class="p-4 flex flex-col justify-center min-w-0">
                                    <span class="text-xs font-bold text-amber-600 mb-1"><?= $item['type'] ?></span>
                                    <h3 class="font-semibold text-silk-900 text-sm group-hover:text-amber-700 transition-colors line-clamp-2 leading-snug">
                                        <?= highlightSearchTerm($item['name'], $q) ?>
                                    </h3>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endforeach; ?>

            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/layout_footer.php'; ?>

