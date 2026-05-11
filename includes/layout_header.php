<?php
$current_page = $_SERVER['PHP_SELF'];

function isActiveNav($path) {
    global $current_page;
    return strpos($current_page, $path) !== false;
}

$pageTitle = $pageTitle ?? 'Admin Panel';
?>
<!DOCTYPE html>
<html lang="en" class="bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Turizm Admin</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="flex h-screen overflow-hidden text-gray-800 font-sans antialiased" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-20 md:hidden"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display:none;">
    </div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           class="fixed md:static inset-y-0 left-0 z-30 w-64 bg-slate-900 text-white flex flex-col transition-transform duration-200 ease-in-out shrink-0">
        <div class="h-16 flex items-center px-6 font-bold text-xl tracking-wider border-b border-slate-800 text-blue-400 shrink-0">
            TURIZM<span class="text-white ml-1">ADMIN</span>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1 px-3">
                <li>
                    <a href="<?= BASE_URL ?>admin/index.php" class="flex items-center px-3 py-2.5 rounded-lg transition-colors <?= $current_page == '/admin/index.php' || strpos($current_page, 'admin/index.php') !== false ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>admin/regions/index.php" class="flex items-center px-3 py-2.5 rounded-lg transition-colors <?= isActiveNav('/admin/regions/') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Viloyatlar
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>admin/categories/index.php" class="flex items-center px-3 py-2.5 rounded-lg transition-colors <?= isActiveNav('/admin/categories/') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        Kategoriyalar
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>admin/places/index.php" class="flex items-center px-3 py-2.5 rounded-lg transition-colors <?= isActiveNav('/admin/places/') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Joylar
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>admin/people/index.php" class="flex items-center px-3 py-2.5 rounded-lg transition-colors <?= isActiveNav('/admin/people/') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Tarixiy Shaxslar
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>admin/restaurants/index.php" class="flex items-center px-3 py-2.5 rounded-lg transition-colors <?= isActiveNav('/admin/restaurants/') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"></path></svg>
                        Restoranlar
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>admin/hotels/index.php" class="flex items-center px-3 py-2.5 rounded-lg transition-colors <?= isActiveNav('/admin/hotels/') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Mehmonxonalar
                    </a>
                </li>

                <li class="pt-3 mt-3 border-t border-slate-700">
                    <a href="<?= BASE_URL ?>admin/settings.php" class="flex items-center px-3 py-2.5 rounded-lg transition-colors <?= isActiveNav('/admin/settings.php') ? 'bg-purple-600 text-white shadow-md shadow-purple-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <svg class="w-5 h-5 mr-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        AI Sozlamalari
                    </a>
                </li>

                <li class="pt-3 mt-3 border-t border-slate-700">
                    <a href="<?= BASE_URL ?>admin/bookings.php" class="flex items-center px-3 py-2.5 rounded-lg transition-colors <?= isActiveNav('/admin/bookings') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Bronlar
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>admin/reviews.php" class="flex items-center px-3 py-2.5 rounded-lg transition-colors <?= isActiveNav('/admin/reviews') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                        Sharhlar
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Sidebar footer: logged in user -->
        <div class="border-t border-slate-800 px-4 py-3 shrink-0">
            <p class="text-xs text-slate-500 text-center">Tourism Admin Panel</p>
        </div>
    </aside>

    <!-- Main Content wrapper -->
    <div class="flex-1 flex flex-col min-w-0 bg-gray-50">
        
        <!-- Top bar -->
        <header class="bg-white border-b border-gray-200 h-16 shrink-0 flex items-center justify-between px-4 md:px-6 z-10 relative shadow-sm">
            <div class="flex items-center gap-3">
                <!-- Hamburger for mobile -->
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors" aria-label="Menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
            </div>
            
            <div class="flex items-center space-x-5">
                <a href="<?= BASE_URL ?>" target="_blank" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors flex items-center group">
                    <svg class="w-4 h-4 mr-1.5 text-slate-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    Saytni ko'rish
                </a>
                
                <div class="h-6 w-px bg-gray-200"></div>
                
                <form action="<?= BASE_URL ?>admin/logout.php" method="POST" class="m-0 flex">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrf()) ?>">
                    <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-700 transition-colors flex items-center group">
                        Chiqish
                        <svg class="w-4 h-4 ml-1.5 text-red-400 group-hover:text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8">
            
            <!-- Flash Messages -->
            <?php if (function_exists('getFlash')): ?>
                <?php if ($successMsg = getFlash('success')): ?>
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-md shadow-sm">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700 font-medium"><?= htmlspecialchars($successMsg) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($errorMsg = getFlash('error')): ?>
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md shadow-sm">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700 font-medium"><?= htmlspecialchars($errorMsg) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
