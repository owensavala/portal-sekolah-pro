<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$config = get_config();
$categories = get_categories(true);
$shortcuts = get_shortcuts(true);

// View mode: 'simple' (Clean Grid Tabs) or 'launcher' (3D Category Cards)
$viewMode = $_GET['mode'] ?? ($config['view_mode'] ?? 'launcher');

// Group shortcuts by category
$shortcutsByCategory = [];
$pinnedShortcuts = [];
foreach ($shortcuts as $sc) {
    $catId = $sc['category_id'] ?? 'general';
    $shortcutsByCategory[$catId][] = $sc;
    if (!empty($sc['is_pinned'])) {
        $pinnedShortcuts[] = $sc;
    }
}

// Category map
$categoryMap = [];
foreach ($categories as $cat) {
    $categoryMap[$cat['id']] = $cat;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($config['site_title'] ?? 'Portal Sekolah Terpadu') ?></title>
    <meta name="description" content="<?= e($config['tagline'] ?? '') ?> - <?= e($config['school_name'] ?? '') ?>">
    <link rel="shortcut icon" href="<?= e(asset_url($config['favicon'] ?? 'uploads/favicon.ico')) ?>" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        <?= generate_theme_variables($config) ?>
        body {
            <?= generate_background_css($config) ?>
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="selection:bg-cyan-400 selection:text-slate-900 min-h-screen flex flex-col justify-between relative overflow-x-hidden">

    <!-- Ambient Glowing Orbs with Dynamic Theme Colors -->
    <div class="ambient-orb w-[420px] h-[420px] top-[-50px] left-1/4 opacity-30" style="background: radial-gradient(circle, var(--secondary) 0%, transparent 70%);"></div>
    <div class="ambient-orb w-[480px] h-[480px] bottom-[-80px] right-1/4 opacity-25" style="background: radial-gradient(circle, var(--accent) 0%, transparent 70%); animation-delay: -5s;"></div>

    <!-- Top Navigation Bar -->
    <header class="w-full border-b border-white/10 bg-black/40 backdrop-blur-xl sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Left: School Badge & Date -->
            <div class="flex items-center space-x-2.5 text-xs font-medium">
                <span class="px-3.5 py-1 rounded-full font-extrabold uppercase tracking-wider text-[11px] flex items-center gap-1.5 shadow"
                      style="color: var(--secondary); background: rgba(var(--secondary-rgb), 0.18); border: 1px solid rgba(var(--secondary-rgb), 0.35);">
                    <i class="fa-solid fa-graduation-cap text-xs"></i>
                    <span><?= e($config['school_type_badge'] ?? 'Portal Sekolah') ?></span>
                </span>
                <span class="hidden sm:inline-flex items-center gap-1.5 text-slate-300 bg-white/5 px-3 py-1 rounded-full border border-white/10">
                    <i class="fa-regular fa-calendar-days" style="color: var(--secondary);"></i>
                    <span><?= date('l, d F Y') ?></span>
                </span>
                <span class="hidden md:inline-flex items-center gap-1.5 px-3 py-1 rounded-full border"
                      style="background: rgba(var(--primary-rgb), 0.2); border-color: rgba(var(--primary-rgb), 0.4); color: var(--accent);">
                    <i class="fa-regular fa-clock"></i>
                    <span id="realtime-clock">--:--:-- WIT</span>
                </span>
            </div>

            <!-- Right: Layout Switcher & Admin Button -->
            <div class="flex items-center space-x-2.5">
                <!-- Toggle View Mode Button -->
                <div class="inline-flex rounded-xl bg-white/10 p-1 border border-white/15">
                    <a href="?mode=simple" title="Tampilan Simpel & Bersih" 
                       class="px-3 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1.5 <?= ($viewMode === 'simple') ? 'shadow text-slate-950 font-black' : 'text-slate-300 hover:text-white' ?>"
                       style="<?= ($viewMode === 'simple') ? 'background: var(--secondary);' : '' ?>">
                        <i class="fa-solid fa-table-cells-large text-[11px]"></i>
                        <span class="hidden sm:inline">Simpel</span>
                    </a>
                    <a href="?mode=launcher" title="Tampilan Kartu 3D Kategori" 
                       class="px-3 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1.5 <?= ($viewMode === 'launcher') ? 'shadow text-slate-950 font-black' : 'text-slate-300 hover:text-white' ?>"
                       style="<?= ($viewMode === 'launcher') ? 'background: var(--secondary);' : '' ?>">
                        <i class="fa-solid fa-layer-group text-[11px]"></i>
                        <span class="hidden sm:inline">Kartu 3D</span>
                    </a>
                </div>

                <!-- Admin Link -->
                <a href="admin/login.php" class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-xs font-bold transition text-white shadow hover:scale-105">
                    <i class="fa-solid fa-lock text-[11px]" style="color: var(--secondary);"></i>
                    <span>Admin</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 w-full flex flex-col items-center">

        <!-- Clean School Identity Header -->
        <div class="text-center max-w-3xl mx-auto mb-8 sm:mb-10">
            <!-- Logo -->
            <div class="hero-logo-box w-20 h-20 sm:w-24 sm:h-24 mx-auto rounded-3xl p-2.5 bg-white/10 backdrop-blur-xl border border-white/25 shadow-2xl flex items-center justify-center mb-4 float-icon cursor-pointer">
                <img src="<?= e(asset_url($config['logo'] ?? 'uploads/Logo_Instansi.png')) ?>" alt="Logo" class="max-h-full max-w-full object-contain filter drop-shadow">
            </div>

            <!-- School Name -->
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight text-white drop-shadow mb-2">
                <?= e($config['school_name'] ?? 'PORTAL SEKOLAH TERPADU') ?>
            </h1>
            
            <!-- Location -->
            <p class="text-xs sm:text-sm font-semibold mb-3.5 flex items-center justify-center gap-1.5 text-slate-200">
                <i class="fa-solid fa-location-dot text-xs" style="color: var(--secondary);"></i>
                <span><?= e($config['school_location'] ?? 'Indonesia') ?></span>
            </p>

            <!-- Slogan Pill with Dynamic Border -->
            <div class="inline-block px-5 py-2 rounded-full backdrop-blur-xl text-xs sm:text-sm font-bold shadow-lg"
                 style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(var(--secondary-rgb), 0.4); color: #ffffff;">
                <span style="color: var(--secondary);" class="mr-1">✦</span>
                <?= e($config['tagline'] ?? 'Mewujudkan Generasi Cerdas & Berkarakter') ?>
                <span style="color: var(--secondary);" class="ml-1">✦</span>
            </div>
        </div>

        <!-- Global Search Bar -->
        <?php if (!empty($config['show_search'])): ?>
        <div class="w-full max-w-2xl mx-auto mb-8 relative z-30">
            <div class="relative flex items-center">
                <i class="fa-solid fa-magnifying-glass absolute left-4 text-base pointer-events-none" style="color: var(--secondary);"></i>
                <input type="text" id="simple-search-input" 
                       placeholder="Cari tautan aplikasi (contoh: EMIS, Raport, CBT, Dapodik, PMM...)" 
                       class="w-full pl-11 pr-20 py-3.5 rounded-2xl bg-white/15 backdrop-blur-xl border border-white/25 text-white placeholder-slate-300 focus:outline-none focus:ring-2 text-sm shadow-2xl transition"
                       style="focus:ring-color: var(--secondary);">
                <div class="absolute right-3 flex items-center">
                    <kbd class="hidden sm:inline-block px-2 py-0.5 bg-white/20 border border-white/25 rounded text-[10px] font-mono text-slate-200">
                        Ctrl+K
                    </kbd>
                </div>
            </div>
            <!-- Live Search Autocomplete (Floating) -->
            <div id="search-dropdown-results" class="hidden absolute left-0 right-0 mt-2 bg-slate-900/95 backdrop-blur-2xl border border-white/20 rounded-2xl shadow-2xl overflow-hidden z-50 modal-enter"></div>
        </div>
        <?php endif; ?>

        <!-- Quick Access (Pinned Links) -->
        <?php if (!empty($config['show_quick_access']) && count($pinnedShortcuts) > 0): ?>
        <div class="w-full max-w-4xl mx-auto mb-8">
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none justify-center flex-wrap">
                <span class="text-xs font-extrabold flex items-center gap-1 mr-1" style="color: var(--secondary);">
                    <i class="fa-solid fa-bolt"></i> Favorit:
                </span>
                <?php foreach ($pinnedShortcuts as $sc): ?>
                <a href="<?= e($sc['url']) ?>" target="<?= e($sc['target'] ?? '_blank') ?>"
                   data-track-id="<?= e($sc['id']) ?>"
                   class="flex items-center space-x-2 px-3.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-xs font-semibold text-white transition shadow group"
                   style="hover:border-color: var(--secondary);">
                    <i class="<?= e($sc['fa_icon'] ?? 'fa-solid fa-link') ?> transition group-hover:scale-110" style="color: var(--secondary);"></i>
                    <span><?= e($sc['title']) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($viewMode === 'simple'): ?>
        <!-- ======================================================== -->
        <!-- MODE 1: TAMPILAN SIMPEL & BERSIH (CLEAN GRID TABS)      -->
        <!-- ======================================================== -->

        <!-- Category Filter Pills -->
        <div class="w-full max-w-5xl mx-auto mb-6">
            <div class="flex items-center gap-2 overflow-x-auto pb-2 justify-center flex-wrap" id="category-filter-bar">
                <button type="button" data-filter="all" 
                        class="filter-tab active px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-xs font-bold text-white transition shadow flex items-center gap-1.5">
                    <i class="fa-solid fa-layer-group text-[11px]"></i>
                    <span>Semua Link (<?= count($shortcuts) ?>)</span>
                </button>

                <?php foreach ($categories as $cat): 
                    $catCount = count($shortcutsByCategory[$cat['id']] ?? []);
                ?>
                <button type="button" data-filter="<?= e($cat['id']) ?>" 
                        class="filter-tab px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-xs font-bold text-white transition shadow flex items-center gap-1.5">
                    <?php if (($cat['icon_type'] ?? '') === 'image' && !empty($cat['icon_image'])): ?>
                        <img src="<?= e($cat['icon_image']) ?>" class="w-3.5 h-3.5 object-contain" alt="Icon">
                    <?php else: ?>
                        <i class="fa-solid <?= e($cat['fa_icon'] ?? 'fa-folder') ?> text-[11px]" style="color: var(--secondary);"></i>
                    <?php endif; ?>
                    <span><?= e($cat['name']) ?> (<?= $catCount ?>)</span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Clean Shortcuts Cards Grid -->
        <div class="w-full max-w-5xl mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="simple-shortcuts-grid">
                <?php foreach ($shortcuts as $scIdx => $sc): 
                    $catName = $categoryMap[$sc['category_id'] ?? '']['name'] ?? 'Umum';
                ?>
                <div class="shortcut-card-tile card-animate-in group p-4 rounded-2xl flex flex-col justify-between"
                     style="animation-delay: <?= $scIdx * 0.05 ?>s;"
                     data-cat="<?= e($sc['category_id'] ?? '') ?>"
                     data-title="<?= strtolower(e($sc['title'])) ?>"
                     data-desc="<?= strtolower(e($sc['description'] ?? '')) ?>"
                     data-badge="<?= strtolower(e($sc['badge'] ?? '')) ?>">
                    
                    <div>
                        <!-- Top: Icon & Badge -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-base transition border"
                                 style="background: rgba(var(--secondary-rgb), 0.15); border-color: rgba(var(--secondary-rgb), 0.35); color: var(--secondary);">
                                <i class="<?= e($sc['fa_icon'] ?? 'fa-solid fa-link') ?>"></i>
                            </div>
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full"
                                  style="background: rgba(var(--secondary-rgb), 0.2); color: var(--secondary); border: 1px solid rgba(var(--secondary-rgb), 0.35);">
                                <?= e($sc['badge'] ?: $catName) ?>
                            </span>
                        </div>

                        <!-- Title & Description -->
                        <h3 class="font-extrabold text-sm text-white transition line-clamp-1 mb-1 group-hover:text-[var(--secondary)]">
                            <?= e($sc['title']) ?>
                        </h3>
                        <p class="text-xs text-slate-300 line-clamp-2 leading-relaxed mb-4">
                            <?= e($sc['description'] ?: 'Akses layanan sistem aplikasi resmi.') ?>
                        </p>
                    </div>

                    <!-- Direct Access Button -->
                    <div class="pt-2.5 border-t border-white/10 flex items-center justify-between">
                        <span class="text-[10px] text-slate-400 font-mono"><?= e($catName) ?></span>
                        <a href="<?= e($sc['url']) ?>" target="<?= e($sc['target'] ?? '_blank') ?>"
                           data-track-id="<?= e($sc['id']) ?>"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition shadow"
                           style="background: rgba(255, 255, 255, 0.1); color: var(--secondary);">
                            <span>Buka</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Empty Search State -->
            <div id="no-results-message" class="hidden text-center py-12 text-slate-400">
                <i class="fa-solid fa-magnifying-glass text-3xl mb-2 text-slate-500"></i>
                <p class="font-bold text-white">Tidak ada tautan yang cocok.</p>
                <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
            </div>
        </div>

        <?php else: ?>
        <!-- ======================================================== -->
        <!-- MODE 2: TAMPILAN KARTU 3D LAUNCHER (CENTERED & DYNAMIC)  -->
        <!-- ======================================================== -->
        <div class="w-full max-w-6xl mx-auto">
            <!-- Flex Wrap Justify Center ensures 1, 2, 3, 4, 5+ cards are ALWAYS centered with zero awkward holes! -->
            <div class="flex flex-wrap justify-center items-stretch gap-5 sm:gap-6 w-full">
                <?php foreach ($categories as $catIdx => $cat): 
                    $catShortcuts = $shortcutsByCategory[$cat['id']] ?? [];
                    $count = count($catShortcuts);
                ?>
                <button type="button" 
                        data-category-id="<?= e($cat['id']) ?>"
                        style="animation-delay: <?= $catIdx * 0.08 ?>s;"
                        class="category-card-btn glass-card-dynamic card-animate-in group rounded-3xl p-5 sm:p-6 text-center flex flex-col items-center justify-between cursor-pointer border border-white/20 transition-all duration-300 relative focus:outline-none min-h-[220px] sm:min-h-[250px] w-full sm:w-[calc(50%-1.25rem)] md:w-[220px] max-w-[260px] flex-shrink-0">
                    
                    <!-- Decorative Corner Highlight -->
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-white/10 to-transparent rounded-bl-full pointer-events-none"></div>

                    <!-- Category Icon Container -->
                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-3 flex items-center justify-center rounded-2xl bg-white/10 group-hover:bg-white/20 border border-white/20 transition-all duration-300 shadow-xl"
                         style="group-hover:border-color: var(--secondary);">
                        <?php if (($cat['icon_type'] ?? '') === 'image' && !empty($cat['icon_image'])): ?>
                            <img src="<?= e($cat['icon_image']) ?>" alt="<?= e($cat['name']) ?>" class="card-icon-glow w-14 h-14 sm:w-16 sm:h-16 object-contain filter drop-shadow">
                        <?php else: ?>
                            <i class="card-icon-glow fa-solid <?= e($cat['fa_icon'] ?? 'fa-folder') ?> text-3xl sm:text-4xl" style="color: var(--secondary);"></i>
                        <?php endif; ?>
                    </div>

                    <!-- Title & Subtitle -->
                    <div class="w-full">
                        <h2 class="text-base sm:text-lg font-black tracking-wider text-white transition uppercase mb-1 group-hover:text-[var(--secondary)]">
                            <?= e($cat['name']) ?>
                        </h2>
                        <p class="text-[11px] text-slate-300 line-clamp-1 mb-3.5">
                            <?= e($cat['subname'] ?? $cat['description'] ?? '') ?>
                        </p>
                        
                        <!-- Open Category Badge / Button -->
                        <div class="card-action-btn inline-flex items-center space-x-1.5 px-3.5 py-1 rounded-full text-xs font-bold transition shadow"
                             style="background: rgba(var(--secondary-rgb), 0.18); color: var(--secondary); border: 1px solid rgba(var(--secondary-rgb), 0.35);">
                            <span><?= $count ?> Link</span>
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </div>
                    </div>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-white/10 bg-black/40 backdrop-blur-xl py-6 mt-12 text-center text-xs text-slate-300">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                Copyright &copy; <?= e($config['copyright_year'] ?? date('Y')) ?> 
                <span class="font-bold text-white"><?= e($config['footer_text'] ?? $config['school_name'] ?? 'Portal Sekolah Terpadu') ?></span>. All Rights Reserved.
            </div>

            <!-- Social Links & Admin -->
            <div class="flex items-center space-x-4 text-base">
                <?php if (!empty($config['social_website'])): ?>
                <a href="<?= e($config['social_website']) ?>" target="_blank" title="Website Resmi" class="text-slate-400 hover:text-white transition" style="hover:color: var(--secondary);">
                    <i class="fa-solid fa-globe"></i>
                </a>
                <?php endif; ?>
                <?php if (!empty($config['social_youtube'])): ?>
                <a href="<?= e($config['social_youtube']) ?>" target="_blank" title="YouTube" class="text-slate-400 hover:text-red-400 transition">
                    <i class="fa-brands fa-youtube"></i>
                </a>
                <?php endif; ?>
                <?php if (!empty($config['social_instagram'])): ?>
                <a href="<?= e($config['social_instagram']) ?>" target="_blank" title="Instagram" class="text-slate-400 hover:text-pink-400 transition">
                    <i class="fa-brands fa-instagram"></i>
                </a>
                <?php endif; ?>
                <?php if (!empty($config['social_whatsapp'])): ?>
                <a href="<?= e($config['social_whatsapp']) ?>" target="_blank" title="WhatsApp Center" class="text-slate-400 hover:text-green-400 transition">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
                <?php endif; ?>
                <a href="admin/index.php" title="Panel Admin" class="text-slate-400 hover:text-white transition" style="hover:color: var(--secondary);">
                    <i class="fa-solid fa-gear"></i>
                </a>
            </div>
        </div>
    </footer>

    <!-- Interactive Category Popover / Modal (Untuk Mode Kartu 3D) -->
    <div id="category-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
        <div id="modal-backdrop" class="fixed inset-0 bg-black/80 backdrop-blur-md transition-opacity"></div>
        <div class="relative w-full max-w-3xl rounded-3xl bg-slate-900/95 border border-white/20 shadow-2xl overflow-hidden modal-enter z-10 flex flex-col max-h-[90vh]">
            <div class="p-5 sm:p-6 border-b border-white/10 flex items-center justify-between bg-white/5">
                <div class="flex items-center space-x-4">
                    <div id="modal-icon" class="w-12 h-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0"></div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 id="modal-title" class="text-xl sm:text-2xl font-black text-white uppercase tracking-wide"></h3>
                            <span id="modal-badge" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold"
                                  style="background: rgba(var(--secondary-rgb), 0.2); color: var(--secondary); border: 1px solid rgba(var(--secondary-rgb), 0.4);"></span>
                        </div>
                        <p id="modal-subtitle" class="text-xs text-slate-300 mt-0.5"></p>
                    </div>
                </div>
                <button type="button" id="modal-close-btn" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 text-white flex items-center justify-center transition focus:outline-none">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="px-5 py-3 border-b border-white/10 bg-black/25">
                <div class="relative flex items-center">
                    <i class="fa-solid fa-filter absolute left-3 text-xs" style="color: var(--secondary);"></i>
                    <input type="text" id="modal-filter-input" placeholder="Saring link di kategori ini..." 
                           class="w-full pl-9 pr-4 py-2 rounded-xl bg-white/10 border border-white/15 text-xs sm:text-sm text-white placeholder-slate-400 focus:outline-none focus:ring-1"
                           style="focus:ring-color: var(--secondary);">
                </div>
            </div>

            <div class="p-5 sm:p-6 overflow-y-auto flex-grow">
                <div id="modal-shortcuts-list" class="grid grid-cols-1 sm:grid-cols-2 gap-3.5"></div>
            </div>
        </div>
    </div>

    <!-- Data Injection for Client JavaScript -->
    <script>
        window.PORTAL_CATEGORIES = <?= json_encode($categoryMap, JSON_UNESCAPED_UNICODE) ?>;
        window.PORTAL_SHORTCUTS = <?= json_encode($shortcutsByCategory, JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <script src="assets/js/app.js"></script>

    <!-- Simple Tabs Client Filter Logic -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const filterTabs = document.querySelectorAll('.filter-tab');
        const cards = document.querySelectorAll('.shortcut-card-tile');
        const noResults = document.getElementById('no-results-message');
        const searchInput = document.getElementById('simple-search-input');

        let activeCategory = 'all';

        filterTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                filterTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                activeCategory = tab.getAttribute('data-filter');
                applyFilters();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                applyFilters();
            });
        }

        function applyFilters() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            let visibleCount = 0;

            cards.forEach(card => {
                const cardCat = card.getAttribute('data-cat');
                const title = card.getAttribute('data-title') || '';
                const desc = card.getAttribute('data-desc') || '';
                const badge = card.getAttribute('data-badge') || '';

                const matchesCat = (activeCategory === 'all' || cardCat === activeCategory);
                const matchesQuery = (!query || title.includes(query) || desc.includes(query) || badge.includes(query));

                if (matchesCat && matchesQuery) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            if (noResults) {
                if (visibleCount === 0) {
                    noResults.classList.remove('hidden');
                } else {
                    noResults.classList.add('hidden');
                }
            }
        }
    });
    </script>
</body>
</html>
