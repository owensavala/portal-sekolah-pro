<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/presets.php';

require_login();

$csrf_token = generate_csrf_token();
$error = '';
$schoolPresets = get_school_presets();

// 1. Handle 1-Click School Preset Apply (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_school_preset'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Validasi keamanan gagal.';
    } else {
        $presetKey = $_POST['preset_key'] ?? '';
        if (isset($schoolPresets[$presetKey])) {
            $preset = $schoolPresets[$presetKey];
            $currentConfig = get_config();

            // Merge config
            $newConfig = array_merge($currentConfig, $preset['config']);
            save_config($newConfig);

            // Also load default categories & shortcuts if requested
            if (!empty($_POST['include_sample_links'])) {
                save_categories($preset['categories']);
                save_shortcuts($preset['shortcuts']);
            }

            set_flash_message('success', "Template '{$preset['label']}' berhasil diterapkan!");
            header('Location: settings.php');
            exit;
        }
    }
}

// 2. Handle Regular Settings Save (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['apply_school_preset'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Validasi keamanan gagal (CSRF mismatch).';
    } else {
        $config = get_config();

        // School Identity & Type
        $config['school_name'] = trim($_POST['school_name'] ?? $config['school_name']);
        $config['school_location'] = trim($_POST['school_location'] ?? $config['school_location']);
        $config['school_type_badge'] = trim($_POST['school_type_badge'] ?? 'Portal Sekolah');
        $config['tagline'] = trim($_POST['tagline'] ?? $config['tagline']);
        $config['site_title'] = trim($_POST['site_title'] ?? $config['site_title']);
        $config['footer_text'] = trim($_POST['footer_text'] ?? $config['footer_text']);
        $config['copyright_year'] = trim($_POST['copyright_year'] ?? date('Y'));

        // View Mode: 'simple' vs 'launcher'
        $config['view_mode'] = $_POST['view_mode'] ?? 'launcher';

        // Background Mode
        $config['bg_type'] = $_POST['bg_type'] ?? 'gradient';
        $config['bg_color_start'] = $_POST['bg_color_start'] ?? '#032b17';
        $config['bg_color_end'] = $_POST['bg_color_end'] ?? '#0b532c';
        $config['bg_gradient_angle'] = $_POST['bg_gradient_angle'] ?? '135';
        $config['bg_solid_color'] = $_POST['bg_solid_color'] ?? '#032b17';
        $config['bg_overlay_color'] = $_POST['bg_overlay_color'] ?? '#082f49';
        $config['bg_overlay_opacity'] = floatval($_POST['bg_overlay_opacity'] ?? 0.75);

        // Preset Wallpaper selection if chosen from gallery
        if (!empty($_POST['selected_wallpaper'])) {
            $config['bg_image'] = $_POST['selected_wallpaper'];
            $config['bg_type'] = 'image';
        }

        // Colors
        $config['primary_color'] = $_POST['primary_color'] ?? '#00873e';
        $config['secondary_color'] = $_POST['secondary_color'] ?? '#f59e0b';
        $config['accent_color'] = $_POST['accent_color'] ?? '#10b981';

        // Feature Toggles
        $config['show_search'] = isset($_POST['show_search']);
        $config['show_quick_access'] = isset($_POST['show_quick_access']);

        // Social Links
        $config['social_website'] = trim($_POST['social_website'] ?? '');
        $config['social_youtube'] = trim($_POST['social_youtube'] ?? '');
        $config['social_instagram'] = trim($_POST['social_instagram'] ?? '');
        $config['social_whatsapp'] = trim($_POST['social_whatsapp'] ?? '');

        // Handle File Uploads (Logo)
        if (!empty($_FILES['logo_file']['name'])) {
            $up = handle_file_upload($_FILES['logo_file'], ['png', 'jpg', 'jpeg', 'webp', 'svg']);
            if ($up['success']) {
                $config['logo'] = $up['path'];
            } else {
                $error = 'Upload Logo gagal: ' . $up['error'];
            }
        }

        // Favicon
        if (!empty($_FILES['favicon_file']['name'])) {
            $up = handle_file_upload($_FILES['favicon_file'], ['ico', 'png', 'svg']);
            if ($up['success']) {
                $config['favicon'] = $up['path'];
            }
        }

        // Background Wallpaper Custom Upload
        if (!empty($_FILES['bg_image_file']['name'])) {
            $up = handle_file_upload($_FILES['bg_image_file'], ['jpg', 'jpeg', 'png', 'webp', 'svg']);
            if ($up['success']) {
                $config['bg_image'] = $up['path'];
                $config['bg_type'] = 'image';
            } else {
                $error = 'Upload Gambar Background gagal: ' . $up['error'];
            }
        }

        if (empty($error)) {
            save_config($config);
            set_flash_message('success', 'Pengaturan tampilan, warna, logo, dan identitas berhasil diperbarui!');
            header('Location: settings.php');
            exit;
        }
    }
}

// 3. Render Page Header
$pageTitle = 'Tampilan, Warna & Logo';
require_once __DIR__ . '/header.php';
$config = get_config();
?>

<div class="max-w-5xl mx-auto space-y-8">

    <?php if (!empty($error)): ?>
    <div class="p-4 rounded-2xl bg-red-900/50 border border-red-500/50 text-red-200 text-sm flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation text-base"></i>
        <span><?= e($error) ?></span>
    </div>
    <?php endif; ?>

    <!-- 🌟 CARD 0: PILIH TEMPLATE JENIS SEKOLAH (1-KLIK) -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950/60 border border-indigo-500/30 shadow-2xl space-y-5">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div>
                <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-400/30 text-[10px] font-black uppercase tracking-wider">
                    Universal Multi-Sekolah
                </span>
                <h3 class="text-base font-bold text-white mt-1 flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-400"></i>
                    <span>Template Jenis Sekolah Siap Pakai (1-Klik)</span>
                </h3>
                <p class="text-xs text-slate-300 mt-0.5">
                    Gunakan portal ini untuk jenis sekolah apa pun (Madrasah, SD/SMP/SMA Kemdikbud, SMK Kejuruan, atau Pesantren).
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <?php foreach ($schoolPresets as $key => $preset): ?>
            <form method="POST" action="settings.php" onsubmit="return confirm('Terapkan template <?= addslashes($preset['label']) ?>? Warna, identitas dasar, dan pengaturan akan disesuaikan.');" class="p-4 rounded-2xl bg-slate-800/80 hover:bg-slate-800 border border-slate-700 hover:border-indigo-400 transition flex flex-col justify-between">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
                <input type="hidden" name="apply_school_preset" value="1">
                <input type="hidden" name="preset_key" value="<?= e($key) ?>">

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-300 flex items-center justify-center text-sm">
                            <i class="fa-solid <?= e($preset['icon']) ?>"></i>
                        </span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-900 text-slate-300 border border-slate-700">
                            <?= e($preset['badge']) ?>
                        </span>
                    </div>
                    <h4 class="font-bold text-xs text-white line-clamp-1"><?= e($preset['label']) ?></h4>
                    <p class="text-[11px] text-slate-400 mt-1 line-clamp-2"><?= e($preset['description']) ?></p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-700/60">
                    <label class="flex items-center gap-1.5 text-[10px] text-slate-300 mb-2 cursor-pointer">
                        <input type="checkbox" name="include_sample_links" value="1" class="rounded text-indigo-500 bg-slate-900 border-slate-600">
                        <span>Reset ke link contoh <?= e($preset['badge']) ?></span>
                    </label>
                    <button type="submit" class="w-full py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition shadow flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-check text-[10px]"></i>
                        <span>Pilih Template Ini</span>
                    </button>
                </div>
            </form>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- MAIN SETTINGS FORM -->
    <form method="POST" action="settings.php" enctype="multipart/form-data" class="space-y-8">
        <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
        <input type="hidden" id="selected_wallpaper" name="selected_wallpaper" value="">

        <!-- CARD 1: GAYA TAMPILAN (LAYOUT MODE) -->
        <div class="p-6 sm:p-8 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-5">
            <div class="border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-table-cells-large text-emerald-400"></i>
                    <span>1. Gaya Tata Letak Halaman Depan</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Pilih tampilan yang paling nyaman untuk guru dan siswa</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Mode 1: Launcher Kartu 3D -->
                <label class="cursor-pointer p-4 rounded-2xl border-2 transition flex items-start space-x-3 <?= (($config['view_mode'] ?? 'launcher') === 'launcher') ? 'border-amber-400 bg-amber-500/10' : 'border-slate-800 bg-slate-800/40 hover:border-slate-700' ?>">
                    <input type="radio" name="view_mode" value="launcher" <?= (($config['view_mode'] ?? 'launcher') === 'launcher') ? 'checked' : '' ?> class="mt-1 text-amber-500 focus:ring-amber-400">
                    <div>
                        <div class="font-bold text-sm text-white flex items-center gap-2">
                            <span>Mode Kartu 3D Kategori (Launcher Popover)</span>
                            <span class="text-[10px] bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded-full font-bold">Animasi 3D</span>
                        </div>
                        <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                            Menampilkan kartu 3D interaktif yang bergerak miring mengikuti kursor mouse (3D tilt). Saat kartu diklik, jendela modal elegan akan terbuka menampilkan semua link di dalamnya.
                        </p>
                    </div>
                </label>

                <!-- Mode 2: Simpel & Bersih -->
                <label class="cursor-pointer p-4 rounded-2xl border-2 transition flex items-start space-x-3 <?= (($config['view_mode'] ?? '') === 'simple') ? 'border-amber-400 bg-amber-500/10' : 'border-slate-800 bg-slate-800/40 hover:border-slate-700' ?>">
                    <input type="radio" name="view_mode" value="simple" <?= (($config['view_mode'] ?? '') === 'simple') ? 'checked' : '' ?> class="mt-1 text-amber-500 focus:ring-amber-400">
                    <div>
                        <div class="font-bold text-sm text-white">Mode Simpel & Bersih (Clean Grid Tabs)</div>
                        <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                            Semua link tampil langsung dalam grid rapi dengan filter tombol tab kategori horizontal. Sangat cepat, to-the-point, dan bisa langsung diklik dalam 1 detik.
                        </p>
                    </div>
                </label>
            </div>
        </div>

        <!-- CARD 2: Identitas Sekolah & Logo -->
        <div class="p-6 sm:p-8 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-6">
            <div class="border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-school text-blue-400"></i>
                    <span>2. Identitas Sekolah & Logo</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Ubah nama sekolah, jenis instansi, slogan, judul portal, dan upload logo resmi</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Nama Sekolah / Instansi</label>
                    <input type="text" name="school_name" value="<?= e($config['school_name'] ?? '') ?>" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Label Jenis Sekolah (Badge)</label>
                    <input type="text" name="school_type_badge" value="<?= e($config['school_type_badge'] ?? 'Sekolah Kemendikbud') ?>" placeholder="Contoh: Madrasah Kemenag / SMP Negeri / SMK Vokasi"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Lokasi / Kabupaten / Kota</label>
                    <input type="text" name="school_location" value="<?= e($config['school_location'] ?? '') ?>"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Slogan / Visi Singkat</label>
                    <input type="text" name="tagline" value="<?= e($config['tagline'] ?? '') ?>"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Judul Tab Browser (HTML Title)</label>
                    <input type="text" name="site_title" value="<?= e($config['site_title'] ?? '') ?>"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>

            <!-- Logo & Favicon Upload -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-800">
                <!-- Logo -->
                <div class="p-4 rounded-2xl bg-slate-800/50 border border-slate-700/60 flex items-start space-x-4">
                    <div class="w-20 h-20 rounded-2xl bg-slate-900 border border-slate-700 p-2 flex items-center justify-center shrink-0">
                        <img src="<?= e(asset_url($config['logo'] ?? 'uploads/Logo_Instansi.png', true)) ?>" alt="Logo Saat Ini" class="max-h-full max-w-full object-contain">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-white mb-1">Upload Logo Sekolah Baru</label>
                        <p class="text-[11px] text-slate-400 mb-2">Format PNG/SVG transparan disarankan. Maks 5MB.</p>
                        <input type="file" name="logo_file" accept="image/*"
                               class="block w-full text-xs text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500 cursor-pointer">
                    </div>
                </div>

                <!-- Favicon -->
                <div class="p-4 rounded-2xl bg-slate-800/50 border border-slate-700/60 flex items-start space-x-4">
                    <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-700 p-2 flex items-center justify-center shrink-0">
                        <img src="<?= e(asset_url($config['favicon'] ?? 'uploads/favicon.ico', true)) ?>" alt="Favicon" class="max-h-full max-w-full object-contain">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-white mb-1">Ganti Favicon Tab</label>
                        <p class="text-[11px] text-slate-400 mb-2">Ikon kecil pada tab browser (ICO/PNG).</p>
                        <input type="file" name="favicon_file" accept=".ico,.png"
                               class="block w-full text-xs text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-white hover:file:bg-slate-600 cursor-pointer">
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 3: Pengaturan Background & Galeri Wallpaper -->
        <div class="p-6 sm:p-8 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-6">
            <div class="border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-image text-amber-400"></i>
                    <span>3. Pengaturan Background Latar Belakang</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Pilih wallpaper foto, gradien halus, atau warna solid dengan kontrol warna overlay</p>
            </div>

            <!-- Radio Mode Selection -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <label class="cursor-pointer p-4 rounded-2xl border-2 transition flex items-center space-x-3 <?= ($config['bg_type'] === 'image') ? 'border-amber-400 bg-amber-500/10' : 'border-slate-800 bg-slate-800/40 hover:border-slate-700' ?>">
                    <input type="radio" name="bg_type" value="image" <?= ($config['bg_type'] === 'image') ? 'checked' : '' ?> onchange="toggleBgOptions('image')" class="text-amber-500 focus:ring-amber-400">
                    <div>
                        <div class="font-bold text-sm text-white">Foto / Wallpaper</div>
                        <div class="text-[11px] text-slate-400">Gedung / pemandangan alam</div>
                    </div>
                </label>

                <label class="cursor-pointer p-4 rounded-2xl border-2 transition flex items-center space-x-3 <?= ($config['bg_type'] === 'gradient') ? 'border-amber-400 bg-amber-500/10' : 'border-slate-800 bg-slate-800/40 hover:border-slate-700' ?>">
                    <input type="radio" name="bg_type" value="gradient" <?= ($config['bg_type'] === 'gradient') ? 'checked' : '' ?> onchange="toggleBgOptions('gradient')" class="text-amber-500 focus:ring-amber-400">
                    <div>
                        <div class="font-bold text-sm text-white">Gradien Halus</div>
                        <div class="text-[11px] text-slate-400">Modern & bersih di mata</div>
                    </div>
                </label>

                <label class="cursor-pointer p-4 rounded-2xl border-2 transition flex items-center space-x-3 <?= ($config['bg_type'] === 'solid') ? 'border-amber-400 bg-amber-500/10' : 'border-slate-800 bg-slate-800/40 hover:border-slate-700' ?>">
                    <input type="radio" name="bg_type" value="solid" <?= ($config['bg_type'] === 'solid') ? 'checked' : '' ?> onchange="toggleBgOptions('solid')" class="text-amber-500 focus:ring-amber-400">
                    <div>
                        <div class="font-bold text-sm text-white">Warna Solid</div>
                        <div class="text-[11px] text-slate-400">1 warna polos bersih</div>
                    </div>
                </label>
            </div>

            <!-- Dynamic Options: Image Wallpaper with Curated Gallery -->
            <div id="bg-option-image" class="p-5 rounded-2xl bg-slate-800/40 border border-slate-700/60 space-y-5 <?= ($config['bg_type'] !== 'image') ? 'hidden' : '' ?>">
                
                <!-- 🌟 GALERI WALLPAPER 1-KLIK -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-photo-film text-amber-400"></i>
                        <span>Pilih Dari Galeri Wallpaper Siap Pakai (1-Klik):</span>
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <!-- Wallpaper 1: Danau & Benteng Ternate (Gambar Upload Pengguna) -->
                        <button type="button" onclick="selectPresetWallpaper('uploads/up_6afed119aa9e5_9cc281f2.png')" 
                                class="wallpaper-thumb p-2 rounded-xl bg-slate-900 border-2 border-slate-700 hover:border-amber-400 transition text-left group">
                            <div class="h-20 w-full rounded-lg bg-cover bg-center mb-1.5 overflow-hidden border border-white/10 group-hover:scale-105 transition" style="background-image: url('../uploads/up_6afed119aa9e5_9cc281f2.png');"></div>
                            <div class="text-[11px] font-bold text-white truncate">Benteng & Danau</div>
                            <div class="text-[9px] text-slate-400">Pemandangan Alam</div>
                        </button>

                        <!-- Wallpaper 2: Kampus Modern -->
                        <button type="button" onclick="selectPresetWallpaper('uploads/wallpapers/modern_campus.svg')" 
                                class="wallpaper-thumb p-2 rounded-xl bg-slate-900 border-2 border-slate-700 hover:border-amber-400 transition text-left group">
                            <div class="h-20 w-full rounded-lg bg-cover bg-center mb-1.5 overflow-hidden border border-white/10 group-hover:scale-105 transition" style="background-image: url('../uploads/wallpapers/modern_campus.svg');"></div>
                            <div class="text-[11px] font-bold text-white truncate">Kampus Modern</div>
                            <div class="text-[9px] text-slate-400">Arsitektur Biru</div>
                        </button>

                        <!-- Wallpaper 3: Islamic Geometry -->
                        <button type="button" onclick="selectPresetWallpaper('uploads/wallpapers/islamic_geometry.svg')" 
                                class="wallpaper-thumb p-2 rounded-xl bg-slate-900 border-2 border-slate-700 hover:border-amber-400 transition text-left group">
                            <div class="h-20 w-full rounded-lg bg-cover bg-center mb-1.5 overflow-hidden border border-white/10 group-hover:scale-105 transition" style="background-image: url('../uploads/wallpapers/islamic_geometry.svg');"></div>
                            <div class="text-[11px] font-bold text-white truncate">Islamic Arabesque</div>
                            <div class="text-[9px] text-slate-400">Bintang Zamrud</div>
                        </button>

                        <!-- Wallpaper 4: Cyber Vokasi -->
                        <button type="button" onclick="selectPresetWallpaper('uploads/wallpapers/cyber_vokasi.svg')" 
                                class="wallpaper-thumb p-2 rounded-xl bg-slate-900 border-2 border-slate-700 hover:border-amber-400 transition text-left group">
                            <div class="h-20 w-full rounded-lg bg-cover bg-center mb-1.5 overflow-hidden border border-white/10 group-hover:scale-105 transition" style="background-image: url('../uploads/wallpapers/cyber_vokasi.svg');"></div>
                            <div class="text-[11px] font-bold text-white truncate">Cyber Grid Vokasi</div>
                            <div class="text-[9px] text-slate-400">Teknologi SMK</div>
                        </button>
                    </div>
                </div>

                <!-- Custom Wallpaper Upload -->
                <div class="pt-4 border-t border-slate-700/60 grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <div>
                        <label class="block text-xs font-bold text-white mb-1">Atau Upload Foto Sendiri dari Komputer</label>
                        <input type="file" name="bg_image_file" accept="image/*"
                               class="block w-full text-xs text-slate-300 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 cursor-pointer">
                    </div>

                    <!-- Overlay Tint Color & Opacity Slider -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-bold text-white">Warna Lapisan Gelap (Overlay Tint)</label>
                            <span id="opacity-val" class="text-amber-400 font-extrabold text-xs"><?= intval(($config['bg_overlay_opacity'] ?? 0.75) * 100) ?>%</span>
                        </div>
                        <div class="flex items-center gap-2 mb-2">
                            <input type="color" id="input_overlay_color" name="bg_overlay_color" value="<?= e($config['bg_overlay_color'] ?? '#082f49') ?>"
                                   class="w-8 h-8 rounded-lg cursor-pointer bg-transparent border border-slate-600">
                            <div class="flex gap-1.5">
                                <button type="button" onclick="setOverlayColor('#082f49')" class="px-2 py-0.5 rounded text-[10px] bg-[#082f49] border border-white/20 text-white">Navy</button>
                                <button type="button" onclick="setOverlayColor('#032b17')" class="px-2 py-0.5 rounded text-[10px] bg-[#032b17] border border-white/20 text-white">Zamrud</button>
                                <button type="button" onclick="setOverlayColor('#0f172a')" class="px-2 py-0.5 rounded text-[10px] bg-[#0f172a] border border-white/20 text-white">Charcoal</button>
                                <button type="button" onclick="setOverlayColor('#000000')" class="px-2 py-0.5 rounded text-[10px] bg-[#000000] border border-white/20 text-white">Hitam</button>
                            </div>
                        </div>
                        <input type="range" name="bg_overlay_opacity" min="0.1" max="0.95" step="0.05" 
                               value="<?= e($config['bg_overlay_opacity'] ?? 0.75) ?>"
                               oninput="document.getElementById('opacity-val').innerText = Math.round(this.value * 100) + '%'"
                               class="w-full accent-amber-500">
                        <p class="text-[10px] text-slate-400 mt-1">Menggelapkan foto agar teks, judul, dan ikon kartu terbaca sangat tajam.</p>
                    </div>
                </div>

                <?php if (!empty($config['bg_image'])): ?>
                <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-700 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2 truncate">
                        <span class="text-slate-400">File Aktif:</span>
                        <span class="text-emerald-400 font-mono truncate"><?= e($config['bg_image']) ?></span>
                    </div>
                    <a href="<?= e(asset_url($config['bg_image'], true)) ?>" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 hover:text-white text-[11px] font-bold">
                        Lihat Gambar Asli
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Dynamic Options: Gradient -->
            <div id="bg-option-gradient" class="p-5 rounded-2xl bg-slate-800/40 border border-slate-700/60 space-y-4 <?= ($config['bg_type'] !== 'gradient') ? 'hidden' : '' ?>">
                <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Konfigurasi Gradien</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Warna Awal (Start)</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="bg_color_start" value="<?= e($config['bg_color_start'] ?? '#032b17') ?>"
                                   class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border border-slate-600">
                            <input type="text" value="<?= e($config['bg_color_start'] ?? '#032b17') ?>" oninput="this.previousElementSibling.value = this.value"
                                   class="w-24 px-2 py-1 text-xs rounded bg-slate-900 border border-slate-700 text-slate-200 font-mono">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Warna Akhir (End)</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="bg_color_end" value="<?= e($config['bg_color_end'] ?? '#0b532c') ?>"
                                   class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border border-slate-600">
                            <input type="text" value="<?= e($config['bg_color_end'] ?? '#0b532c') ?>" oninput="this.previousElementSibling.value = this.value"
                                   class="w-24 px-2 py-1 text-xs rounded bg-slate-900 border border-slate-700 text-slate-200 font-mono">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Arah Sudut Gradien</label>
                        <select name="bg_gradient_angle" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-xs text-white">
                            <option value="135" <?= ($config['bg_gradient_angle'] ?? '135') == '135' ? 'selected' : '' ?>>135° (Diagonal Menurun)</option>
                            <option value="90" <?= ($config['bg_gradient_angle'] ?? '') == '90' ? 'selected' : '' ?>>90° (Kiri ke Kanan)</option>
                            <option value="180" <?= ($config['bg_gradient_angle'] ?? '') == '180' ? 'selected' : '' ?>>180° (Atas ke Bawah)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Dynamic Options: Solid Color -->
            <div id="bg-option-solid" class="p-5 rounded-2xl bg-slate-800/40 border border-slate-700/60 space-y-4 <?= ($config['bg_type'] !== 'solid') ? 'hidden' : '' ?>">
                <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Pilih Warna Solid</h4>
                <div class="flex items-center gap-3">
                    <input type="color" name="bg_solid_color" value="<?= e($config['bg_solid_color'] ?? '#032b17') ?>"
                           class="w-12 h-12 rounded-xl cursor-pointer bg-transparent border border-slate-600">
                    <input type="text" value="<?= e($config['bg_solid_color'] ?? '#032b17') ?>" oninput="this.previousElementSibling.value = this.value"
                           class="w-32 px-3 py-2 text-sm rounded-xl bg-slate-900 border border-slate-700 text-slate-200 font-mono">
                </div>
            </div>
        </div>

        <!-- CARD 4: Warna Utama & Live Preview -->
        <div class="p-6 sm:p-8 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-6">
            <div class="border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-palette text-purple-400"></i>
                    <span>4. Kustomisasi Warna Utama, Sekunder & Aksen</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Warna ini otomatis mengubah kartu, tombol, ikon, dan efek bercahaya pada halaman utama</p>
            </div>

            <!-- Quick Color Palettes -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Preset Palet Warna 1-Klik:</label>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                    <button type="button" onclick="applyColorPalette('#0284c7', '#38bdf8', '#0ea5e9')" 
                            class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-left transition">
                        <div class="flex gap-1 mb-1.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#0284c7]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#38bdf8]"></span>
                        </div>
                        <div class="text-[11px] font-bold text-white">Biru Kemdikbud</div>
                    </button>

                    <button type="button" onclick="applyColorPalette('#00873e', '#f59e0b', '#10b981')" 
                            class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-left transition">
                        <div class="flex gap-1 mb-1.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#00873e]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#f59e0b]"></span>
                        </div>
                        <div class="text-[11px] font-bold text-white">Hijau Zamrud</div>
                    </button>

                    <button type="button" onclick="applyColorPalette('#0284c7', '#f97316', '#38bdf8')" 
                            class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-left transition">
                        <div class="flex gap-1 mb-1.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#0284c7]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#f97316]"></span>
                        </div>
                        <div class="text-[11px] font-bold text-white">Cyber Vokasi</div>
                    </button>

                    <button type="button" onclick="applyColorPalette('#7c3aed', '#f59e0b', '#a855f7')" 
                            class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-left transition">
                        <div class="flex gap-1 mb-1.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#7c3aed]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#f59e0b]"></span>
                        </div>
                        <div class="text-[11px] font-bold text-white">Royal Amethyst</div>
                    </button>

                    <button type="button" onclick="applyColorPalette('#10b981', '#38bdf8', '#34d399')" 
                            class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-left transition">
                        <div class="flex gap-1 mb-1.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#10b981]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#38bdf8]"></span>
                        </div>
                        <div class="text-[11px] font-bold text-white">Midnight Neon</div>
                    </button>
                </div>
            </div>

            <!-- Color Inputs (2-Way Synchronized) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-3 border-t border-slate-800">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Warna Utama (Primary)</label>
                    <div class="flex items-center gap-2">
                        <input type="color" id="input_primary" name="primary_color" value="<?= e($config['primary_color'] ?? '#0284c7') ?>"
                               oninput="syncColor('primary', this.value)"
                               class="w-10 h-10 rounded-xl cursor-pointer bg-transparent border border-slate-600">
                        <input type="text" id="text_primary" value="<?= e($config['primary_color'] ?? '#0284c7') ?>" 
                               oninput="syncColor('primary', this.value)"
                               class="flex-1 px-3 py-2 text-xs rounded-xl bg-slate-800 border border-slate-700 text-white font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Warna Sekunder (Badge & Tombol)</label>
                    <div class="flex items-center gap-2">
                        <input type="color" id="input_secondary" name="secondary_color" value="<?= e($config['secondary_color'] ?? '#38bdf8') ?>"
                               oninput="syncColor('secondary', this.value)"
                               class="w-10 h-10 rounded-xl cursor-pointer bg-transparent border border-slate-600">
                        <input type="text" id="text_secondary" value="<?= e($config['secondary_color'] ?? '#38bdf8') ?>" 
                               oninput="syncColor('secondary', this.value)"
                               class="flex-1 px-3 py-2 text-xs rounded-xl bg-slate-800 border border-slate-700 text-white font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Warna Aksen (Glow Bercahaya)</label>
                    <div class="flex items-center gap-2">
                        <input type="color" id="input_accent" name="accent_color" value="<?= e($config['accent_color'] ?? '#0ea5e9') ?>"
                               oninput="syncColor('accent', this.value)"
                               class="w-10 h-10 rounded-xl cursor-pointer bg-transparent border border-slate-600">
                        <input type="text" id="text_accent" value="<?= e($config['accent_color'] ?? '#0ea5e9') ?>" 
                               oninput="syncColor('accent', this.value)"
                               class="flex-1 px-3 py-2 text-xs rounded-xl bg-slate-800 border border-slate-700 text-white font-mono">
                    </div>
                </div>
            </div>

            <!-- 🌟 LIVE PREVIEW BOX -->
            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                <div class="flex items-center justify-between text-xs text-slate-400">
                    <span class="font-bold uppercase tracking-wider">Pratinjau Langsung Komponen:</span>
                    <span class="text-[10px] text-emerald-400">Otomatis berubah mengikuti pilihan warna</span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div id="prev-card" class="p-3.5 rounded-2xl bg-white/10 border flex items-center space-x-3 transition shadow" style="border-color: <?= e($config['secondary_color'] ?? '#38bdf8') ?>;">
                        <div id="prev-icon" class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold" style="background: rgba(56, 189, 248, 0.2); color: <?= e($config['secondary_color'] ?? '#38bdf8') ?>;">
                            <i class="fa-solid fa-shapes"></i>
                        </div>
                        <div>
                            <div class="font-extrabold text-xs text-white">Contoh Kartu Aplikasi</div>
                            <div class="text-[10px] text-slate-400">Efek hover & border glow</div>
                        </div>
                    </div>

                    <span id="prev-badge" class="px-3 py-1 rounded-full text-xs font-extrabold" style="background: rgba(56, 189, 248, 0.2); color: <?= e($config['secondary_color'] ?? '#38bdf8') ?>; border: 1px solid <?= e($config['secondary_color'] ?? '#38bdf8') ?>;">
                        Badge Contoh
                    </span>

                    <button type="button" id="prev-btn" class="px-4 py-2 rounded-xl text-xs font-black shadow" style="background: <?= e($config['secondary_color'] ?? '#38bdf8') ?>; color: #0f172a;">
                        Tombol Akses
                    </button>
                </div>
            </div>
        </div>

        <!-- CARD 5: Kontak & Footer -->
        <div class="p-6 sm:p-8 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-5">
            <div class="border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-phone text-teal-400"></i>
                    <span>5. Kontak, Medsos & Teks Copyright</span>
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Website Resmi Sekolah</label>
                    <input type="url" name="social_website" value="<?= e($config['social_website'] ?? '') ?>" placeholder="https://..."
                           class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-xs text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">WhatsApp Center Sekolah</label>
                    <input type="url" name="social_whatsapp" value="<?= e($config['social_whatsapp'] ?? '') ?>" placeholder="https://wa.me/..."
                           class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-xs text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Channel YouTube</label>
                    <input type="url" name="social_youtube" value="<?= e($config['social_youtube'] ?? '') ?>" placeholder="https://youtube.com/..."
                           class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-xs text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Akun Instagram</label>
                    <input type="url" name="social_instagram" value="<?= e($config['social_instagram'] ?? '') ?>" placeholder="https://instagram.com/..."
                           class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-xs text-white">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Teks Footer Copyright</label>
                    <input type="text" name="footer_text" value="<?= e($config['footer_text'] ?? '') ?>"
                           class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-xs text-white">
                </div>
            </div>
        </div>

        <!-- Sticky Save Button -->
        <div class="sticky bottom-4 z-20 flex justify-end">
            <button type="submit" 
                    class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white font-black text-sm uppercase tracking-wider shadow-2xl hover:shadow-emerald-500/40 transition-all duration-200 flex items-center gap-3">
                <i class="fa-solid fa-floppy-disk text-base"></i>
                <span>Simpan Semua Perubahan</span>
            </button>
        </div>
    </form>

</div>

<script>
function toggleBgOptions(type) {
    document.getElementById('bg-option-gradient').classList.toggle('hidden', type !== 'gradient');
    document.getElementById('bg-option-image').classList.toggle('hidden', type !== 'image');
    document.getElementById('bg-option-solid').classList.toggle('hidden', type !== 'solid');
}

function selectPresetWallpaper(path) {
    document.getElementById('selected_wallpaper').value = path;
    const imgRadio = document.querySelector('input[name="bg_type"][value="image"]');
    if (imgRadio) {
        imgRadio.checked = true;
        toggleBgOptions('image');
    }
    alert('Wallpaper dipilih: ' + path + '. Klik "Simpan Semua Perubahan" untuk menerapkan!');
}

function setOverlayColor(hex) {
    document.getElementById('input_overlay_color').value = hex;
}

function syncColor(type, val) {
    if (type === 'primary') {
        document.getElementById('input_primary').value = val;
        document.getElementById('text_primary').value = val;
    } else if (type === 'secondary') {
        document.getElementById('input_secondary').value = val;
        document.getElementById('text_secondary').value = val;
        // Update live preview
        document.getElementById('prev-card').style.borderColor = val;
        document.getElementById('prev-icon').style.color = val;
        document.getElementById('prev-badge').style.color = val;
        document.getElementById('prev-badge').style.borderColor = val;
        document.getElementById('prev-btn').style.backgroundColor = val;
    } else if (type === 'accent') {
        document.getElementById('input_accent').value = val;
        document.getElementById('text_accent').value = val;
    }
}

function applyColorPalette(p, s, a) {
    syncColor('primary', p);
    syncColor('secondary', s);
    syncColor('accent', a);
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
