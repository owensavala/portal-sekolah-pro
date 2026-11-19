<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

require_login();

$csrf_token = generate_csrf_token();
$categories = get_categories();
$shortcuts = get_shortcuts();

$catMap = [];
foreach ($categories as $c) {
    $catMap[$c['id']] = $c['name'];
}

// Filter by category
$filterCategory = $_GET['cat'] ?? '';
$searchKeyword = strtolower(trim($_GET['q'] ?? ''));

$displayShortcuts = $shortcuts;
if (!empty($filterCategory)) {
    $displayShortcuts = array_filter($displayShortcuts, fn($s) => ($s['category_id'] ?? '') === $filterCategory);
}
if (!empty($searchKeyword)) {
    $displayShortcuts = array_filter($displayShortcuts, function($s) use ($searchKeyword) {
        $title = strtolower($s['title'] ?? '');
        $desc = strtolower($s['description'] ?? '');
        $url = strtolower($s['url'] ?? '');
        return str_contains($title, $searchKeyword) || str_contains($desc, $searchKeyword) || str_contains($url, $searchKeyword);
    });
}

// Handle Form Submit (Add / Edit / Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('danger', 'Validasi keamanan CSRF gagal.');
        header('Location: shortcuts.php');
        exit;
    }

    $formAction = $_POST['form_action'] ?? '';

    if ($formAction === 'save') {
        $scId = trim($_POST['shortcut_id'] ?? '');
        $isNew = empty($scId);

        if ($isNew) {
            $scId = 'sc_' . uniqid();
        }

        $title = trim($_POST['title'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $category_id = trim($_POST['category_id'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $badge = trim($_POST['badge'] ?? '');
        $fa_icon = trim($_POST['fa_icon'] ?? 'fa-solid fa-link');
        $order = intval($_POST['order'] ?? 1);
        $is_pinned = isset($_POST['is_pinned']);
        $is_active = isset($_POST['is_active']);
        $target = $_POST['target'] ?? '_blank';

        $scData = [
            'id' => $scId,
            'category_id' => $category_id,
            'title' => $title,
            'url' => $url,
            'description' => $description,
            'badge' => $badge,
            'fa_icon' => $fa_icon,
            'clicks' => intval($_POST['clicks'] ?? 0),
            'is_pinned' => $is_pinned,
            'is_active' => $is_active,
            'target' => $target,
            'order' => $order
        ];

        if ($isNew) {
            $shortcuts[] = $scData;
            set_flash_message('success', "Pintasan '{$title}' berhasil ditambahkan!");
        } else {
            foreach ($shortcuts as &$s) {
                if ($s['id'] === $scId) {
                    $scData['clicks'] = $s['clicks'] ?? 0;
                    $s = $scData;
                    break;
                }
            }
            set_flash_message('success', "Pintasan '{$title}' berhasil diperbarui!");
        }

        save_shortcuts($shortcuts);
        header('Location: shortcuts.php' . (!empty($filterCategory) ? "?cat=" . urlencode($filterCategory) : ''));
        exit;

    } elseif ($formAction === 'delete') {
        $delId = $_POST['delete_id'] ?? '';
        $filtered = [];
        $deletedTitle = '';

        foreach ($shortcuts as $s) {
            if ($s['id'] === $delId) {
                $deletedTitle = $s['title'];
            } else {
                $filtered[] = $s;
            }
        }

        save_shortcuts($filtered);
        set_flash_message('success', "Pintasan '{$deletedTitle}' berhasil dihapus.");
        header('Location: shortcuts.php');
        exit;
    }
}

$pageTitle = 'Kelola Pintasan / Link Aplikasi';
require_once __DIR__ . '/header.php';
?>

<div class="space-y-6">

    <!-- Top Action & Filter Bar -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-link text-purple-400"></i>
                <span>Kelola Pintasan / Tautan Aplikasi</span>
            </h3>
            <p class="text-xs text-slate-400">Total <?= count($shortcuts) ?> tautan terdaftar (Menampilkan <?= count($displayShortcuts) ?>)</p>
        </div>
        <button type="button" onclick="openAddShortcutModal()" 
                class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider transition shadow flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Pintasan Baru</span>
        </button>
    </div>

    <!-- Filter & Search Controls -->
    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 shadow">
        <!-- Filter Category -->
        <div class="w-full sm:w-auto flex items-center gap-2">
            <span class="text-xs text-slate-400 font-semibold whitespace-nowrap">Filter Kategori:</span>
            <select onchange="location.href='shortcuts.php?cat=' + encodeURIComponent(this.value)" 
                    class="px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-xs text-white focus:outline-none focus:ring-1 focus:ring-emerald-400">
                <option value="">Semua Kategori (<?= count($shortcuts) ?>)</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat['id']) ?>" <?= ($filterCategory === $cat['id']) ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Search Input -->
        <form method="GET" action="shortcuts.php" class="w-full sm:w-72 relative">
            <?php if (!empty($filterCategory)): ?>
            <input type="hidden" name="cat" value="<?= e($filterCategory) ?>">
            <?php endif; ?>
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-xs text-slate-400"></i>
            <input type="text" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Cari nama atau URL..."
                   class="w-full pl-8 pr-3 py-1.5 rounded-xl bg-slate-800 border border-slate-700 text-xs text-white placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-emerald-400">
        </form>
    </div>

    <!-- Shortcuts Table -->
    <div class="rounded-3xl bg-slate-900 border border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-800/80 uppercase font-black tracking-wider text-[11px] text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-12">#</th>
                        <th class="py-3.5 px-4">Nama Pintasan & URL</th>
                        <th class="py-3.5 px-4 hidden md:table-cell">Kategori</th>
                        <th class="py-3.5 px-4 text-center">Akses Cepat</th>
                        <th class="py-3.5 px-4 text-center">Klik</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($displayShortcuts)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-10 text-slate-500">
                            Tidak ada pintasan yang cocok dengan kriteria pencarian.
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php foreach ($displayShortcuts as $idx => $sc): ?>
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3.5 px-4 text-center font-mono text-slate-400">
                            <?= $idx + 1 ?>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center text-xs text-emerald-400 shrink-0 mt-0.5">
                                    <i class="<?= e($sc['fa_icon'] ?? 'fa-solid fa-link') ?>"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-sm text-white flex items-center gap-2 flex-wrap">
                                        <span><?= e($sc['title']) ?></span>
                                        <?php if (!empty($sc['badge'])): ?>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 border border-slate-700 text-slate-300">
                                            <?= e($sc['badge']) ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-[11px] text-slate-400 truncate max-w-sm"><?= e($sc['description'] ?? '') ?></p>
                                    <a href="<?= e($sc['url']) ?>" target="_blank" class="text-[11px] text-emerald-400 hover:underline flex items-center gap-1 font-mono mt-0.5">
                                        <span><?= e($sc['url']) ?></span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 hidden md:table-cell">
                            <span class="inline-block px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700 text-slate-300 font-semibold text-xs">
                                <?= e($catMap[$sc['category_id'] ?? ''] ?? 'Umum') ?>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <?php if (!empty($sc['is_pinned'])): ?>
                                <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 text-[10px] font-bold">
                                    <i class="fa-solid fa-bolt mr-0.5"></i> Pin
                                </span>
                            <?php else: ?>
                                <span class="text-slate-600">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 text-center font-bold text-white">
                            <?= number_format($sc['clicks'] ?? 0) ?>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <?php if (!empty($sc['is_active'])): ?>
                                <span class="px-2 py-0.5 rounded bg-emerald-950 text-emerald-400 border border-emerald-800 text-[10px] font-bold">AKTIF</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700 text-[10px] font-bold">NONAKTIF</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <button type="button" onclick='editShortcut(<?= json_encode($sc) ?>)'
                                        class="px-2.5 py-1.5 rounded-lg bg-amber-500/20 text-amber-300 hover:bg-amber-500 hover:text-slate-950 border border-amber-400/30 transition text-xs font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>Edit</span>
                                </button>
                                <form method="POST" action="shortcuts.php" onsubmit="return confirm('Hapus tautan <?= addslashes($sc['title']) ?>?');">
                                    <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
                                    <input type="hidden" name="form_action" value="delete">
                                    <input type="hidden" name="delete_id" value="<?= e($sc['id']) ?>">
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-red-500/20 text-red-300 hover:bg-red-500 hover:text-white border border-red-400/30 transition text-xs font-semibold">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Tambah / Edit Pintasan -->
<div id="shortcut-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/75 backdrop-blur-sm" onclick="closeShortcutModal()"></div>
    <div class="relative w-full max-w-lg rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl p-6 sm:p-8 z-10 space-y-4 modal-enter">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 id="sc-modal-title" class="text-base font-bold text-white">Tambah Pintasan Baru</h3>
            <button type="button" onclick="closeShortcutModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form method="POST" action="shortcuts.php" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
            <input type="hidden" name="form_action" value="save">
            <input type="hidden" id="sc_form_id" name="shortcut_id" value="">
            <input type="hidden" id="sc_form_clicks" name="clicks" value="0">

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Aplikasi / Link</label>
                <input type="text" id="sc_form_title" name="title" required placeholder="Contoh: EMIS 4.0 MADRASAH"
                       class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">URL Tujuan</label>
                <input type="url" id="sc_form_url" name="url" required placeholder="https://emis.kemenag.go.id/"
                       class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Pilih Kategori</label>
                    <select id="sc_form_cat" name="category_id" required
                            class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= e($cat['id']) ?>"><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Badge Penanda</label>
                    <input type="text" id="sc_form_badge" name="badge" placeholder="Contoh: Kemenag / Wajib / Guru"
                           class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Keterangan / Fungsi Singkat</label>
                <input type="text" id="sc_form_desc" name="description" placeholder="Penjelasan singkat tujuan sistem ini..."
                       class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs">
            </div>

            <!-- Visual Icon Picker Field -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Pilih Ikon Tautan</label>
                <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-800 border border-slate-700">
                    <div class="w-12 h-12 rounded-xl bg-slate-900 border border-slate-700 flex items-center justify-center text-xl text-emerald-400 shrink-0 shadow-inner">
                        <i id="sc_form_icon_preview" class="fa-solid fa-link"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <input type="hidden" id="sc_form_icon" name="fa_icon" value="fa-solid fa-link">
                        <div class="text-xs font-bold text-white flex items-center gap-1.5 truncate">
                            <span id="sc_form_icon_name">Tautan / Link URL</span>
                        </div>
                        <span id="sc_form_icon_class_display" class="font-mono text-[11px] text-emerald-400 block truncate">fa-solid fa-link</span>
                    </div>
                    <button type="button" onclick="openIconPicker('sc_form_icon', 'sc_form_icon_preview')" 
                            class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider shrink-0 transition flex items-center gap-1.5 shadow">
                        <i class="fa-solid fa-icons"></i>
                        <span>Pilih Ikon</span>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nomor Urutan</label>
                <input type="number" id="sc_form_order" name="order" value="1" min="1" max="99"
                       class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs">
            </div>

            <div class="flex items-center gap-6 pt-2 border-t border-slate-800">
                <label class="flex items-center space-x-2 text-xs font-bold text-amber-300 cursor-pointer">
                    <input type="checkbox" id="sc_form_pinned" name="is_pinned"
                           class="w-4 h-4 rounded text-amber-500 bg-slate-900 border-slate-700 focus:ring-amber-400">
                    <span>Pin ke Akses Cepat (Favorit)</span>
                </label>

                <label class="flex items-center space-x-2 text-xs font-bold text-white cursor-pointer">
                    <input type="checkbox" id="sc_form_active" name="is_active" checked
                           class="w-4 h-4 rounded text-emerald-500 bg-slate-900 border-slate-700 focus:ring-emerald-400">
                    <span>Status Aktif</span>
                </label>
            </div>

            <div class="pt-3 border-t border-slate-800 flex justify-end space-x-2">
                <button type="button" onclick="closeShortcutModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-bold hover:bg-slate-700">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold">
                    Simpan Pintasan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateShortcutIconPreview(iconClass) {
    iconClass = iconClass || 'fa-solid fa-link';
    const input = document.getElementById('sc_form_icon');
    const preview = document.getElementById('sc_form_icon_preview');
    const classDisplay = document.getElementById('sc_form_icon_class_display');
    const nameDisplay = document.getElementById('sc_form_icon_name');
    
    if (input) input.value = iconClass;
    if (preview) preview.className = iconClass;
    if (classDisplay) classDisplay.innerText = iconClass;
    
    if (nameDisplay && typeof ICON_CATALOG !== 'undefined') {
        const found = ICON_CATALOG.find(i => i.class === iconClass);
        nameDisplay.innerText = found ? found.name : 'Ikon Kustom';
    }
}

// Listen to input changes from picker
document.addEventListener('DOMContentLoaded', function() {
    const iconInput = document.getElementById('sc_form_icon');
    if (iconInput) {
        iconInput.addEventListener('change', function() {
            updateShortcutIconPreview(this.value);
        });
        iconInput.addEventListener('input', function() {
            updateShortcutIconPreview(this.value);
        });
    }
});

function openAddShortcutModal() {
    document.getElementById('sc-modal-title').innerText = 'Tambah Pintasan Baru';
    document.getElementById('sc_form_id').value = '';
    document.getElementById('sc_form_title').value = '';
    document.getElementById('sc_form_url').value = '';
    document.getElementById('sc_form_desc').value = '';
    document.getElementById('sc_form_badge').value = '';
    updateShortcutIconPreview('fa-solid fa-link');
    document.getElementById('sc_form_order').value = 1;
    document.getElementById('sc_form_pinned').checked = false;
    document.getElementById('sc_form_active').checked = true;
    document.getElementById('sc_form_clicks').value = '0';
    document.getElementById('shortcut-modal').classList.remove('hidden');
}

function editShortcut(sc) {
    document.getElementById('sc-modal-title').innerText = 'Edit Pintasan: ' + sc.title;
    document.getElementById('sc_form_id').value = sc.id;
    document.getElementById('sc_form_title').value = sc.title;
    document.getElementById('sc_form_url').value = sc.url;
    document.getElementById('sc_form_cat').value = sc.category_id || '';
    document.getElementById('sc_form_desc').value = sc.description || '';
    document.getElementById('sc_form_badge').value = sc.badge || '';
    updateShortcutIconPreview(sc.fa_icon || 'fa-solid fa-link');
    document.getElementById('sc_form_order').value = sc.order || 1;
    document.getElementById('sc_form_pinned').checked = !!sc.is_pinned;
    document.getElementById('sc_form_active').checked = !!sc.is_active;
    document.getElementById('sc_form_clicks').value = sc.clicks || 0;
    document.getElementById('shortcut-modal').classList.remove('hidden');
}

function closeShortcutModal() {
    document.getElementById('shortcut-modal').classList.add('hidden');
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
