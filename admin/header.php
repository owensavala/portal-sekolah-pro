<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

require_login();

$config = get_config();
$admin = get_admin();
$flash = get_flash_message();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Panel Pengelola') ?> • <?= e($config['school_name'] ?? 'Portal Madrasah') ?></title>
    <link rel="shortcut icon" href="<?= e(asset_url($config['favicon'] ?? 'uploads/favicon.ico', true)) ?>" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            background-color: #0f172a;
            color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .admin-sidebar-link.active {
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.25), rgba(16, 185, 129, 0.05));
            border-left: 4px solid #10b981;
            color: #ffffff;
            font-weight: 700;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row bg-slate-950 text-slate-100">

    <!-- Left Sidebar -->
    <aside class="w-full md:w-64 bg-slate-900/90 border-r border-slate-800 flex flex-col justify-between shrink-0">
        <div>
            <!-- School Brand / Logo -->
            <div class="p-5 border-b border-slate-800/80 flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 p-1 flex items-center justify-center border border-white/20">
                    <img src="<?= e(asset_url($config['logo'] ?? 'uploads/Logo_Instansi.png', true)) ?>" alt="Logo" class="max-h-full max-w-full object-contain">
                </div>
                <div class="overflow-hidden">
                    <h1 class="text-xs font-black tracking-wider text-white uppercase truncate">ADMIN PORTAL</h1>
                    <p class="text-[11px] text-emerald-400 font-medium truncate"><?= e($config['school_name'] ?? 'Madrasah') ?></p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-3 space-y-1 text-sm">
                <div class="px-3 pt-3 pb-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Navigasi Utama</div>
                
                <a href="index.php" class="admin-sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800/60 transition <?= ($currentPage === 'index.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-gauge-high w-5 text-emerald-400 text-center"></i>
                    <span>Ringkasan Dashboard</span>
                </a>

                <a href="settings.php" class="admin-sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800/60 transition <?= ($currentPage === 'settings.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-palette w-5 text-amber-400 text-center"></i>
                    <span>Tampilan, Warna & Logo</span>
                </a>

                <a href="categories.php" class="admin-sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800/60 transition <?= ($currentPage === 'categories.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-folder-tree w-5 text-blue-400 text-center"></i>
                    <span>Kelola Kategori</span>
                </a>

                <a href="shortcuts.php" class="admin-sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800/60 transition <?= ($currentPage === 'shortcuts.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-link w-5 text-purple-400 text-center"></i>
                    <span>Kelola Pintasan / Link</span>
                </a>

                <div class="px-3 pt-4 pb-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Alat & Keamanan</div>

                <a href="backup.php" class="admin-sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800/60 transition <?= ($currentPage === 'backup.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-cloud-arrow-down w-5 text-teal-400 text-center"></i>
                    <span>Backup & Restore</span>
                </a>

                <a href="profile.php" class="admin-sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800/60 transition <?= ($currentPage === 'profile.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-user-shield w-5 text-rose-400 text-center"></i>
                    <span>Ubah Akun Admin</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer Action -->
        <div class="p-3 border-t border-slate-800 space-y-2">
            <a href="../index.php" target="_blank" class="w-full flex items-center justify-center space-x-2 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition border border-slate-700">
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-emerald-400"></i>
                <span>Lihat Web Portal</span>
            </a>
            <a href="logout.php" onclick="return confirm('Keluar dari panel pengelola?');" class="w-full flex items-center justify-center space-x-2 px-3 py-2 rounded-xl bg-red-950/40 hover:bg-red-900/60 text-red-300 text-xs font-semibold transition border border-red-800/40">
                <i class="fa-solid fa-power-off text-xs"></i>
                <span>Keluar (Logout)</span>
            </a>
        </div>
    </aside>

    <!-- Right Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top Header Bar -->
        <header class="h-16 bg-slate-900/60 border-b border-slate-800/80 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30 backdrop-blur">
            <div class="flex items-center space-x-3">
                <h2 class="text-base sm:text-lg font-bold text-white"><?= e($pageTitle ?? 'Dashboard') ?></h2>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-right hidden sm:block">
                    <div class="text-xs font-bold text-slate-200"><?= e($_SESSION['admin_name'] ?? 'Administrator') ?></div>
                    <div class="text-[10px] text-emerald-400 font-medium">Superadmin</div>
                </div>
                <div class="w-9 h-9 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 flex items-center justify-center font-black text-sm">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="p-4 sm:p-8 flex-1">
            <!-- Flash Message -->
            <?php if ($flash): ?>
            <div class="mb-6 p-4 rounded-2xl flex items-center justify-between text-sm shadow-lg <?= $flash['type'] === 'success' ? 'bg-emerald-900/50 border border-emerald-500/50 text-emerald-200' : 'bg-red-900/50 border border-red-500/50 text-red-200' ?>">
                <div class="flex items-center space-x-3">
                    <i class="<?= $flash['type'] === 'success' ? 'fa-solid fa-circle-check text-emerald-400' : 'fa-solid fa-circle-exclamation text-red-400' ?> text-lg"></i>
                    <span><?= e($flash['message']) ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <?php endif; ?>
