    </main>

    <!-- Footer -->
    <footer class="bg-silk-950 text-white/80 pt-20 pb-8">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <!-- Brand -->
                <div class="lg:col-span-1" data-aos="fade-up">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-9 h-9 bg-gradient-to-br from-amber-400 to-amber-600 rounded-lg flex items-center justify-center shadow-lg shadow-amber-500/20">
                            <span class="text-white font-bold text-sm">SRE</span>
                        </div>
                        <span class="text-lg font-bold text-white tracking-tight">Silk Road Explorer</span>
                    </div>
                    <p class="text-white/50 text-sm leading-relaxed mb-6"><?= __('footer_desc') ?></p>
                    <div class="flex gap-3">
                        <a href="#" class="w-9 h-9 bg-white/5 hover:bg-amber-500/20 border border-white/10 rounded-lg flex items-center justify-center transition-colors" aria-label="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-9 h-9 bg-white/5 hover:bg-amber-500/20 border border-white/10 rounded-lg flex items-center justify-center transition-colors" aria-label="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="#" class="w-9 h-9 bg-white/5 hover:bg-amber-500/20 border border-white/10 rounded-lg flex items-center justify-center transition-colors" aria-label="Telegram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Navigation -->
                <div data-aos="fade-up" data-aos-delay="100">
                    <h4 class="text-amber-400 font-semibold text-xs uppercase tracking-[0.2em] mb-5"><?= __('footer_pages') ?></h4>
                    <ul class="space-y-3">
                        <li><a class="text-white/50 hover:text-amber-400 transition-colors text-sm" href="<?= BASE_URL ?>public/places/index.php"><?= __('places_title') ?></a></li>
                        <li><a class="text-white/50 hover:text-amber-400 transition-colors text-sm" href="<?= BASE_URL ?>public/people/index.php"><?= __('people_title') ?></a></li>
                        <li><a class="text-white/50 hover:text-amber-400 transition-colors text-sm" href="<?= BASE_URL ?>public/restaurants/index.php"><?= __('nav_restaurants') ?></a></li>
                        <li><a class="text-white/50 hover:text-amber-400 transition-colors text-sm" href="<?= BASE_URL ?>public/hotels/index.php"><?= __('nav_hotels') ?></a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div data-aos="fade-up" data-aos-delay="200">
                    <h4 class="text-amber-400 font-semibold text-xs uppercase tracking-[0.2em] mb-5"><?= __('footer_support') ?></h4>
                    <ul class="space-y-3">
                        <li><a class="text-white/50 hover:text-amber-400 transition-colors text-sm" href="#"><?= __('footer_help') ?></a></li>
                        <li><a class="text-white/50 hover:text-amber-400 transition-colors text-sm" href="#"><?= __('footer_faq') ?></a></li>
                        <li><a class="text-white/50 hover:text-amber-400 transition-colors text-sm" href="#"><?= __('footer_contact') ?></a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div data-aos="fade-up" data-aos-delay="300">
                    <h4 class="text-amber-400 font-semibold text-xs uppercase tracking-[0.2em] mb-5"><?= __('footer_legal') ?></h4>
                    <ul class="space-y-3">
                        <li><a class="text-white/50 hover:text-amber-400 transition-colors text-sm" href="#"><?= __('footer_privacy') ?></a></li>
                        <li><a class="text-white/50 hover:text-amber-400 transition-colors text-sm" href="#"><?= __('footer_terms') ?></a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom bar -->
            <div class="border-t border-white/5 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-white/30 text-xs">© <?= date('Y') ?> Silk Road Explorer. <?= __('footer_copy') ?></p>
                <p class="text-white/20 text-xs">O'zbekiston Respublikasi</p>
            </div>
        </div>
    </footer>

    <!-- Lightbox JS (for gallery) -->
    <script>
    function openLightbox(src) {
        const overlay = document.createElement('div');
        overlay.className = 'lightbox-overlay';
        overlay.onclick = () => overlay.remove();
        const img = document.createElement('img');
        img.src = src;
        img.onclick = (e) => e.stopPropagation();
        overlay.appendChild(img);
        document.body.appendChild(overlay);
    }
    </script>
</body>
</html>
