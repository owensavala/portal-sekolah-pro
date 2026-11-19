# 🌐 Portal Sekolah & Madrasah Terpadu Pro

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Security](https://img.shields.io/badge/security-hardened-success.svg)](#keamanan)

Portal Web Layanan Terpadu multi-institusi yang dirancang khusus untuk sekolah dan madrasah (Kemenag, Kemdikbud SD/SMP/SMA, SMK Vokasi, Pondok Pesantren, hingga Kampus). Menampilkan antarmuka modern dengan **efek 3D tilt interaktif, spotlight cursor lighting, glassmorphism, dan sistem pengelolaan admin mandiri tanpa database MySQL (Zero-Config Flat JSON)**.

---

## ✨ Fitur Unggulan

### 🎨 Tampilan & Desain
- **Dual View Mode (1-Klik Switch)**:
  - **Mode Launcher 3D**: Kartu kategori interaktif dengan fisika tilt 3D dinamis, spotlight cursor glow, dan popup modal launcher.
  - **Mode Simpel**: Layout kisi bersih dengan filter tab instan dan pencarian instan.
- **Kustomisasi Tema Lengkap**:
  - Ubah warna primer, sekunder, dan aksen secara real-time.
  - 3 Mode Background: Warna Solid, Gradasi Modern, dan Wallpaper Gambar Gedung Sekolah dengan slider kegelapan (*overlay tint*).
  - Ganti Logo Institusi, Nama Sekolah, Slogan, dan Lokasi.
- **Template Institusi 1-Klik**:
  - Madrasah Kemenag (Hijau Emerald & Emas)
  - Sekolah Umum Kemdikbud (Biru Pendidikan & Kuning)
  - SMK Vokasi & Kejuruan (Indigo & Oranye)
  - Pondok Pesantren & Ma'had (Teal Islami)

### 🚀 Pengelolaan Admin & Operator
- **Katalog Template Kategori 1-Klik**: Mempermudah operator membuat kategori baru (Kemenag, Kemdikbud, Asesmen CBT, Akademik Rapor, Keuangan SPP, Kesiswaan OSIS, Perpustakaan, PTSP, Pesantren, UKS/BK) tanpa perlu merancang dari awal.
- **Visual Icon Picker**: Katalog 100+ ikon edukasi dengan pencarian instan (*live search*), filter kategori, dan live preview.
- **Manajemen Pintasan & Tautan**: Tambah, edit, urutkan, beri label badge, serta pasang pin (*Akses Cepat Favorit*).
- **Statistik & Analitik**: Penghitung klik (*click tracking*) untuk memantau aplikasi yang paling sering diakses.
- **Backup & Restore**: Ekspor dan impor seluruh data konfigurasi dan tautan dalam format JSON dengan 1-klik.

### 🛡️ Keamanan (Security Hardened)
- **Anti Brute-Force Rate Limiting**: Pembatasan maksimal 5 kali gagal login per IP dengan *auto-lockout* 10 menit.
- **CSRF Token Protection**: Validasi token kriptografi acak di seluruh formulir admin.
- **Session Security**: Session fixation prevention (`session_regenerate_id`), Secure Cookie Flags (`HttpOnly`, `SameSite=Lax`).
- **Deep Upload Protection**:
  - Whitelist ekstensi ketat (hanya gambar: png, jpg, jpeg, webp, svg, ico).
  - Pengecekan MIME-Type langsung dari stream file.
  - Pemblokiran *double extension* (misal: `shell.php.png`).
  - Pembersihan XSS pada file SVG (menghapus `<script>` dan event handler berbahaya).
  - `.htaccess` khusus di folder `uploads/` yang mematikan eksekusi script engine PHP.
- **Data Protection**: Folder `data/` dilindungi `.htaccess` (`Require all denied`) agar berkas JSON tidak dapat diakses langsung via browser.

---

## 🛠️ Panduan Instalasi & Menjalankan

### Cara 1: Menjalankan di Komputer Lokal (Windows / XAMPP)
1. Pastikan Anda memiliki PHP (PHP 8.0 atau lebih baru).
2. Jalankan file:
   ```cmd
   start_local.bat
   ```
   Atau via terminal:
   ```cmd
   php -S 0.0.0.0:8080
   ```
3. Buka browser: `http://localhost:8080`
4. Login Admin: `http://localhost:8080/admin/login.php`
   - **Username**: `admin`
   - **Password**: `admin123` *(Segera ubah password setelah login pertama kali di menu Ubah Akun Admin)*

### Cara 2: Deploy ke Web Hosting (cPanel / Hostinger / Niagahoster)
1. Kompres seluruh file ke dalam format `.zip`.
2. Buka **File Manager** cPanel, masuk ke folder `public_html` (atau subdomain).
3. Upload dan ekstrak file `.zip`.
4. Portal langsung aktif! **Tidak perlu membuat database MySQL atau konfigurasi koneksi**.

### Cara 3: Deploy ke Vercel
Project ini sudah dilengkapi konfigurasi `vercel.json` untuk runtime PHP:
1. Hubungkan repository GitHub ini di dashboard [Vercel](https://vercel.com).
2. Klik **Deploy**.

---

## 📁 Struktur Direktori

```text
├── admin/                  # Panel Admin & Pengelola
│   ├── categories.php      # Kelola kategori + Template Kategori Instan
│   ├── shortcuts.php       # Kelola link/tautan aplikasi
│   ├── settings.php        # Kustomisasi warna, background, logo, identitas
│   ├── icon_picker.php     # Komponen visual icon picker
│   ├── backup.php          # Backup & restore data
│   └── login.php           # Autentikasi dengan proteksi brute force
├── api/                    # Endpoint API (Search, Click Tracker)
├── assets/                 # Stylesheet, FontAwesome, JS App
├── data/                   # Basis data atomic JSON (Protected by .htaccess)
├── includes/               # Helper fungsi, autentikasi & generator tema
├── uploads/                # Penyimpanan logo & wallpaper (No PHP execution)
├── .htaccess               # Proteksi server utama
├── start_local.bat         # Launcher server lokal otomatis
└── vercel.json             # Konfigurasi deploy Vercel
```

---

## 📄 Lisensi
Didistribusikan di bawah lisensi MIT. Silakan digunakan dan dikembangkan untuk kemajuan digitalisasi sekolah dan madrasah di Indonesia.
