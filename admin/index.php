<?php
$pageTitle = 'Ringkasan Dashboard';
require_once __DIR__ . '/header.php';

$categories = get_categories();
$shortcuts = get_shortcuts();

$totalCategories = count($categories);
$totalShortcuts = count($shortcuts);

$totalClicks = 0;
$pinnedCount = 0;
foreach ($shortcuts as $sc) {
    $totalClicks += intval($sc['clicks'] ?? 0);
    if (!empty($sc['is_pinned'])) $pinnedCount++;
}

// Top 5 popular shortcuts
$popular = $shortcuts;
usort($popular, fn($a, $b) => ($b['clicks'] ?? 0) <=> ($a['clicks'] ?? 0));
$topPopular = array_slice($popular, 0, 5);

$catMap = [];
foreach ($categories as $c) {
    $catMap[$c['id']] = $c['name'];
}
?>

<div class="space-y-8">

    <!-- Welcome Hero Banner -->
    <div class="relative rounded-3xl p-6 sm:p-8 bg-gradient-to-r from-emerald-950 via-slate-900 to-slate-900 border border-emerald-500/30 overflow-hidden shadow-2xl">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-block px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider mb-2">
                Panel Kontrol Aktif
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Selamat Datang, <?= e($_SESSION['admin_name'] ?? 'Administrator') ?>!
            </h2>
            <p class="text-sm text-slate-300 mt-2 leading-relaxed">
                Anda dapat mengubah seluruh warna tema, latar belakang gambar/gradien, nama madrasah, logo, serta menambah dan mengedit pintasan link aplikasi madrasah secara langsung.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="settings.php" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider transition shadow-lg flex items-center gap-2">
                    <i class="fa-solid fa-palette"></i>
                    <span>Ubah Warna & Tampilan</span>
                </a>
                <a href="shortcuts.php?action=add" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider transition shadow-lg flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Pintasan Baru</span>
                </a>
            </div>
        </div>
        <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Card 1: Total Kategori -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kategori Menu</p>
                <h3 class="text-3xl font-black text-white mt-1"><?= $totalCategories ?></h3>
                <p class="text-[11px] text-emerald-400 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-folder-tree"></i> Grup navigasi aktif
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-500/20 text-blue-400 border border-blue-500/30 flex items-center justify-center text-xl">
                <i class="fa-solid fa-layer-group"></i>
            </div>
        </div>

        <!-- Card 2: Total Pintasan -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pintasan / Link</p>
                <h3 class="text-3xl font-black text-white mt-1"><?= $totalShortcuts ?></h3>
                <p class="text-[11px] text-amber-400 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-link"></i> Terdaftar di portal
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-500/20 text-purple-400 border border-purple-500/30 flex items-center justify-center text-xl">
                <i class="fa-solid fa-link"></i>
            </div>
        </div>

        <!-- Card 3: Pinned / Akses Cepat -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Akses Cepat (Favorit)</p>
                <h3 class="text-3xl font-black text-white mt-1"><?= $pinnedCount ?></h3>
                <p class="text-[11px] text-amber-300 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-bolt"></i> Muncul di baris atas
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center text-xl">
                <i class="fa-solid fa-star"></i>
            </div>
        </div>

        <!-- Card 4: Total Klik / Trafik -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Klik Akses</p>
                <h3 class="text-3xl font-black text-white mt-1"><?= number_format($totalClicks) ?></h3>
                <p class="text-[11px] text-emerald-400 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-arrow-trend-up"></i> Interaksi guru & siswa
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xl">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Top 5 Popular Apps -->
        <div class="lg:col-span-2 rounded-3xl bg-slate-900 border border-slate-800 p-6 shadow-xl">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-fire text-amber-400"></i>
                        <span>Aplikasi Paling Sering Diakses</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Statistik link dengan jumlah klik tertinggi di portal madrasah</p>
                </div>
                <a href="shortcuts.php" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-800">
                <?php foreach ($topPopular as $idx => $sc): ?>
                <div class="py-3.5 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="w-6 text-center font-black text-sm <?= $idx === 0 ? 'text-amber-400' : ($idx === 1 ? 'text-slate-300' : 'text-slate-500') ?>">
                            #<?= $idx + 1 ?>
                        </span>
                        <div class="w-9 h-9 rounded-xl bg-slate-800 flex items-center justify-center text-sm text-emerald-400 border border-slate-700">
                            <i class="<?= e($sc['fa_icon'] ?? 'fa-solid fa-link') ?>"></i>
                        </div>
                        <div>
                            <div class="font-bold text-sm text-white flex items-center gap-2">
                                <span><?= e($sc['title']) ?></span>
                                <?php if (!empty($sc['badge'])): ?>
                                <span class="text-[10px] bg-slate-800 border border-slate-700 px-2 py-0.5 rounded text-slate-300"><?= e($sc['badge']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="text-xs text-slate-400"><?= e($catMap[$sc['category_id'] ?? ''] ?? 'Umum') ?></div>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="inline-block px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold">
                            <?= number_format($sc['clicks'] ?? 0) ?> Klik
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Right 1 Col: Quick Configuration Info -->
        <div class="rounded-3xl bg-slate-900 border border-slate-800 p-6 shadow-xl flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-circle-info text-blue-400"></i>
                    <span>Status Pengaturan Portal</span>
                </h3>

                <div class="space-y-3.5 text-xs">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="text-slate-400">Nama Sekolah:</span>
                        <span class="font-bold text-white truncate max-w-[160px]"><?= e($config['school_name'] ?? '-') ?></span>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="text-slate-400">Mode Background:</span>
                        <span class="font-bold uppercase text-amber-400"><?= e($config['bg_type'] ?? 'gradient') ?></span>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="text-slate-400">Warna Utama (Primary):</span>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full border border-white/30" style="background-color: <?= e($config['primary_color'] ?? '#00873e') ?>;"></span>
                            <span class="font-mono text-slate-300"><?= e($config['primary_color'] ?? '#00873e') ?></span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="text-slate-400">Warna Aksen:</span>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full border border-white/30" style="background-color: <?= e($config['secondary_color'] ?? '#f59e0b') ?>;"></span>
                            <span class="font-mono text-slate-300"><?= e($config['secondary_color'] ?? '#f59e0b') ?></span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="text-slate-400">Live Search:</span>
                        <span class="text-emerald-400 font-bold"><?= !empty($config['show_search']) ? 'Aktif' : 'Nonaktif' ?></span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Penyimpanan Data:</span>
                        <span class="font-semibold text-emerald-300">Atomic JSON Safe File</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-800">
                <a href="settings.php" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition flex items-center justify-center gap-2 border border-slate-700">
                    <i class="fa-solid fa-sliders"></i>
                    <span>Buka Pengaturan Lengkap</span>
                </a>
            </div>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>
