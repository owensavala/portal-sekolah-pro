<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

require_login();

$csrf_token = generate_csrf_token();

// Export Handler
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    $backupData = [
        'app' => 'Portal Madrasah Pro',
        'version' => '2.0.0',
        'export_time' => date('Y-m-d H:i:s'),
        'config' => get_config(),
        'categories' => get_categories(),
        'shortcuts' => get_shortcuts(),
        'sliders' => get_sliders()
    ];

    $filename = 'backup_portal_' . date('Y-m-d_His') . '.json';
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Import Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'import') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('danger', 'CSRF verification failed.');
        header('Location: backup.php');
        exit;
    }

    if (empty($_FILES['backup_file']['tmp_name'])) {
        set_flash_message('danger', 'Pilih file JSON backup terlebih dahulu.');
        header('Location: backup.php');
        exit;
    }

    $content = file_get_contents($_FILES['backup_file']['tmp_name']);
    $data = json_decode($content, true);

    if (!$data || !is_array($data) || empty($data['app'])) {
        set_flash_message('danger', 'Format file backup tidak valid atau rusak.');
        header('Location: backup.php');
        exit;
    }

    if (!empty($data['config'])) save_config($data['config']);
    if (!empty($data['categories'])) save_categories($data['categories']);
    if (!empty($data['shortcuts'])) save_shortcuts($data['shortcuts']);
    if (!empty($data['sliders'])) save_sliders($data['sliders']);

    set_flash_message('success', 'Data portal berhasil dipulihkan dari file backup!');
    header('Location: backup.php');
    exit;
}

$pageTitle = 'Backup & Restore Data';
require_once __DIR__ . '/header.php';
?>

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Export Box -->
    <div class="p-6 sm:p-8 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-down text-emerald-400"></i>
                    <span>Ekspor Cadangan Data (Backup)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                    Unduh seluruh data pengaturan identitas, warna tema, kategori, dan seluruh link pintasan ke dalam 1 file cadangan format JSON.
                </p>
            </div>
        </div>

        <div class="pt-2">
            <a href="backup.php?action=export" 
               class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider transition shadow">
                <i class="fa-solid fa-download"></i>
                <span>Unduh File Backup Sekarang (.JSON)</span>
            </a>
        </div>
    </div>

    <!-- Import Box -->
    <div class="p-6 sm:p-8 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
        <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-up text-amber-400"></i>
                <span>Pulihkan Data (Restore)</span>
            </h3>
            <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                Unggah file cadangan JSON yang sebelumnya pernah Anda unduh untuk mengembalikan seluruh pengaturan dan link.
            </p>
        </div>

        <form method="POST" action="backup.php" enctype="multipart/form-data" onsubmit="return confirm('Peringatan: Seluruh data saat ini akan ditimpa dengan data dari file backup. Lanjutkan?');" class="space-y-4 pt-2">
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
            <input type="hidden" name="action" value="import">

            <div>
                <input type="file" name="backup_file" accept=".json" required
                       class="block w-full text-xs text-slate-300 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer">
            </div>

            <button type="submit" 
                    class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider transition shadow">
                <i class="fa-solid fa-upload mr-1.5"></i>
                <span>Pulihkan dari File Backup</span>
            </button>
        </form>
    </div>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>
