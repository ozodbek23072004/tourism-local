<?php
if (!defined('BASE_URL')) {
    require_once dirname(__DIR__, 2) . '/includes/config.php';
}
require_once __DIR__ . '/lang.php';

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

function isPublicActive(string $path, string $current): string {
    return (strpos($current, $path) !== false) 
        ? 'text-amber-500 font-semibold' 
        : 'text-gray-300 hover:text-white transition-colors duration-200';
}

$pageTitle = $pageTitle ?? "Explorer — O'zbekiston sayohat portali";
$lang = currentLang();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" class="scroll-smooth" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <?= $seoMetaHtml ?? '<title>' . htmlspecialchars($pageTitle) . '</title>' ?>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries,typography"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- AOS Animations -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css"/>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        gold: { 50:'#fefce8', 100:'#fef9c3', 200:'#fef08a', 300:'#fde047', 400:'#facc15', 500:'#eab308', 600:'#ca8a04', 700:'#a16207', 800:'#854d0e', 900:'#713f12' },
                        silk: { 50:'#f8f6f3', 100:'#ede8df', 200:'#ddd4c5', 300:'#c9b8a1', 400:'#b69c7d', 500:'#a38565', 600:'#8f6e50', 700:'#755842', 800:'#5e4737', 900:'#4e3c30', 950:'#2a1f18' }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        display: ['Playfair Display', 'Georgia', 'serif']
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .text-gradient { background: linear-gradient(135deg, #f59e0b, #d97706); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.08); }
        .card-hover { transition: all 0.4s cubic-bezier(0.4,0,0.2,1); }
        .card-hover:hover { transform: translateY(-6px); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15); }
        @keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .fade-up { animation: fadeUp 0.6s ease-out forwards; }
        .line-clamp-2 { overflow:hidden; display:-webkit-box; -webkit-box-orient:vertical; -webkit-line-clamp:2; }
        .line-clamp-3 { overflow:hidden; display:-webkit-box; -webkit-box-orient:vertical; -webkit-line-clamp:3; }
        
        /* Dark mode styles */
        .dark body { background-color: #1a1510; color: #e8e0d5; }
        .dark .bg-white { background-color: #2a2118 !important; }
        .dark .bg-silk-50 { background-color: #1e1812 !important; }
        .dark .text-silk-900 { color: #ede8df !important; }
        .dark .text-silk-800 { color: #ddd4c5 !important; }
        .dark .text-silk-700 { color: #c9b8a1 !important; }
        .dark .text-silk-600 { color: #b69c7d !important; }
        .dark .text-silk-500 { color: #a38565 !important; }
        .dark .text-silk-400 { color: #b69c7d !important; }
        .dark .text-gray-800, .dark .text-gray-900 { color: #ede8df !important; }
        .dark .border-silk-200\/60, .dark .border-silk-200 { border-color: rgba(93,71,55,0.4) !important; }
        .dark .border-silk-100 { border-color: rgba(93,71,55,0.3) !important; }
        .dark .bg-silk-100 { background-color: #2a2118 !important; }
        .dark .bg-amber-50 { background-color: rgba(245,158,11,0.08) !important; }
        .dark .bg-amber-100 { background-color: rgba(245,158,11,0.12) !important; }
        .dark .from-amber-50 { --tw-gradient-from: rgba(245,158,11,0.06) !important; }
        .dark .from-silk-50 { --tw-gradient-from: #1e1812 !important; }
        .dark .to-silk-50 { --tw-gradient-to: #1e1812 !important; }
        .dark .bg-gradient-to-br.from-silk-50 { background: linear-gradient(to bottom right, #1e1812, #2a2118) !important; }
        .dark .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4) !important; }
        
        /* Lightbox styles */
        .lightbox-overlay {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,0.92); backdrop-filter: blur(12px);
            display: flex; align-items: center; justify-content: center;
            cursor: zoom-out;
        }
        .lightbox-overlay img {
            max-width: 90vw; max-height: 85vh; object-fit: contain;
            border-radius: 12px; cursor: default;
        }

        /* Star rating input */
        .star-rating { display: flex; flex-direction: row-reverse; gap: 2px; }
        .star-rating input { display: none; }
        .star-rating label { cursor: pointer; font-size: 24px; color: #ddd4c5; transition: color 0.15s; }
        .star-rating label:hover, .star-rating label:hover ~ label,
        .star-rating input:checked ~ label { color: #f59e0b; }
    </style>
</head>
<body class="bg-silk-50 text-silk-900 min-h-screen flex flex-col font-sans antialiased">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 transition-all duration-300" 
         x-data="{ mobileOpen: false, scrolled: false, langOpen: false }"
         x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
         :class="scrolled ? 'bg-silk-950/95 backdrop-blur-xl shadow-2xl shadow-black/10' : 'bg-transparent'">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="<?= BASE_URL ?>public/index.php" class="flex items-center gap-2 group">
                    <div class="w-9 h-9 bg-gradient-to-br from-amber-400 to-amber-600 rounded-lg flex items-center justify-center shadow-lg shadow-amber-500/25 group-hover:shadow-amber-500/40 transition-shadow">
                        <span class="text-white font-bold text-sm">SRE</span>
                    </div>
                    <span class="text-xl font-bold text-white tracking-tight">Silk Road <span class="text-gradient">Explorer</span></span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden lg:flex items-center gap-1">
                    <a class="px-4 py-2 rounded-lg text-sm font-medium <?= isPublicActive('places/index.php', $currentPath) ?>" href="<?= BASE_URL ?>public/places/index.php"><?= __('nav_places') ?></a>
                    <a class="px-4 py-2 rounded-lg text-sm font-medium <?= isPublicActive('people/index.php', $currentPath) ?>" href="<?= BASE_URL ?>public/people/index.php"><?= __('nav_people') ?></a>
                    <a class="px-4 py-2 rounded-lg text-sm font-medium <?= isPublicActive('restaurants/index.php', $currentPath) ?>" href="<?= BASE_URL ?>public/restaurants/index.php"><?= __('nav_restaurants') ?></a>
                    <a class="px-4 py-2 rounded-lg text-sm font-medium <?= isPublicActive('hotels/index.php', $currentPath) ?>" href="<?= BASE_URL ?>public/hotels/index.php"><?= __('nav_hotels') ?></a>
                    <a class="px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-1.5 <?= isPublicActive('map.php', $currentPath) ?>" href="<?= BASE_URL ?>public/map.php">
                        <span class="material-symbols-outlined text-base">map</span>
                        <?= __('nav_map') ?>
                    </a>
                    <a class="px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-1.5 <?= isPublicActive('guide.php', $currentPath) ?> hover:text-amber-400 transition-colors" href="<?= BASE_URL ?>public/guide.php">
                        <span class="material-symbols-outlined text-base text-amber-500">auto_awesome</span>
                        AI Sayohatchi
                    </a>
                    
                    <div class="w-px h-6 bg-white/20 mx-2"></div>

                    <!-- Favorites -->
                    <a href="<?= BASE_URL ?>public/favorites.php" 
                       class="relative flex items-center justify-center w-10 h-10 rounded-full hover:bg-white/10 text-white transition-all group"
                       x-data="{ count: 0 }"
                       x-init="fetch('<?= BASE_URL ?>public/api/favorites.php').then(r=>r.json()).then(d=>count=d.count)"
                       @favorite-updated.window="fetch('<?= BASE_URL ?>public/api/favorites.php').then(r=>r.json()).then(d=>count=d.count)">
                        <span class="material-symbols-outlined group-hover:scale-110 transition-transform">favorite</span>
                        <template x-if="count > 0">
                            <span class="absolute -top-1 -right-1 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-amber-500 px-1 text-[10px] font-bold text-silk-950 shadow-lg" x-text="count"></span>
                        </template>
                    </a>
                    
                    <!-- Language Switcher -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm text-gray-300 hover:text-white hover:bg-white/10 transition-all">
                            <span class="material-symbols-outlined text-base">translate</span>
                            <span class="uppercase font-semibold text-xs"><?= $lang ?></span>
                            <svg class="w-3 h-3 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-36 bg-silk-950/95 backdrop-blur-xl rounded-xl border border-white/10 shadow-2xl overflow-hidden" style="display:none">
                            <a href="<?= langSwitchUrl('uz') ?>" class="flex items-center gap-2 px-4 py-2.5 text-sm <?= $lang === 'uz' ? 'text-amber-400 bg-amber-500/10' : 'text-white/70 hover:text-white hover:bg-white/5' ?> transition-colors">
                                <span class="text-base">🇺🇿</span> O'zbekcha
                            </a>
                            <a href="<?= langSwitchUrl('ru') ?>" class="flex items-center gap-2 px-4 py-2.5 text-sm <?= $lang === 'ru' ? 'text-amber-400 bg-amber-500/10' : 'text-white/70 hover:text-white hover:bg-white/5' ?> transition-colors">
                                <span class="text-base">🇷🇺</span> Русский
                            </a>
                            <a href="<?= langSwitchUrl('en') ?>" class="flex items-center gap-2 px-4 py-2.5 text-sm <?= $lang === 'en' ? 'text-amber-400 bg-amber-500/10' : 'text-white/70 hover:text-white hover:bg-white/5' ?> transition-colors">
                                <span class="text-base">🇬🇧</span> English
                            </a>
                        </div>
                    </div>

                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" 
                            class="p-2 rounded-lg text-gray-300 hover:text-white hover:bg-white/10 transition-all" 
                            :title="darkMode ? '<?= __('light_mode') ?>' : '<?= __('dark_mode') ?>'">
                        <span x-show="!darkMode" class="material-symbols-outlined text-lg">dark_mode</span>
                        <span x-show="darkMode" class="material-symbols-outlined text-lg text-amber-400" style="display:none">light_mode</span>
                    </button>

                    <a href="<?= BASE_URL ?>admin/login.php" class="bg-gradient-to-r from-amber-500 to-amber-600 text-silk-950 px-5 py-2.5 rounded-xl font-semibold text-sm hover:from-amber-400 hover:to-amber-500 transition-all shadow-lg shadow-amber-500/25 active:scale-95 ml-1">
                        Admin
                    </a>
                </div>

                <!-- Mobile Hamburger -->
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition-all" aria-label="Menyu">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileOpen" x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4" class="lg:hidden bg-silk-950/98 backdrop-blur-2xl border-t border-white/5" style="display:none">
            <div class="px-6 py-6 space-y-2">
                <a @click="mobileOpen = false" class="block px-4 py-3 text-white/80 hover:text-white hover:bg-white/5 rounded-xl font-medium transition-all" href="<?= BASE_URL ?>public/places/index.php"><?= __('nav_places') ?></a>
                <a @click="mobileOpen = false" class="block px-4 py-3 text-white/80 hover:text-white hover:bg-white/5 rounded-xl font-medium transition-all" href="<?= BASE_URL ?>public/people/index.php"><?= __('nav_people') ?></a>
                <a @click="mobileOpen = false" class="block px-4 py-3 text-white/80 hover:text-white hover:bg-white/5 rounded-xl font-medium transition-all" href="<?= BASE_URL ?>public/restaurants/index.php"><?= __('nav_restaurants') ?></a>
                <a @click="mobileOpen = false" class="block px-4 py-3 text-white/80 hover:text-white hover:bg-white/5 rounded-xl font-medium transition-all" href="<?= BASE_URL ?>public/hotels/index.php"><?= __('nav_hotels') ?></a>
                <a @click="mobileOpen = false" class="flex items-center gap-2 px-4 py-3 text-white/80 hover:text-white hover:bg-white/5 rounded-xl font-medium transition-all" href="<?= BASE_URL ?>public/map.php">
                    <span class="material-symbols-outlined text-base">map</span>
                    <?= __('nav_map') ?>
                </a>
                <a @click="mobileOpen = false" class="flex items-center gap-2 px-4 py-3 text-amber-400 hover:bg-white/5 rounded-xl font-bold transition-all" href="<?= BASE_URL ?>public/guide.php">
                    <span class="material-symbols-outlined text-base">auto_awesome</span>
                    AI Sayohatchi
                </a>
                <a @click="mobileOpen = false" class="flex items-center gap-2 px-4 py-3 text-amber-400 hover:bg-white/5 rounded-xl font-semibold transition-all" href="<?= BASE_URL ?>public/favorites.php">
                    <span class="material-symbols-outlined text-base">favorite</span>
                    <?= __('nav_favorites') ?>
                </a>
                
                <!-- Mobile Language & Dark Mode -->
                <div class="pt-3 border-t border-white/10 flex items-center gap-3">
                    <a href="<?= langSwitchUrl('uz') ?>" class="flex-1 text-center py-2.5 rounded-lg text-sm font-semibold <?= $lang === 'uz' ? 'bg-amber-500/20 text-amber-400' : 'bg-white/5 text-white/60' ?> transition-all">UZ</a>
                    <a href="<?= langSwitchUrl('ru') ?>" class="flex-1 text-center py-2.5 rounded-lg text-sm font-semibold <?= $lang === 'ru' ? 'bg-amber-500/20 text-amber-400' : 'bg-white/5 text-white/60' ?> transition-all">RU</a>
                    <a href="<?= langSwitchUrl('en') ?>" class="flex-1 text-center py-2.5 rounded-lg text-sm font-semibold <?= $lang === 'en' ? 'bg-amber-500/20 text-amber-400' : 'bg-white/5 text-white/60' ?> transition-all">EN</a>
                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="p-2.5 rounded-lg bg-white/5 text-white/60">
                        <span x-show="!darkMode" class="material-symbols-outlined text-lg">dark_mode</span>
                        <span x-show="darkMode" class="material-symbols-outlined text-lg text-amber-400" style="display:none">light_mode</span>
                    </button>
                </div>

                <div class="pt-2">
                    <a href="<?= BASE_URL ?>admin/login.php" class="block w-full text-center bg-gradient-to-r from-amber-500 to-amber-600 text-silk-950 px-6 py-3 rounded-xl font-bold text-sm shadow-lg">Admin Panel</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow">

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof AOS !== 'undefined') {
                AOS.init({ duration: 600, easing: 'ease-out-cubic', once: true, offset: 50 });
            }
        });
    </script>
