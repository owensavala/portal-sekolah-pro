<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

require_login();

$csrf_token = generate_csrf_token();
$categories = get_categories();
$shortcuts = get_shortcuts();

// Count shortcuts per category
$counts = [];
foreach ($shortcuts as $sc) {
    $cid = $sc['category_id'] ?? '';
    $counts[$cid] = ($counts[$cid] ?? 0) + 1;
}

$action = $_GET['action'] ?? 'list';
$editId = $_GET['id'] ?? '';
$editCategory = null;

if ($action === 'edit' && !empty($editId)) {
    foreach ($categories as $cat) {
        if ($cat['id'] === $editId) {
            $editCategory = $cat;
            break;
        }
    }
}

// Handle Form Submit (Add / Edit / Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('danger', 'CSRF verification failed.');
        header('Location: categories.php');
        exit;
    }

    $formAction = $_POST['form_action'] ?? '';

    if ($formAction === 'save') {
        $catId = trim($_POST['category_id'] ?? '');
        $isNew = empty($catId);

        if ($isNew) {
            $catId = 'cat_' . uniqid();
        }

        $name = trim($_POST['name'] ?? '');
        $subname = trim($_POST['subname'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $badge = trim($_POST['badge'] ?? '');
        $fa_icon = trim($_POST['fa_icon'] ?? 'fa-folder');
        $accent_color = trim($_POST['accent_color'] ?? '#10b981');
        $order = intval($_POST['order'] ?? 1);
        $is_active = isset($_POST['is_active']);

        // Check icon image upload
        $icon_image = $_POST['current_icon_image'] ?? '';
        $icon_type = !empty($icon_image) ? 'image' : 'fa';

        if (!empty($_FILES['icon_file']['name'])) {
            $up = handle_file_upload($_FILES['icon_file'], ['png', 'jpg', 'jpeg', 'svg', 'webp']);
            if ($up['success']) {
                $icon_image = $up['path'];
                $icon_type = 'image';
            }
        }

        $newCatData = [
            'id' => $catId,
            'name' => strtoupper($name),
            'subname' => $subname,
            'description' => $description,
            'badge' => $badge,
            'icon_type' => $icon_type,
            'icon_image' => $icon_image,
            'fa_icon' => $fa_icon,
            'accent_color' => $accent_color,
            'order' => $order,
            'is_active' => $is_active
        ];

        if ($isNew) {
            $categories[] = $newCatData;
            set_flash_message('success', "Kategori '{$name}' berhasil ditambahkan!");
        } else {
            foreach ($categories as &$c) {
                if ($c['id'] === $catId) {
                    $c = $newCatData;
                    break;
                }
            }
            set_flash_message('success', "Kategori '{$name}' berhasil diperbarui!");
        }

        save_categories($categories);
        header('Location: categories.php');
        exit;

    } elseif ($formAction === 'delete') {
        $delId = $_POST['delete_id'] ?? '';
        $filtered = [];
        $deletedName = '';

        foreach ($categories as $c) {
            if ($c['id'] === $delId) {
                $deletedName = $c['name'];
            } else {
                $filtered[] = $c;
            }
        }

        save_categories($filtered);
        set_flash_message('success', "Kategori '{$deletedName}' berhasil dihapus.");
        header('Location: categories.php');
        exit;
    }
}

$pageTitle = 'Kelola Kategori Portal';
require_once __DIR__ . '/header.php';
?>

<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-folder-tree text-emerald-400"></i>
                <span>Manajemen Kategori Kartu Portal</span>
            </h3>
            <p class="text-xs text-slate-400">Kategori ini ditampilkan sebagai kartu menu utama di halaman depan portal</p>
        </div>
        <button type="button" onclick="openAddModal()" 
                class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider transition shadow flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Kategori Baru</span>
        </button>
    </div>

    <!-- Category List Table -->
    <div class="rounded-3xl bg-slate-900 border border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-800/80 uppercase font-black tracking-wider text-[11px] text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-14">Urutan</th>
                        <th class="py-3.5 px-4">Ikon & Kategori</th>
                        <th class="py-3.5 px-4 hidden sm:table-cell">Subjudul / Deskripsi</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Link</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-8 text-slate-500">Belum ada kategori. Klik "Tambah Kategori Baru".</td>
                    </tr>
                    <?php endif; ?>

                    <?php foreach ($categories as $cat): ?>
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-4 px-4 text-center font-bold text-white">
                            <span class="w-7 h-7 rounded-lg bg-slate-800 inline-flex items-center justify-center border border-slate-700">
                                <?= $cat['order'] ?? 1 ?>
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 p-1 flex items-center justify-center shrink-0">
                                    <?php if (($cat['icon_type'] ?? '') === 'image' && !empty($cat['icon_image'])): ?>
                                        <img src="../<?= e($cat['icon_image']) ?>" alt="Icon" class="max-h-full max-w-full object-contain">
                                    <?php else: ?>
                                        <i class="fa-solid <?= e($cat['fa_icon'] ?? 'fa-folder') ?> text-amber-400 text-lg"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="font-extrabold text-sm text-white flex items-center gap-2">
                                        <span><?= e($cat['name']) ?></span>
                                        <?php if (!empty($cat['badge'])): ?>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-amber-400 border border-amber-400/30">
                                            <?= e($cat['badge']) ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400"><?= e($cat['id']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 hidden sm:table-cell text-slate-300">
                            <p class="font-medium"><?= e($cat['subname'] ?? '-') ?></p>
                            <p class="text-[11px] text-slate-500 truncate max-w-xs"><?= e($cat['description'] ?? '') ?></p>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold text-xs">
                                <?= $counts[$cat['id']] ?? 0 ?> Link
                            </span>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <?php if (!empty($cat['is_active'])): ?>
                                <span class="px-2 py-0.5 rounded-md bg-emerald-950 text-emerald-400 border border-emerald-800 text-[10px] font-bold">AKTIF</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 rounded-md bg-slate-800 text-slate-400 border border-slate-700 text-[10px] font-bold">NONAKTIF</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <button type="button" onclick='editCategory(<?= json_encode($cat) ?>)'
                                        class="px-2.5 py-1.5 rounded-lg bg-amber-500/20 text-amber-300 hover:bg-amber-500 hover:text-slate-950 border border-amber-400/30 transition text-xs font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>Edit</span>
                                </button>
                                <form method="POST" action="categories.php" onsubmit="return confirm('Hapus kategori <?= addslashes($cat['name']) ?>? Link di dalamnya tidak akan terhapus.');">
                                    <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
                                    <input type="hidden" name="form_action" value="delete">
                                    <input type="hidden" name="delete_id" value="<?= e($cat['id']) ?>">
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

<!-- Modal Form Tambah / Edit Kategori -->
<div id="category-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/75 backdrop-blur-sm" onclick="closeCategoryModal()"></div>
    <div class="relative w-full max-w-lg rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl p-6 sm:p-8 z-10 space-y-5 modal-enter">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 id="modal-form-title" class="text-base font-bold text-white">Tambah Kategori Baru</h3>
            <button type="button" onclick="closeCategoryModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Template Kategori Instan (1-Click Presets) -->
        <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-400 flex items-center gap-1.5 uppercase tracking-wider">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>Pilih Dari Template Siap Pakai (1-Klik):</span>
                </span>
                <button type="button" onclick="clearCategoryForm()" class="text-[10px] text-slate-400 hover:text-white underline">
                    Reset / Kustom Sendiri
                </button>
            </div>
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs scrollbar-thin">
                <button type="button" onclick="applyCategoryTemplate(0)" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-emerald-600/30 hover:border-emerald-500/50 border border-slate-700 text-slate-200 text-[11px] font-semibold whitespace-nowrap transition flex items-center gap-1.5">
                    <i class="fa-solid fa-mosque text-emerald-400"></i> Kemenag
                </button>
                <button type="button" onclick="applyCategoryTemplate(1)" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-emerald-600/30 hover:border-emerald-500/50 border border-slate-700 text-slate-200 text-[11px] font-semibold whitespace-nowrap transition flex items-center gap-1.5">
                    <i class="fa-solid fa-building-columns text-blue-400"></i> Kemdikbud
                </button>
                <button type="button" onclick="applyCategoryTemplate(2)" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-emerald-600/30 hover:border-emerald-500/50 border border-slate-700 text-slate-200 text-[11px] font-semibold whitespace-nowrap transition flex items-center gap-1.5">
                    <i class="fa-solid fa-laptop-code text-cyan-400"></i> Asesmen CBT
                </button>
                <button type="button" onclick="applyCategoryTemplate(3)" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-emerald-600/30 hover:border-emerald-500/50 border border-slate-700 text-slate-200 text-[11px] font-semibold whitespace-nowrap transition flex items-center gap-1.5">
                    <i class="fa-solid fa-graduation-cap text-amber-400"></i> Akademik Rapor
                </button>
                <button type="button" onclick="applyCategoryTemplate(4)" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-emerald-600/30 hover:border-emerald-500/50 border border-slate-700 text-slate-200 text-[11px] font-semibold whitespace-nowrap transition flex items-center gap-1.5">
                    <i class="fa-solid fa-wallet text-yellow-400"></i> Keuangan SPP
                </button>
                <button type="button" onclick="applyCategoryTemplate(5)" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-emerald-600/30 hover:border-emerald-500/50 border border-slate-700 text-slate-200 text-[11px] font-semibold whitespace-nowrap transition flex items-center gap-1.5">
                    <i class="fa-solid fa-users text-purple-400"></i> Kesiswaan Ekskul
                </button>
                <button type="button" onclick="applyCategoryTemplate(6)" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-emerald-600/30 hover:border-emerald-500/50 border border-slate-700 text-slate-200 text-[11px] font-semibold whitespace-nowrap transition flex items-center gap-1.5">
                    <i class="fa-solid fa-book-open text-rose-400"></i> Perpustakaan
                </button>
                <button type="button" onclick="applyCategoryTemplate(7)" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-emerald-600/30 hover:border-emerald-500/50 border border-slate-700 text-slate-200 text-[11px] font-semibold whitespace-nowrap transition flex items-center gap-1.5">
                    <i class="fa-solid fa-bullhorn text-teal-400"></i> PTSP Humas
                </button>
                <button type="button" onclick="applyCategoryTemplate(8)" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-emerald-600/30 hover:border-emerald-500/50 border border-slate-700 text-slate-200 text-[11px] font-semibold whitespace-nowrap transition flex items-center gap-1.5">
                    <i class="fa-solid fa-star-and-crescent text-emerald-300"></i> Pesantren
                </button>
                <button type="button" onclick="applyCategoryTemplate(9)" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-emerald-600/30 hover:border-emerald-500/50 border border-slate-700 text-slate-200 text-[11px] font-semibold whitespace-nowrap transition flex items-center gap-1.5">
                    <i class="fa-solid fa-heart text-red-400"></i> BK & UKS
                </button>
            </div>
        </div>

        <form method="POST" action="categories.php" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
            <input type="hidden" name="form_action" value="save">
            <input type="hidden" id="form_category_id" name="category_id" value="">
            <input type="hidden" id="form_current_icon_image" name="current_icon_image" value="">

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Kategori (Judul Kartu)</label>
                <input type="text" id="form_name" name="name" required placeholder="Contoh: APLIKASI KEMENAG"
                       class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 uppercase">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Subjudul Ringkas</label>
                    <input type="text" id="form_subname" name="subname" placeholder="Contoh: Pendataan Nasional"
                           class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Badge Label</label>
                    <input type="text" id="form_badge" name="badge" placeholder="Contoh: Resmi / Wajib"
                           class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Deskripsi Tambahan</label>
                <input type="text" id="form_description" name="description" placeholder="Penjelasan singkat fungsi menu ini..."
                       class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="space-y-3 pt-2">
                <label class="block text-xs font-bold text-slate-300 uppercase">Ikon Kategori</label>
                
                <!-- Visual FA Icon Picker -->
                <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-800 border border-slate-700">
                    <div class="w-12 h-12 rounded-xl bg-slate-900 border border-slate-700 flex items-center justify-center text-xl text-emerald-400 shrink-0 shadow-inner">
                        <i id="form_fa_icon_preview" class="fa-solid fa-folder"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <input type="hidden" id="form_fa_icon" name="fa_icon" value="fa-solid fa-folder">
                        <div class="text-xs font-bold text-white flex items-center gap-1.5 truncate">
                            <span id="form_fa_icon_name">Folder Arsip</span>
                        </div>
                        <span id="form_fa_icon_class_display" class="font-mono text-[11px] text-emerald-400 block truncate">fa-solid fa-folder</span>
                    </div>
                    <button type="button" onclick="openIconPicker('form_fa_icon', 'form_fa_icon_preview')" 
                            class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider shrink-0 transition flex items-center gap-1.5 shadow">
                        <i class="fa-solid fa-icons"></i>
                        <span>Pilih Ikon</span>
                    </button>
                </div>

                <!-- Or Upload Custom 3D PNG Icon -->
                <div class="p-3 rounded-2xl bg-slate-800/60 border border-slate-800 space-y-1.5">
                    <label class="block text-[11px] font-semibold text-slate-400">Atau Upload Gambar Ikon 3D (Opsional, PNG transparan)</label>
                    <input type="file" name="icon_file" accept="image/*"
                           class="block w-full text-xs text-slate-300 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-slate-700 file:text-white cursor-pointer">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nomor Urutan</label>
                    <input type="number" id="form_order" name="order" value="1" min="1" max="99"
                           class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs">
                </div>
                <div class="flex items-center pt-5">
                    <label class="flex items-center space-x-2 text-xs font-bold text-white cursor-pointer">
                        <input type="checkbox" id="form_is_active" name="is_active" checked
                               class="w-4 h-4 rounded text-emerald-500 bg-slate-900 border-slate-700 focus:ring-emerald-400">
                        <span>Kategori Aktif</span>
                    </label>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-800 flex justify-end space-x-2">
                <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-bold hover:bg-slate-700">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateCategoryIconPreview(iconClass) {
    iconClass = iconClass || 'fa-solid fa-folder';
    const input = document.getElementById('form_fa_icon');
    const preview = document.getElementById('form_fa_icon_preview');
    const classDisplay = document.getElementById('form_fa_icon_class_display');
    const nameDisplay = document.getElementById('form_fa_icon_name');
    
    if (input) input.value = iconClass;
    if (preview) preview.className = iconClass;
    if (classDisplay) classDisplay.innerText = iconClass;
    
    if (nameDisplay && typeof ICON_CATALOG !== 'undefined') {
        const found = ICON_CATALOG.find(i => i.class === iconClass);
        nameDisplay.innerText = found ? found.name : 'Ikon Kustom';
    }
}

// Listen to changes
document.addEventListener('DOMContentLoaded', function() {
    const iconInput = document.getElementById('form_fa_icon');
    if (iconInput) {
        iconInput.addEventListener('change', function() {
            updateCategoryIconPreview(this.value);
        });
        iconInput.addEventListener('input', function() {
            updateCategoryIconPreview(this.value);
        });
    }
});

// 10 Curated Category Presets for Schools & Madrasah
const CATEGORY_TEMPLATES = [
    {
        name: 'APLIKASI KEMENAG',
        subname: 'EMIS 4.0, SIMPATIKA & RDM',
        badge: 'Kemenag',
        desc: 'Layanan terpadu pendataan dan administrasi resmi Kementerian Agama RI.',
        icon: 'fa-solid fa-mosque'
    },
    {
        name: 'LAYANAN KEMDIKBUD',
        subname: 'Dapodik, PMM & Rapor Pendidikan',
        badge: 'Kemdikdasmen',
        desc: 'Sistem pendataan pokok pendidikan nasional dan kurikulum merdeka.',
        icon: 'fa-solid fa-building-columns'
    },
    {
        name: 'ASESMEN & CBT ONLINE',
        subname: 'ANBK, PAS, PTS & Ujian Sekolah',
        badge: 'Ujian CBT',
        desc: 'Pelaksanaan asesmen nasional dan ujian sumatif berbasis komputer.',
        icon: 'fa-solid fa-laptop-code'
    },
    {
        name: 'AKADEMIK & GURU',
        subname: 'E-Rapor, Modul & Perangkat Ajar',
        badge: 'Kurikulum',
        desc: 'Pengelolaan nilai rapor, perangkat mengajar guru, dan bahan ajar digital.',
        icon: 'fa-solid fa-graduation-cap'
    },
    {
        name: 'KEUANGAN & ADMINISTRASI',
        subname: 'SPP, Tagihan, BOS & Kuitansi',
        badge: 'Keuangan',
        desc: 'Informasi pembayaran SPP siswa, administrasi kas, dan transparansi anggaran.',
        icon: 'fa-solid fa-wallet'
    },
    {
        name: 'KESISWAAN & EKSTRAKURIKULER',
        subname: 'OSIS, Pramuka, Ekskul & Prestasi',
        badge: 'Siswa & OSIS',
        desc: 'Organisasi siswa intra sekolah, kegiatan ekstrakurikuler, dan catatan prestasi.',
        icon: 'fa-solid fa-users'
    },
    {
        name: 'PERPUSTAKAAN DIGITAL',
        subname: 'E-Book, SIBI & Katalog Koleksi',
        badge: 'Literasi',
        desc: 'Koleksi buku pelajaran digital, modul literasi, dan katalog perpustakaan.',
        icon: 'fa-solid fa-book-open'
    },
    {
        name: 'PELAYANAN TERPADU (PTSP)',
        subname: 'Surat Keterangan & Hotline Pengaduan',
        badge: 'Layanan Publik',
        desc: 'Pusat pelayanan administrasi terpadu satu pintu dan meja informasi.',
        icon: 'fa-solid fa-bullhorn'
    },
    {
        name: 'KESANTRIAN & TAHFIDZ',
        subname: 'Hafalan Al-Quran & Asrama Santri',
        badge: 'Pesantren',
        desc: 'Monitoring mutabaah hafalan Al-Quran, kedisiplinan asrama santri, dan taklim.',
        icon: 'fa-solid fa-star-and-crescent'
    },
    {
        name: 'BK & KESEHATAN (UKS)',
        subname: 'Konseling, Pemeriksaan & Psikotes',
        badge: 'Karakter & BK',
        desc: 'Layanan bimbingan konseling karir, kesehatan siswa, dan konsultasi psikologi.',
        icon: 'fa-solid fa-heart'
    }
];

function applyCategoryTemplate(index) {
    const t = CATEGORY_TEMPLATES[index];
    if (!t) return;

    document.getElementById('form_name').value = t.name;
    document.getElementById('form_subname').value = t.subname;
    document.getElementById('form_badge').value = t.badge;
    document.getElementById('form_description').value = t.desc;
    updateCategoryIconPreview(t.icon);
}

function clearCategoryForm() {
    document.getElementById('form_name').value = '';
    document.getElementById('form_subname').value = '';
    document.getElementById('form_badge').value = '';
    document.getElementById('form_description').value = '';
    updateCategoryIconPreview('fa-solid fa-folder');
}

function openAddModal() {
    document.getElementById('modal-form-title').innerText = 'Tambah Kategori Baru';
    document.getElementById('form_category_id').value = '';
    document.getElementById('form_name').value = '';
    document.getElementById('form_subname').value = '';
    document.getElementById('form_description').value = '';
    document.getElementById('form_badge').value = '';
    updateCategoryIconPreview('fa-solid fa-folder');
    document.getElementById('form_order').value = <?= count($categories) + 1 ?>;
    document.getElementById('form_is_active').checked = true;
    document.getElementById('form_current_icon_image').value = '';
    document.getElementById('category-modal').classList.remove('hidden');
}

function editCategory(cat) {
    document.getElementById('modal-form-title').innerText = 'Edit Kategori: ' + cat.name;
    document.getElementById('form_category_id').value = cat.id;
    document.getElementById('form_name').value = cat.name;
    document.getElementById('form_subname').value = cat.subname || '';
    document.getElementById('form_description').value = cat.description || '';
    document.getElementById('form_badge').value = cat.badge || '';
    updateCategoryIconPreview(cat.fa_icon || 'fa-solid fa-folder');
    document.getElementById('form_order').value = cat.order || 1;
    document.getElementById('form_is_active').checked = !!cat.is_active;
    document.getElementById('form_current_icon_image').value = cat.icon_image || '';
    document.getElementById('category-modal').classList.remove('hidden');
}

function closeCategoryModal() {
    document.getElementById('category-modal').classList.add('hidden');
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
