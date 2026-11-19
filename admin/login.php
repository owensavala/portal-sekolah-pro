<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

$config = get_config();
$error = '';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $error = 'Validasi keamanan (CSRF) gagal. Silakan muat ulang halaman.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $loginResult = login_user($username, $password);
        if ($loginResult === true || (is_array($loginResult) && !empty($loginResult['success']))) {
            header('Location: index.php');
            exit;
        } else {
            $error = is_array($loginResult) ? ($loginResult['error'] ?? 'Username atau password salah!') : 'Username atau password salah!';
        }
    }
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pengelola • <?= e($config['school_name'] ?? 'Portal Madrasah') ?></title>
    <link rel="shortcut icon" href="../<?= e($config['favicon'] ?? 'uploads/favicon.ico') ?>" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        <?= generate_theme_variables($config) ?>
        body {
            <?= generate_background_css($config) ?>
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Return to Public Portal -->
        <div class="mb-4 text-center">
            <a href="../index.php" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-300 hover:text-white bg-black/30 hover:bg-black/50 px-4 py-2 rounded-full border border-white/10 backdrop-blur transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Web Portal</span>
            </a>
        </div>

        <!-- Login Card -->
        <div class="glass-panel rounded-3xl p-6 sm:p-8 border border-white/20 shadow-2xl relative overflow-hidden">
            <!-- Decorative Glow -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-amber-400/20 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Header -->
            <div class="text-center mb-6">
                <div class="w-20 h-20 mx-auto rounded-2xl p-2 bg-white/15 border border-white/30 backdrop-blur flex items-center justify-center mb-3 shadow-lg">
                    <img src="../<?= e($config['logo'] ?? 'uploads/Logo_Instansi.png') ?>" alt="Logo" class="max-h-full max-w-full object-contain">
                </div>
                <h1 class="text-xl font-black text-white uppercase tracking-wider">PANEL ADMINISTRATOR</h1>
                <p class="text-xs text-emerald-300 mt-1"><?= e($config['school_name'] ?? 'MIS Integral Hidayatullah') ?></p>
            </div>

            <!-- Alert Error -->
            <?php if (!empty($error)): ?>
            <div class="mb-5 p-3 rounded-xl bg-red-500/20 border border-red-500/40 text-red-200 text-xs flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-base text-red-400 shrink-0"></i>
                <span><?= e($error) ?></span>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="login.php" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">

                <div>
                    <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider mb-1.5">Username</label>
                    <div class="relative flex items-center">
                        <i class="fa-solid fa-user absolute left-3.5 text-xs text-slate-400"></i>
                        <input type="text" name="username" required autofocus placeholder="Masukkan username admin"
                               class="w-full pl-10 pr-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider mb-1.5">Password</label>
                    <div class="relative flex items-center">
                        <i class="fa-solid fa-lock absolute left-3.5 text-xs text-slate-400"></i>
                        <input type="password" name="password" required placeholder="Masukkan password"
                               class="w-full pl-10 pr-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 transition">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-sm uppercase tracking-wider shadow-lg hover:shadow-amber-500/30 transition-all duration-200 flex items-center justify-center gap-2">
                        <span>Masuk ke Dashboard</span>
                        <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-white/10 text-center text-[11px] text-slate-400">
                <p>Default Login: Username <b>admin</b> & Password <b>admin123</b></p>
                <p class="mt-0.5 text-slate-500">(Dapat diganti kapan saja melalui menu Pengaturan Profil)</p>
            </div>
        </div>
    </div>
</body>
</html>
