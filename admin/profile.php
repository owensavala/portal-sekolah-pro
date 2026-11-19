<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

require_login();

$csrf_token = generate_csrf_token();
$admin = get_admin();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Validasi keamanan CSRF gagal.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPassword, $admin['password_hash'])) {
            $error = 'Password saat ini (lama) tidak sesuai!';
        } elseif (empty($username)) {
            $error = 'Username tidak boleh kosong!';
        } else {
            $admin['name'] = $name;
            $admin['username'] = $username;

            if (!empty($newPassword)) {
                if (strlen($newPassword) < 6) {
                    $error = 'Password baru minimal 6 karakter!';
                } elseif ($newPassword !== $confirmPassword) {
                    $error = 'Konfirmasi password baru tidak cocok!';
                } else {
                    $admin['password_hash'] = password_hash($newPassword, PASSWORD_BCRYPT);
                }
            }

            if (empty($error)) {
                save_admin($admin);
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['name'];
                set_flash_message('success', 'Akun admin berhasil diperbarui!');
                header('Location: profile.php');
                exit;
            }
        }
    }
}

$pageTitle = 'Ubah Akun Admin';
require_once __DIR__ . '/header.php';
?>

<div class="max-w-xl mx-auto space-y-6">

    <?php if (!empty($error)): ?>
    <div class="p-4 rounded-2xl bg-red-900/50 border border-red-500/50 text-red-200 text-xs flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation text-base"></i>
        <span><?= e($error) ?></span>
    </div>
    <?php endif; ?>

    <div class="p-6 sm:p-8 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-5">
        <div class="border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-shield text-rose-400"></i>
                <span>Pengaturan Akun & Keamanan</span>
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Perbarui nama pengguna dan kata sandi login administrator</p>
        </div>

        <form method="POST" action="profile.php" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Tampilan</label>
                <input type="text" name="name" value="<?= e($admin['name'] ?? 'Administrator') ?>" required
                       class="w-full px-3 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Username Login</label>
                <input type="text" name="username" value="<?= e($admin['username'] ?? 'admin') ?>" required
                       class="w-full px-3 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="pt-3 border-t border-slate-800">
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Password Saat Ini (Wajib diisi untuk konfirmasi)</label>
                <input type="password" name="current_password" required placeholder="Masukkan password saat ini"
                       class="w-full px-3 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Password Baru (Opsional)</label>
                    <input type="password" name="new_password" placeholder="Kosongkan jika tidak diganti"
                           class="w-full px-3 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" placeholder="Ulangi password baru"
                           class="w-full px-3 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider transition shadow">
                    Simpan Perubahan Akun
                </button>
            </div>
        </form>
    </div>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>
