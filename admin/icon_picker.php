<?php
/**
 * Visual Icon Picker Component
 * Modal dialog & JS helper for selecting FontAwesome 6 icons visually with search and categories.
 */
?>
<!-- Visual Icon Picker Modal -->
<div id="icon-picker-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-4">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" onclick="closeIconPicker()"></div>

    <!-- Modal Box -->
    <div class="relative w-full max-w-3xl rounded-3xl bg-slate-900 border border-slate-700 shadow-2xl overflow-hidden z-10 flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Header -->
        <div class="p-5 border-b border-slate-800 flex items-center justify-between bg-slate-900/90 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-lg">
                    <i class="fa-solid fa-icons"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Pilih Ikon Visual</h3>
                    <p class="text-xs text-slate-400">Pilih dari katalog edukasi & portal, atau ketik kata kunci pencarian</p>
                </div>
            </div>
            <button type="button" onclick="closeIconPicker()" 
                    class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Search & Category Filters -->
        <div class="p-4 bg-slate-950/50 border-b border-slate-800 space-y-3 shrink-0">
            <!-- Search Bar -->
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-xs text-slate-400"></i>
                <input type="text" id="icon-picker-search" 
                       placeholder="Cari ikon (misal: sekolah, buku, cbt, guru, siswa, wa, raport, uang, cloud)..."
                       autocomplete="off"
                       class="w-full pl-10 pr-10 py-2.5 rounded-2xl bg-slate-800 border border-slate-700 text-white placeholder-slate-400 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <button type="button" id="icon-picker-clear-search" onclick="clearIconSearch()" 
                        class="hidden absolute right-3 top-2.5 text-slate-400 hover:text-white text-xs">
                    <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </div>

            <!-- Category Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs scrollbar-thin" id="icon-category-tabs">
                <button type="button" onclick="filterIconCategory('all')" data-cat="all"
                        class="icon-tab-btn active px-3 py-1.5 rounded-xl font-semibold whitespace-nowrap bg-emerald-600 text-white transition">
                    Semua (<span id="icon-count-total">0</span>)
                </button>
                <button type="button" onclick="filterIconCategory('edu')" data-cat="edu"
                        class="icon-tab-btn px-3 py-1.5 rounded-xl font-semibold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                    <i class="fa-solid fa-school text-emerald-400 mr-1"></i> Pendidikan
                </button>
                <button type="button" onclick="filterIconCategory('exam')" data-cat="exam"
                        class="icon-tab-btn px-3 py-1.5 rounded-xl font-semibold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                    <i class="fa-solid fa-laptop-code text-cyan-400 mr-1"></i> CBT & Ujian
                </button>
                <button type="button" onclick="filterIconCategory('it')" data-cat="it"
                        class="icon-tab-btn px-3 py-1.5 rounded-xl font-semibold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                    <i class="fa-solid fa-database text-purple-400 mr-1"></i> Sistem & IT
                </button>
                <button type="button" onclick="filterIconCategory('web')" data-cat="web"
                        class="icon-tab-btn px-3 py-1.5 rounded-xl font-semibold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                    <i class="fa-solid fa-globe text-blue-400 mr-1"></i> Web & Portal
                </button>
                <button type="button" onclick="filterIconCategory('user')" data-cat="user"
                        class="icon-tab-btn px-3 py-1.5 rounded-xl font-semibold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                    <i class="fa-solid fa-user-graduate text-amber-400 mr-1"></i> Siswa & Guru
                </button>
                <button type="button" onclick="filterIconCategory('social')" data-cat="social"
                        class="icon-tab-btn px-3 py-1.5 rounded-xl font-semibold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                    <i class="fa-brands fa-whatsapp text-emerald-400 mr-1"></i> Medsos & Kontak
                </button>
                <button type="button" onclick="filterIconCategory('finance')" data-cat="finance"
                        class="icon-tab-btn px-3 py-1.5 rounded-xl font-semibold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                    <i class="fa-solid fa-wallet text-amber-400 mr-1"></i> Keuangan
                </button>
                <button type="button" onclick="filterIconCategory('religious')" data-cat="religious"
                        class="icon-tab-btn px-3 py-1.5 rounded-xl font-semibold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                    <i class="fa-solid fa-mosque text-emerald-300 mr-1"></i> Karakter & Agama
                </button>
                <button type="button" onclick="filterIconCategory('time')" data-cat="time"
                        class="icon-tab-btn px-3 py-1.5 rounded-xl font-semibold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                    <i class="fa-solid fa-calendar-days text-rose-400 mr-1"></i> Jadwal & Peta
                </button>
            </div>
        </div>

        <!-- Icon Grid Container -->
        <div class="p-4 sm:p-5 overflow-y-auto flex-1 max-h-[380px] scrollbar-thin">
            <div id="icon-picker-grid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2.5">
                <!-- Dynamically populated by JS -->
            </div>

            <!-- Empty Search State -->
            <div id="icon-picker-empty" class="hidden text-center py-12">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-800 flex items-center justify-center text-slate-500 text-xl mb-2">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <p class="text-sm font-semibold text-slate-400">Tidak ada ikon yang sesuai</p>
                <p class="text-xs text-slate-500 mt-1">Coba gunakan kata kunci umum lain atau masukkan kode FontAwesome di bawah.</p>
            </div>
        </div>

        <!-- Footer / Manual Fallback -->
        <div class="p-4 bg-slate-950/80 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <span class="text-[11px] text-slate-400 whitespace-nowrap">Input Manual FontAwesome:</span>
                <input type="text" id="icon-picker-manual" placeholder="fa-solid fa-bell"
                       class="px-3 py-1.5 rounded-xl bg-slate-800 border border-slate-700 text-white font-mono text-xs w-48 focus:outline-none focus:ring-1 focus:ring-emerald-400">
                <button type="button" onclick="applyManualIcon()"
                        class="px-3 py-1.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold transition">
                    Terapkan
                </button>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <button type="button" onclick="closeIconPicker()"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition">
                    Batal
                </button>
            </div>
        </div>

    </div>
</div>

<script>
// Catalog of icons curated for Indonesian School & Educational Portals
const ICON_CATALOG = [
    // PENDIDIKAN & BELAJAR
    { class: 'fa-solid fa-school', name: 'Sekolah / Madrasah', cat: 'edu', kw: 'sekolah madrasah gedung building school edu' },
    { class: 'fa-solid fa-school-flag', name: 'Kampus / Gedung', cat: 'edu', kw: 'gedung sekolah bendera kampus building' },
    { class: 'fa-solid fa-graduation-cap', name: 'Kelulusan / Toga', cat: 'edu', kw: 'wisuda toga kelulusan alumni sarjana graduation student' },
    { class: 'fa-solid fa-book', name: 'Buku Pelajaran', cat: 'edu', kw: 'buku modul materi belajar book library perpustakaan' },
    { class: 'fa-solid fa-book-open', name: 'Buku Terbuka / Modul', cat: 'edu', kw: 'baca buku terbuka modul ajar kurikulum reading' },
    { class: 'fa-solid fa-book-bookmark', name: 'Jurnal / Catatan', cat: 'edu', kw: 'jurnal catatan buku pembatas buku diary note' },
    { class: 'fa-solid fa-chalkboard-user', name: 'Guru & Papan Tulis', cat: 'edu', kw: 'guru pengajar ustadz kelas mengajar teacher class lesson' },
    { class: 'fa-solid fa-chalkboard', name: 'Papan Tulis / Kelas', cat: 'edu', kw: 'papan tulis kelas materi ruang belajar blackboard' },
    { class: 'fa-solid fa-award', name: 'Prestasi / Piagam', cat: 'edu', kw: 'prestasi piagam penghargaan juara award badge' },
    { class: 'fa-solid fa-trophy', name: 'Piala / Juara', cat: 'edu', kw: 'piala juara perlombaan olimpiade trophy winner' },
    { class: 'fa-solid fa-medal', name: 'Medali Prestasi', cat: 'edu', kw: 'medali emas perak juara lomba medal' },
    { class: 'fa-solid fa-certificate', name: 'Sertifikat / Ijazah', cat: 'edu', kw: 'sertifikat ijazah kelulusan akreditasi lisensi certificate' },
    { class: 'fa-solid fa-flask', name: 'Laboratorium IPA', cat: 'edu', kw: 'lab ipa kimia sains percobaan praktikum science chemistry' },
    { class: 'fa-solid fa-atom', name: 'Sains / Fisika', cat: 'edu', kw: 'fisika sains atom penelitian stem science' },
    { class: 'fa-solid fa-microscope', name: 'Mikroskop / Biologi', cat: 'edu', kw: 'biologi mikroskop penelitian riset science lab' },
    { class: 'fa-solid fa-palette', name: 'Seni Budaya / Lukis', cat: 'edu', kw: 'seni lukis gambar kreativitas art design' },
    { class: 'fa-solid fa-music', name: 'Kesenian / Musik', cat: 'edu', kw: 'musik kesenian marching band lagu suara audio' },
    { class: 'fa-solid fa-shapes', name: 'Ekstrakurikuler', cat: 'edu', kw: 'ekskul kegiatan variasi bentuk kreasi shapes' },
    { class: 'fa-solid fa-futbol', name: 'Olahraga / Futsal', cat: 'edu', kw: 'olahraga bola futsal sepakbola jasmani sport' },
    { class: 'fa-solid fa-building-columns', name: 'Universitas / Dinas', cat: 'edu', kw: 'dinas kemdikbud kementerian universitas kampus hukum' },

    // CBT & UJIAN
    { class: 'fa-solid fa-laptop-code', name: 'CBT / ANBK', cat: 'exam', kw: 'cbt anbk asesmen ujian komputer tes test exam laptop' },
    { class: 'fa-solid fa-laptop-file', name: 'Ujian Online', cat: 'exam', kw: 'ujian tes soal evaluasi quiz laptop' },
    { class: 'fa-solid fa-file-signature', name: 'Lembar Ujian / SPMB', cat: 'exam', kw: 'lembar ujian soal formulir pendaftaran spmb ppdb signature' },
    { class: 'fa-solid fa-square-check', name: 'Hasil Kelulusan', cat: 'exam', kw: 'kelulusan nilai hasil lolos checklist pass verified' },
    { class: 'fa-solid fa-list-check', name: 'Absensi / Presensi', cat: 'exam', kw: 'absensi kehadiran presensi ceklis checklist attendance' },
    { class: 'fa-solid fa-pen-to-square', name: 'Input Nilai / Edit', cat: 'exam', kw: 'input nilai e-rapor rapot edit formulir form pen write' },
    { class: 'fa-solid fa-clipboard-check', name: 'Verifikasi Berkas', cat: 'exam', kw: 'verifikasi berkas persyaratan lolos dokumen checklist' },
    { class: 'fa-solid fa-clipboard-list', name: 'Daftar Tugas / Soal', cat: 'exam', kw: 'tugas pekerjaan rumah pr soal daftar list task' },
    { class: 'fa-solid fa-chart-line', name: 'Grafik / Progres Rapor', cat: 'exam', kw: 'progres nilai capaian rapor statistik analitik chart' },
    { class: 'fa-solid fa-brain', name: 'Tes Psikologi / Bakat', cat: 'exam', kw: 'psikotes bakat minat kecerdasan otak brain test' },
    { class: 'fa-solid fa-ranking-star', name: 'Peringkat / Ranking', cat: 'exam', kw: 'ranking peringkat bintang terbaik juara kelas rank' },

    // SISTEM & IT
    { class: 'fa-solid fa-database', name: 'EMIS / Dapodik / DB', cat: 'it', kw: 'emis dapodik database data server sql pendataan' },
    { class: 'fa-solid fa-server', name: 'Server / Hosting', cat: 'it', kw: 'server hosting cloud backend infrastruktur pusat data' },
    { class: 'fa-solid fa-network-wired', name: 'Jaringan / LAN', cat: 'it', kw: 'jaringan lan internet wifi router switch kabel' },
    { class: 'fa-solid fa-desktop', name: 'Komputer PC / Lab', cat: 'it', kw: 'lab komputer pc proktor monitor workstation desktop' },
    { class: 'fa-solid fa-laptop', name: 'Laptop / Notebook', cat: 'it', kw: 'laptop guru siswa notebook komputer jinjing' },
    { class: 'fa-solid fa-cloud', name: 'Cloud Storage', cat: 'it', kw: 'cloud awan drive penyimpanan berkas gdrive onedrive' },
    { class: 'fa-solid fa-cloud-arrow-down', name: 'Unduh / Download', cat: 'it', kw: 'download unduh materi file berkas cetak' },
    { class: 'fa-solid fa-cloud-arrow-up', name: 'Unggah / Upload', cat: 'it', kw: 'upload unggah kirim berkas tugas dokumen' },
    { class: 'fa-solid fa-shield-halved', name: 'Keamanan / SIMPATIKA', cat: 'it', kw: 'keamanan sistem simpatika perisai akun proteksi shield' },
    { class: 'fa-solid fa-lock', name: 'Login / Sandi', cat: 'it', kw: 'kunci password sandi login ganti privasi lock' },
    { class: 'fa-solid fa-key', name: 'Token / Kunci Akses', cat: 'it', kw: 'token ujian rdm cbt sandi akses lisensi key' },
    { class: 'fa-solid fa-wifi', name: 'Hotspot / WiFi', cat: 'it', kw: 'wifi internet kuota hotspot koneksi nirkabel' },
    { class: 'fa-solid fa-qrcode', name: 'Scan QR Presensi', cat: 'it', kw: 'qr code barcode scan pemindai presensi qris' },
    { class: 'fa-solid fa-print', name: 'Cetak Dokumen', cat: 'it', kw: 'cetak print kartu ujian formulir laporan printer' },
    { class: 'fa-solid fa-hard-drive', name: 'Backup / Arsip', cat: 'it', kw: 'backup arsip harddisk data simpan restore' },
    { class: 'fa-solid fa-gear', name: 'Pengaturan / Setelan', cat: 'it', kw: 'pengaturan setting konfigurasi gear alat' },
    { class: 'fa-solid fa-sliders', name: 'Filter / Parameter', cat: 'it', kw: 'filter kontrol slider sesuaikan opsi parameter' },

    // WEB & PORTAL
    { class: 'fa-solid fa-globe', name: 'Website Resmi', cat: 'web', kw: 'website portal web resmi domain www internet online' },
    { class: 'fa-solid fa-link', name: 'Tautan / Link URL', cat: 'web', kw: 'link tautan url pintasan akses website' },
    { class: 'fa-solid fa-arrow-up-right-from-square', name: 'Buka Eksternal', cat: 'web', kw: 'tab baru eksternal buka link keluar window' },
    { class: 'fa-solid fa-house', name: 'Beranda / Home', cat: 'web', kw: 'beranda home utama awal depan rumah' },
    { class: 'fa-solid fa-compass', name: 'Panduan / Kompas', cat: 'web', kw: 'panduan petunjuk arah manual buku kompas compass' },
    { class: 'fa-solid fa-sitemap', name: 'Struktur Organisasi', cat: 'web', kw: 'struktur bagan komite pengurus osis yayasan sitemap' },
    { class: 'fa-solid fa-magnifying-glass', name: 'Pencarian Data', cat: 'web', kw: 'cari search temukan cek nisn nuptk data search' },
    { class: 'fa-solid fa-bars', name: 'Menu Pintasan', cat: 'web', kw: 'menu daftar pilihan nav navigasi' },
    { class: 'fa-solid fa-bookmark', name: 'Tandai / Favorit', cat: 'web', kw: 'favorit bookmark simpan penting pin' },
    { class: 'fa-solid fa-share-nodes', name: 'Bagikan / Share', cat: 'web', kw: 'bagikan share medsos kirim sebar link' },
    { class: 'fa-solid fa-folder', name: 'Folder Arsip', cat: 'web', kw: 'folder berkas kategori dokumen arsip direktori' },
    { class: 'fa-solid fa-folder-open', name: 'Folder Terbuka', cat: 'web', kw: 'folder arsip unduhan berkas dokumen' },
    { class: 'fa-solid fa-layer-group', name: 'Kategori / Modul', cat: 'web', kw: 'kategori paket kelompok layer tingkatan grup' },
    { class: 'fa-solid fa-table-cells-large', name: 'Dashboard Aplikasi', cat: 'web', kw: 'grid dashboard menu aplikasi galeri kumpulan' },

    // PENGGUNA & CIVITAS
    { class: 'fa-solid fa-user', name: 'Profil Siswa / User', cat: 'user', kw: 'user siswa santri profil murid orang akun' },
    { class: 'fa-solid fa-users', name: 'Civitas / Santri / Siswa', cat: 'user', kw: 'komunitas perkumpulan siswa-siswi santri kelas rombel users' },
    { class: 'fa-solid fa-user-graduate', name: 'Siswa & Alumni', cat: 'user', kw: 'siswa santri murid alumni wisudawan graduate' },
    { class: 'fa-solid fa-user-tie', name: 'Kepala Sekolah / Guru', cat: 'user', kw: 'kepala sekolah kamad pimpinan pejabat direktur guru ustadz' },
    { class: 'fa-solid fa-user-check', name: 'Verifikasi Akun', cat: 'user', kw: 'validasi akun siswa terverifikasi aktif setuju' },
    { class: 'fa-solid fa-user-shield', name: 'Admin / Pengawas', cat: 'user', kw: 'admin operator pengawas moderator satpam penjaga' },
    { class: 'fa-solid fa-id-card', name: 'Kartu Pelajar / NISN', cat: 'user', kw: 'kartu pelajar nuptk nisn nip ktp identitas id card' },
    { class: 'fa-solid fa-address-book', name: 'Buku Induk Siswa', cat: 'user', kw: 'buku induk daftar kontak alumni alumni kontak address' },
    { class: 'fa-solid fa-passport', name: 'Rapor / Buku Saku', cat: 'user', kw: 'buku saku pelanggaran prestasi tata tertib paspor' },

    // MEDSOS & KONTAK
    { class: 'fa-brands fa-whatsapp', name: 'WhatsApp Center', cat: 'social', kw: 'whatsapp wa pesan chat admin hubungi cs hotline' },
    { class: 'fa-brands fa-youtube', name: 'YouTube Madrasah/Sekolah', cat: 'social', kw: 'youtube video streaming siaran kajian tutorial' },
    { class: 'fa-brands fa-instagram', name: 'Instagram Resmi', cat: 'social', kw: 'instagram ig foto dokumentasi kegiatan sosmed' },
    { class: 'fa-brands fa-facebook', name: 'Facebook Fanpage', cat: 'social', kw: 'facebook fb grup komunitas sosmed' },
    { class: 'fa-brands fa-tiktok', name: 'TikTok Madrasah', cat: 'social', kw: 'tiktok video pendek edukasi konten kreator' },
    { class: 'fa-brands fa-telegram', name: 'Telegram Saluran', cat: 'social', kw: 'telegram grup pengumuman notifikasi' },
    { class: 'fa-brands fa-google', name: 'Google Workspace', cat: 'social', kw: 'google belajar.id akun akun kemdikbud gmail' },
    { class: 'fa-brands fa-google-drive', name: 'Google Drive Sekolah', cat: 'social', kw: 'gdrive google drive penyimpanan berkas folder awan' },
    { class: 'fa-solid fa-envelope', name: 'Email Madrasah/Sekolah', cat: 'social', kw: 'email surel surat pesan inbox surat menyurat' },
    { class: 'fa-solid fa-phone', name: 'Hotline / Telepon', cat: 'social', kw: 'telepon phone call pusat panggilan layanan cs' },
    { class: 'fa-solid fa-headset', name: 'Helpdesk Layanan', cat: 'social', kw: 'helpdesk bantuan support konseling layanan pelanggan' },
    { class: 'fa-solid fa-bullhorn', name: 'Pengumuman / Warta', cat: 'social', kw: 'pengumuman berita warta siaran edaran surat resmi bullhorn' },
    { class: 'fa-solid fa-newspaper', name: 'Koran / Majalah Sekolah', cat: 'social', kw: 'majalah artikel berita warta mading jurnalisme' },
    { class: 'fa-solid fa-video', name: 'Zoom / Google Meet', cat: 'social', kw: 'zoom gmeet video call konferensi pertemuan virtual daring' },
    { class: 'fa-solid fa-comments', name: 'Forum / Ruang Diskusi', cat: 'social', kw: 'diskusi obrolan tanya jawab forum chat komunitas' },

    // KEUANGAN & PEMBAYARAN
    { class: 'fa-solid fa-wallet', name: 'Dompet / Kas / SPP', cat: 'finance', kw: 'spp kas keuangan infaq donasi uang dompet wallet' },
    { class: 'fa-solid fa-money-bill-wave', name: 'Pembayaran Tunai', cat: 'finance', kw: 'uang kas pembayaran tunai bank transfer rupiah money' },
    { class: 'fa-solid fa-credit-card', name: 'Kartu ATM / Tagihan', cat: 'finance', kw: 'kartu pembayaran debit edc kredit virtual account va' },
    { class: 'fa-solid fa-receipt', name: 'Kuitansi / Bukti Bayar', cat: 'finance', kw: 'kuitansi nota struk bukti pembayaran tanda terima' },
    { class: 'fa-solid fa-file-invoice', name: 'Tagihan SPP / Invoice', cat: 'finance', kw: 'invoice tagihan rincian biaya surat pembayaran' },
    { class: 'fa-solid fa-coins', name: 'Dana BOS / PIP / Koin', cat: 'finance', kw: 'dana bos bantuan pip beasiswa koin infaq tabungan' },
    { class: 'fa-solid fa-vault', name: 'Bendahara / Brankas', cat: 'finance', kw: 'brankas bendahara kasir keuangan yayasan aman' },
    { class: 'fa-solid fa-scale-balanced', name: 'Neraca / Transparansi', cat: 'finance', kw: 'neraca transparansi audit hukum keadilan imbang' },

    // AGAMA & KARAKTER
    { class: 'fa-solid fa-mosque', name: 'Masjid / Musholla', cat: 'religious', kw: 'masjid musholla islam keagamaan ibadah rohis santri pesantren' },
    { class: 'fa-solid fa-star-and-crescent', name: 'Kemenag / Islam', cat: 'religious', kw: 'bulan bintang islam kemenag pesantren madrasah' },
    { class: 'fa-solid fa-hands-praying', name: 'Doa / Ibadah', cat: 'religious', kw: 'doa shalat ibadah dzikir munajat doa' },
    { class: 'fa-solid fa-heart', name: 'Karakter / BP / BK', cat: 'religious', kw: 'karakter budi pekerti bimbingan konseling kasih akhlak' },
    { class: 'fa-solid fa-hand-holding-heart', name: 'Infaq / Shadaqah', cat: 'religious', kw: 'infaq zakat sedekah donasi kebaikan sosial peduli' },
    { class: 'fa-solid fa-moon', name: 'Ramadhan / Tahfidz', cat: 'religious', kw: 'ramadhan bulan malam tahfidz quran asrama pesantren' },
    { class: 'fa-solid fa-seedling', name: 'Adiwiyata / Karakter', cat: 'religious', kw: 'adiwiyata lingkungan hijau pohon tanaman alam' },

    // JADWAL & LOKASI
    { class: 'fa-solid fa-calendar-days', name: 'Kalender Pendidikan', cat: 'time', kw: 'kalender jadwal agenda tahun ajaran semester tanggal' },
    { class: 'fa-solid fa-clock', name: 'Jam Pelajaran / Waktu', cat: 'time', kw: 'jam waktu jadwal durasi istirahat bel' },
    { class: 'fa-solid fa-bell', name: 'Pengingat / Notifikasi', cat: 'time', kw: 'bel lonceng alarm pengingat notifikasi info' },
    { class: 'fa-solid fa-location-dot', name: 'Lokasi Kampus / Peta', cat: 'time', kw: 'peta lokasi gedung alamat denah maps google maps' },
    { class: 'fa-solid fa-map-location-dot', name: 'Denah Madrasah/Sekolah', cat: 'time', kw: 'denah peta petunjuk arah rute gedung' },
    { class: 'fa-solid fa-flag', name: 'Upacara / Nasionalisme', cat: 'time', kw: 'upacara bendera kebangsaan paskibra senin' },
    { class: 'fa-solid fa-bolt', name: 'Akses Cepat / Kilat', cat: 'time', kw: 'kilat cepat petir favorit utama penting' },
    { class: 'fa-solid fa-wand-magic-sparkles', name: 'Fitur Khusus / AI', cat: 'time', kw: 'ajaib ai cerdas fitur khusus pintar baru' }
];

let currentTargetInputId = null;
let currentPreviewIconId = null;
let currentActiveCat = 'all';

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    renderIconGrid();
    document.getElementById('icon-count-total').innerText = ICON_CATALOG.length;

    // Search event
    const searchInput = document.getElementById('icon-picker-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const val = this.value.trim().toLowerCase();
            document.getElementById('icon-picker-clear-search').classList.toggle('hidden', val.length === 0);
            renderIconGrid(val, currentActiveCat);
        });
    }
});

function openIconPicker(targetInputId, previewIconId) {
    currentTargetInputId = targetInputId;
    currentPreviewIconId = previewIconId;

    const modal = document.getElementById('icon-picker-modal');
    modal.classList.remove('hidden');

    // Get current value
    const currentVal = document.getElementById(targetInputId)?.value || '';
    document.getElementById('icon-picker-manual').value = currentVal;

    // Highlight current selection in grid
    renderIconGrid('', currentActiveCat, currentVal);

    // Focus search
    setTimeout(() => {
        const sInput = document.getElementById('icon-picker-search');
        if (sInput) sInput.focus();
    }, 100);
}

function closeIconPicker() {
    document.getElementById('icon-picker-modal').classList.add('hidden');
}

function filterIconCategory(cat) {
    currentActiveCat = cat;
    document.querySelectorAll('.icon-tab-btn').forEach(btn => {
        if (btn.getAttribute('data-cat') === cat) {
            btn.className = 'icon-tab-btn active px-3 py-1.5 rounded-xl font-semibold whitespace-nowrap bg-emerald-600 text-white transition';
        } else {
            btn.className = 'icon-tab-btn px-3 py-1.5 rounded-xl font-semibold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 transition';
        }
    });

    const searchVal = document.getElementById('icon-picker-search')?.value.trim().toLowerCase() || '';
    renderIconGrid(searchVal, cat);
}

function clearIconSearch() {
    const sInput = document.getElementById('icon-picker-search');
    if (sInput) {
        sInput.value = '';
        document.getElementById('icon-picker-clear-search').classList.add('hidden');
        renderIconGrid('', currentActiveCat);
        sInput.focus();
    }
}

function renderIconGrid(searchTerm = '', category = 'all', currentSelected = '') {
    const grid = document.getElementById('icon-picker-grid');
    const emptyState = document.getElementById('icon-picker-empty');
    if (!grid) return;

    if (!currentSelected && currentTargetInputId) {
        currentSelected = document.getElementById(currentTargetInputId)?.value || '';
    }

    grid.innerHTML = '';
    let matchCount = 0;

    ICON_CATALOG.forEach(item => {
        // Category filter
        if (category !== 'all' && item.cat !== category) {
            return;
        }

        // Search filter
        if (searchTerm) {
            const haystack = (item.name + ' ' + item.class + ' ' + item.kw).toLowerCase();
            if (!haystack.includes(searchTerm)) {
                return;
            }
        }

        matchCount++;

        const isSelected = (currentSelected.trim() === item.class.trim());
        const card = document.createElement('button');
        card.type = 'button';
        card.className = `group flex flex-col items-center justify-center p-3 rounded-2xl border transition-all text-center relative ${
            isSelected 
                ? 'bg-emerald-500/20 border-emerald-500 ring-2 ring-emerald-500/50 shadow-lg shadow-emerald-500/20' 
                : 'bg-slate-800/80 hover:bg-slate-800 border-slate-700 hover:border-emerald-500/60 hover:-translate-y-0.5'
        }`;

        card.onclick = function() {
            selectIcon(item.class);
        };

        card.innerHTML = `
            <div class="w-10 h-10 rounded-xl ${isSelected ? 'bg-emerald-500 text-slate-950' : 'bg-slate-900 text-slate-300 group-hover:text-emerald-400 group-hover:bg-slate-950'} flex items-center justify-center text-lg mb-2 transition shadow">
                <i class="${item.class}"></i>
            </div>
            <span class="text-[11px] font-bold ${isSelected ? 'text-emerald-300' : 'text-slate-300 group-hover:text-white'} leading-tight line-clamp-2 max-w-full">
                ${item.name}
            </span>
            <span class="text-[9px] font-mono text-slate-400 mt-0.5 truncate max-w-full opacity-60">
                ${item.class.replace('fa-solid ', '').replace('fa-brands ', '')}
            </span>
            ${isSelected ? '<span class="absolute top-1.5 right-1.5 w-4 h-4 bg-emerald-500 text-slate-950 rounded-full flex items-center justify-center text-[9px] font-black"><i class="fa-solid fa-check"></i></span>' : ''}
        `;

        grid.appendChild(card);
    });

    if (matchCount === 0) {
        emptyState.classList.remove('hidden');
    } else {
        emptyState.classList.add('hidden');
    }
}

function selectIcon(iconClass) {
    if (currentTargetInputId) {
        const input = document.getElementById(currentTargetInputId);
        if (input) {
            input.value = iconClass;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    if (currentPreviewIconId) {
        const preview = document.getElementById(currentPreviewIconId);
        if (preview) {
            preview.className = iconClass;
        }
    }

    // Also update any badge or preview text if present
    const previewName = document.getElementById(currentTargetInputId + '_name');
    if (previewName) {
        const found = ICON_CATALOG.find(i => i.class === iconClass);
        previewName.innerText = found ? found.name : iconClass;
    }

    closeIconPicker();
}

function applyManualIcon() {
    const val = document.getElementById('icon-picker-manual').value.trim();
    if (val) {
        selectIcon(val);
    }
}
</script>
