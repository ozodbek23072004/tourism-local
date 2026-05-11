<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Himoyalangan sahifa
requireAuth();

// Har bir bo'lim bo'yicha ma'lumotlar sonini olish
$stmtPlaces = $pdo->prepare("SELECT COUNT(*) FROM places"); $stmtPlaces->execute();
$placesCount = $stmtPlaces->fetchColumn();

$stmtPeople = $pdo->prepare("SELECT COUNT(*) FROM people"); $stmtPeople->execute();
$peopleCount = $stmtPeople->fetchColumn();

$stmtRest = $pdo->prepare("SELECT COUNT(*) FROM restaurants"); $stmtRest->execute();
$restaurantsCount = $stmtRest->fetchColumn();

$stmtHotels = $pdo->prepare("SELECT COUNT(*) FROM hotels"); $stmtHotels->execute();
$hotelsCount = $stmtHotels->fetchColumn();

$stmtRev = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE status = 'pending'"); $stmtRev->execute();
$pendingReviews = $stmtRev->fetchColumn();

$stmtBook = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE status = 'pending'"); $stmtBook->execute();
$pendingBookings = $stmtBook->fetchColumn();

// Oxirgi qo'shilgan 5 ta joyni olish
$latestPlacesStmt = $pdo->prepare("SELECT name_uz, status, created_at FROM places ORDER BY id DESC LIMIT 5");
$latestPlacesStmt->execute();
$latestPlaces = $latestPlacesStmt->fetchAll();

// Sahifa sarlavhasi
$pageTitle = 'Dashboard';
require_once '../includes/layout_header.php';
?>

<!-- Statistik kartalar (Grid layout) -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-6 mb-8">
    <!-- Places Card -->
    <a href="places/index.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
        <div class="p-3.5 rounded-xl bg-blue-50 text-blue-600 mr-5">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Joylar</p>
            <h3 class="text-3xl font-bold text-gray-800"><?= number_format((float)$placesCount) ?></h3>
        </div>
    </a>
    
    <!-- People Card -->
    <a href="people/index.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
        <div class="p-3.5 rounded-xl bg-green-50 text-green-600 mr-5">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Shaxslar</p>
            <h3 class="text-3xl font-bold text-gray-800"><?= number_format((float)$peopleCount) ?></h3>
        </div>
    </a>
    
    <!-- Restaurants Card -->
    <a href="restaurants/index.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
        <div class="p-3.5 rounded-xl bg-yellow-50 text-yellow-600 mr-5">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"></path></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Restoranlar</p>
            <h3 class="text-3xl font-bold text-gray-800"><?= number_format((float)$restaurantsCount) ?></h3>
        </div>
    </a>
    
    <!-- Hotels Card -->
    <a href="hotels/index.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
        <div class="p-3.5 rounded-xl bg-purple-50 text-purple-600 mr-5">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Mehmonxonalar</p>
            <h3 class="text-3xl font-bold text-gray-800"><?= number_format((float)$hotelsCount) ?></h3>
        </div>
    </a>

    <!-- Reviews Card -->
    <a href="reviews.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
        <div class="p-3.5 rounded-xl bg-rose-50 text-rose-600 mr-5 relative">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
            <?php if ($pendingReviews > 0): ?>
                <span class="absolute top-0 right-0 h-4 w-4 bg-rose-500 rounded-full border-2 border-white flex items-center justify-center text-[10px] text-white font-bold animate-bounce"><?= $pendingReviews ?></span>
            <?php endif; ?>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Sharhlar</p>
            <h3 class="text-3xl font-bold text-gray-800"><?= number_format((float)$pendingReviews) ?></h3>
        </div>
    </a>

    <!-- Bookings Card -->
    <a href="bookings.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
        <div class="p-3.5 rounded-xl bg-amber-50 text-amber-600 mr-5 relative">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <?php if ($pendingBookings > 0): ?>
                <span class="absolute top-0 right-0 h-4 w-4 bg-amber-500 rounded-full border-2 border-white flex items-center justify-center text-[10px] text-white font-bold animate-bounce"><?= $pendingBookings ?></span>
            <?php endif; ?>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Bronlar</p>
            <h3 class="text-3xl font-bold text-gray-800"><?= number_format((float)$pendingBookings) ?></h3>
        </div>
    </a>

    <!-- Hero Sliders Card -->
    <?php
    $stmtSliders = $pdo->prepare("SELECT COUNT(*) FROM hero_sliders"); $stmtSliders->execute();
    $slidersCount = $stmtSliders->fetchColumn();
    ?>
    <a href="hero_sliders.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
        <div class="p-3.5 rounded-xl bg-orange-50 text-orange-600 mr-5 relative">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Sliderlar</p>
            <h3 class="text-3xl font-bold text-gray-800"><?= number_format((float)$slidersCount) ?></h3>
        </div>
    </a>
</div>

<!-- Oxirgi qo'shilgan joylar jadvali -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 bg-white">
        <h2 class="text-lg font-semibold text-gray-800">Oxirgi qo'shilgan joylar</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="px-6 py-3 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomi (UZ)</th>
                    <th class="px-6 py-3 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">Holati</th>
                    <th class="px-6 py-3 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Qo'shilgan sana</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (count($latestPlaces) > 0): ?>
                    <?php foreach ($latestPlaces as $place): ?>
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-800 font-medium">
                            <?= htmlspecialchars($place['name_uz']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <?php if ($place['status'] === 'active'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                    Faol
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>
                                    Qoralama
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 text-right">
                            <?= date('d.m.Y H:i', strtotime($place['created_at'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-sm text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Hozircha hech qanday joy qo'shilmagan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/layout_footer.php'; ?>
