<?php
/**
 * School Type Templates & Presets
 * Allows any school (SD, SMP, SMA, SMK, MI, MTs, MA, Pesantren, Kampus)
 * to be configured in 1-click!
 */

function get_school_presets() {
    return [
        'madrasah' => [
            'key' => 'madrasah',
            'label' => 'Madrasah (MI / MTs / MA - Kemenag)',
            'badge' => 'Madrasah Kemenag',
            'icon' => 'fa-mosque',
            'description' => 'Untuk Madrasah Ibtidaiyah, Tsanawiyah, dan Aliyah di bawah Kementerian Agama RI.',
            'config' => [
                'school_type' => 'madrasah',
                'school_type_badge' => 'Madrasah Kemenag',
                'school_name' => 'MIS INTEGRAL HIDAYATULLAH',
                'school_location' => 'Kota Ternate, Maluku Utara',
                'tagline' => "Aku Cerdas Dengan Al-Qur'an",
                'site_title' => 'Portal Layanan Digital • MIS Integral Hidayatullah Kota Ternate',
                'theme_preset' => 'emerald_gold',
                'primary_color' => '#00873e',
                'secondary_color' => '#f59e0b',
                'accent_color' => '#10b981',
                'bg_type' => 'gradient',
                'bg_color_start' => '#032b17',
                'bg_color_end' => '#0b532c',
                'bg_overlay_color' => '#032b17',
                'bg_gradient_angle' => '135',
                'footer_text' => 'MIS Integral Hidayatullah Kota Ternate'
            ],
            'categories' => [
                [
                    'id' => 'cat_website',
                    'name' => 'WEBSITE RESMI',
                    'subname' => 'Profil & Berita Madrasah',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-globe',
                    'badge' => 'Resmi',
                    'order' => 1,
                    'is_active' => true
                ],
                [
                    'id' => 'cat_kemenag',
                    'name' => 'PENDATAAN KEMENAG',
                    'subname' => 'EMIS 4.0, Simpatika & AKMI',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-database',
                    'badge' => 'Kemenag',
                    'order' => 2,
                    'is_active' => true
                ],
                [
                    'id' => 'cat_akademik',
                    'name' => 'AKADEMIK & RAPOR',
                    'subname' => 'RDM & E-Learning',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-graduation-cap',
                    'badge' => 'Akademik',
                    'order' => 3,
                    'is_active' => true
                ],
                [
                    'id' => 'cat_ujian',
                    'name' => 'CBT & ASESMEN',
                    'subname' => 'Ujian Online Siswa & Guru',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-laptop-code',
                    'badge' => 'Ujian',
                    'order' => 4,
                    'is_active' => true
                ]
            ],
            'shortcuts' => [
                [
                    'id' => 'sc_m1',
                    'category_id' => 'cat_website',
                    'title' => 'Website Resmi Madrasah',
                    'url' => 'https://misintegralhidayatullah.sch.id/',
                    'description' => 'Laman informasi utama, berita, dan kegiatan madrasah',
                    'badge' => 'Resmi',
                    'fa_icon' => 'fa-solid fa-globe',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'id' => 'sc_m2',
                    'category_id' => 'cat_kemenag',
                    'title' => 'EMIS 4.0 Madrasah',
                    'url' => 'https://emis.kemenag.go.id/',
                    'description' => 'Sistem Pendataan Kelembagaan, Siswa & GTK Kemenag RI',
                    'badge' => 'Wajib',
                    'fa_icon' => 'fa-solid fa-database',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'id' => 'sc_m3',
                    'category_id' => 'cat_kemenag',
                    'title' => 'SIMPATIKA Kemenag',
                    'url' => 'https://emisgtk.kemenag.go.id/',
                    'description' => 'Sistem Informasi Pendidik dan Tenaga Kependidikan',
                    'badge' => 'Guru',
                    'fa_icon' => 'fa-solid fa-user-check',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 2
                ],
                [
                    'id' => 'sc_m4',
                    'category_id' => 'cat_kemenag',
                    'title' => 'ANBK Kemdikbud',
                    'url' => 'https://anbk.kemdikbud.go.id/',
                    'description' => 'Portal Asesmen Nasional Berbasis Komputer',
                    'badge' => 'Nasional',
                    'fa_icon' => 'fa-solid fa-desktop',
                    'is_pinned' => false,
                    'is_active' => true,
                    'order' => 3
                ],
                [
                    'id' => 'sc_m5',
                    'category_id' => 'cat_akademik',
                    'title' => 'Rapor Digital Madrasah (RDM)',
                    'url' => 'https://rdm.kemenag.go.id/',
                    'description' => 'Aplikasi Pengisian dan Cetak Rapor Siswa Terintegrasi',
                    'badge' => 'RDM',
                    'fa_icon' => 'fa-solid fa-file-invoice',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'id' => 'sc_m6',
                    'category_id' => 'cat_akademik',
                    'title' => 'E-Learning Madrasah',
                    'url' => 'https://elearning.kemenag.go.id/',
                    'description' => 'Portal Belajar Daring, Materi Kelas, dan Tugas Siswa',
                    'badge' => 'Belajar',
                    'fa_icon' => 'fa-solid fa-book-open-reader',
                    'is_pinned' => false,
                    'is_active' => true,
                    'order' => 2
                ],
                [
                    'id' => 'sc_m7',
                    'category_id' => 'cat_ujian',
                    'title' => 'CBT Ujian Online Siswa',
                    'url' => '#',
                    'description' => 'Aplikasi Pelaksanaan Ujian Asesmen Berbasis Komputer',
                    'badge' => 'Siswa',
                    'fa_icon' => 'fa-solid fa-pen-to-square',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'id' => 'sc_m8',
                    'category_id' => 'cat_ujian',
                    'title' => 'Portal AKMI Kemenag',
                    'url' => 'https://akmi.kemenag.go.id/',
                    'description' => 'Asesmen Kompetensi Madrasah Indonesia',
                    'badge' => 'AKMI',
                    'fa_icon' => 'fa-solid fa-award',
                    'is_pinned' => false,
                    'is_active' => true,
                    'order' => 2
                ]
            ]
        ],

        'kemdikbud' => [
            'key' => 'kemdikbud',
            'label' => 'Sekolah Umum (SD / SMP / SMA - Kemendikbud)',
            'badge' => 'Sekolah Kemendikbud',
            'icon' => 'fa-school',
            'description' => 'Untuk Sekolah Dasar, SMP, SMA Negeri maupun Swasta di bawah Kemendikdasmen.',
            'config' => [
                'school_type' => 'kemdikbud',
                'school_type_badge' => 'Sekolah Kemendikbud',
                'school_name' => 'SMP NEGERI 1 CERDAS MANDIRI',
                'school_location' => 'Kota Ternate, Maluku Utara',
                'tagline' => 'Mewujudkan Generasi Unggul, Berkarakter, dan Berprestasi',
                'site_title' => 'Portal Layanan Terpadu • SMP Negeri 1 Cerdas Mandiri',
                'theme_preset' => 'ocean_sapphire',
                'primary_color' => '#0284c7',
                'secondary_color' => '#38bdf8',
                'accent_color' => '#0ea5e9',
                'bg_type' => 'gradient',
                'bg_color_start' => '#082f49',
                'bg_color_end' => '#0f172a',
                'bg_overlay_color' => '#082f49',
                'bg_gradient_angle' => '135',
                'footer_text' => 'SMP Negeri 1 Cerdas Mandiri'
            ],
            'categories' => [
                [
                    'id' => 'cat_web',
                    'name' => 'PORTAL RESMI',
                    'subname' => 'Website Sekolah & Informasi',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-globe',
                    'badge' => 'Utama',
                    'order' => 1,
                    'is_active' => true
                ],
                [
                    'id' => 'cat_dapo',
                    'name' => 'LAYANAN KEMDIKBUD',
                    'subname' => 'Dapodik, PMM & Rapor Pendidikan',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-landmark',
                    'badge' => 'Kemdikbud',
                    'order' => 2,
                    'is_active' => true
                ],
                [
                    'id' => 'cat_aka',
                    'name' => 'AKADEMIK & GURU',
                    'subname' => 'E-Rapor & Pembelajaran Digital',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-graduation-cap',
                    'badge' => 'Akademik',
                    'order' => 3,
                    'is_active' => true
                ],
                [
                    'id' => 'cat_tes',
                    'name' => 'ASESMEN & CBT',
                    'subname' => 'ANBK & Ujian Sekolah',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-laptop-code',
                    'badge' => 'Ujian',
                    'order' => 4,
                    'is_active' => true
                ]
            ],
            'shortcuts' => [
                [
                    'id' => 'sc_k1',
                    'category_id' => 'cat_web',
                    'title' => 'Website Resmi Sekolah',
                    'url' => '#',
                    'description' => 'Portal resmi berita, pengumuman, dan profil sekolah',
                    'badge' => 'Website',
                    'fa_icon' => 'fa-solid fa-school',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'id' => 'sc_k2',
                    'category_id' => 'cat_dapo',
                    'title' => 'DAPODIK Kemendikdasmen',
                    'url' => 'https://dapo.kemendikdasmen.go.id/',
                    'description' => 'Sistem Pendataan Pokok Pendidikan Nasional',
                    'badge' => 'Dapodik',
                    'fa_icon' => 'fa-solid fa-server',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'id' => 'sc_k3',
                    'category_id' => 'cat_dapo',
                    'title' => 'Platform Merdeka Mengajar (PMM)',
                    'url' => 'https://guru.kemdikbud.go.id/',
                    'description' => 'Pengembangan kompetensi guru dan kurikulum merdeka',
                    'badge' => 'Guru',
                    'fa_icon' => 'fa-solid fa-chalkboard-user',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 2
                ],
                [
                    'id' => 'sc_k4',
                    'category_id' => 'cat_dapo',
                    'title' => 'Rapor Pendidikan Indonesia',
                    'url' => 'https://raporpendidikan.kemdikbud.go.id/',
                    'description' => 'Indikator mutu dan profil capaian satuan pendidikan',
                    'badge' => 'Mutu',
                    'fa_icon' => 'fa-solid fa-chart-pie',
                    'is_pinned' => false,
                    'is_active' => true,
                    'order' => 3
                ],
                [
                    'id' => 'sc_k5',
                    'category_id' => 'cat_aka',
                    'title' => 'E-Rapor Kurikulum Merdeka',
                    'url' => '#',
                    'description' => 'Aplikasi Pengisian dan Cetak Rapor Siswa',
                    'badge' => 'Rapor',
                    'fa_icon' => 'fa-solid fa-file-signature',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'id' => 'sc_k6',
                    'category_id' => 'cat_aka',
                    'title' => 'Perpustakaan Digital (SIBI)',
                    'url' => 'https://buku.kemdikbud.go.id/',
                    'description' => 'Sistem Informasi Perbukuan Indonesia & Buku Digital',
                    'badge' => 'Literasi',
                    'fa_icon' => 'fa-solid fa-book-bookmark',
                    'is_pinned' => false,
                    'is_active' => true,
                    'order' => 2
                ],
                [
                    'id' => 'sc_k7',
                    'category_id' => 'cat_tes',
                    'title' => 'Portal ANBK Kemdikbud',
                    'url' => 'https://anbk.kemdikbud.go.id/',
                    'description' => 'Asesmen Nasional Berbasis Komputer',
                    'badge' => 'ANBK',
                    'fa_icon' => 'fa-solid fa-desktop',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'id' => 'sc_k8',
                    'category_id' => 'cat_tes',
                    'title' => 'CBT Ujian Sekolah',
                    'url' => '#',
                    'description' => 'Ujian Sumatif dan Asesmen Akhir Berbasis Komputer',
                    'badge' => 'Ujian',
                    'fa_icon' => 'fa-solid fa-laptop-code',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 2
                ]
            ]
        ],

        'smk' => [
            'key' => 'smk',
            'label' => 'SMK (Sekolah Menengah Kejuruan)',
            'badge' => 'SMK Vokasi',
            'icon' => 'fa-gears',
            'description' => 'Untuk SMK Kejuruan yang dilengkapi Bursa Kerja Khusus (BKK), Magang/PKL, dan Sertifikasi LSP.',
            'config' => [
                'school_type' => 'smk',
                'school_type_badge' => 'SMK Vokasi',
                'school_name' => 'SMK NEGERI 1 TEKNOLOGI & INOVASI',
                'school_location' => 'Kota Ternate, Maluku Utara',
                'tagline' => 'SMK Bisa! SMK Hebat! Siap Kerja, Santun, Mandiri, Kreatif',
                'site_title' => 'Portal Layanan Vokasi • SMKN 1 Teknologi & Inovasi',
                'theme_preset' => 'crimson_slate',
                'primary_color' => '#0284c7',
                'secondary_color' => '#f97316',
                'accent_color' => '#38bdf8',
                'bg_type' => 'gradient',
                'bg_color_start' => '#0b132b',
                'bg_color_end' => '#1c2541',
                'bg_overlay_color' => '#0b132b',
                'bg_gradient_angle' => '135',
                'footer_text' => 'SMKN 1 Teknologi & Inovasi'
            ],
            'categories' => [
                [
                    'id' => 'cat_smk_web',
                    'name' => 'WEBSITE & INFO',
                    'subname' => 'Portal Resmi & Jurusan',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-globe',
                    'badge' => 'Utama',
                    'order' => 1,
                    'is_active' => true
                ],
                [
                    'id' => 'cat_smk_dapo',
                    'name' => 'ADMINISTRASI & DAPODIK',
                    'subname' => 'Dapodik SMK & Kemdikbud',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-server',
                    'badge' => 'Dapodik',
                    'order' => 2,
                    'is_active' => true
                ],
                [
                    'id' => 'cat_smk_bkk',
                    'name' => 'INDUSTRI & BKK (LOKER)',
                    'subname' => 'Bursa Kerja & Magang PKL',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-briefcase',
                    'badge' => 'Karir',
                    'order' => 3,
                    'is_active' => true
                ],
                [
                    'id' => 'cat_smk_lsp',
                    'name' => 'SERTIFIKASI & CBT',
                    'subname' => 'LSP-P1, UKK & Asesmen',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-certificate',
                    'badge' => 'Vokasi',
                    'order' => 4,
                    'is_active' => true
                ]
            ],
            'shortcuts' => [
                [
                    'id' => 'sc_s1',
                    'category_id' => 'cat_smk_web',
                    'title' => 'Website Resmi SMK',
                    'url' => '#',
                    'description' => 'Profil konsentrasi keahlian, berita, dan kegiatan vokasi',
                    'badge' => 'Website',
                    'fa_icon' => 'fa-solid fa-graduation-cap',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'id' => 'sc_s2',
                    'category_id' => 'cat_smk_bkk',
                    'title' => 'Bursa Kerja Khusus (BKK)',
                    'url' => '#',
                    'description' => 'Portal informasi lowongan kerja industri dan rekrutmen alumni',
                    'badge' => 'Loker',
                    'fa_icon' => 'fa-solid fa-handshake',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'id' => 'sc_s3',
                    'category_id' => 'cat_smk_bkk',
                    'title' => 'Portal Praktik Kerja Lapangan (PKL)',
                    'url' => '#',
                    'description' => 'Sistem monitoring kehadiran dan jurnal magang industri siswa',
                    'badge' => 'PKL / Magang',
                    'fa_icon' => 'fa-solid fa-building-user',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 2
                ],
                [
                    'id' => 'sc_s4',
                    'category_id' => 'cat_smk_lsp',
                    'title' => 'Lembaga Sertifikasi Profesi (LSP)',
                    'url' => '#',
                    'description' => 'Uji Sertifikasi BNSP dan Uji Kompetensi Keahlian (UKK)',
                    'badge' => 'Sertifikasi',
                    'fa_icon' => 'fa-solid fa-stamp',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'id' => 'sc_s5',
                    'category_id' => 'cat_smk_dapo',
                    'title' => 'DAPODIK Vokasi SMK',
                    'url' => 'https://dapo.kemendikdasmen.go.id/',
                    'description' => 'Pendataan kelembagaan, sarpras bengkel, dan GTK SMK',
                    'badge' => 'Dapodik',
                    'fa_icon' => 'fa-solid fa-database',
                    'is_pinned' => false,
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'id' => 'sc_s6',
                    'category_id' => 'cat_smk_lsp',
                    'title' => 'CBT Ujian & Tes Kejuruan',
                    'url' => '#',
                    'description' => 'Ujian berbasis komputer untuk teori kejuruan dan umum',
                    'badge' => 'CBT',
                    'fa_icon' => 'fa-solid fa-desktop',
                    'is_pinned' => false,
                    'is_active' => true,
                    'order' => 2
                ]
            ]
        ],

        'pesantren' => [
            'key' => 'pesantren',
            'label' => 'Pesantren & Boarding School',
            'badge' => 'Pondok Pesantren',
            'icon' => 'fa-kaaba',
            'description' => 'Untuk Pondok Pesantren, Dayah, dan Islamic Boarding School dengan fitur Kesantrian, Asrama & Tahfidz.',
            'config' => [
                'school_type' => 'pesantren',
                'school_type_badge' => 'Pondok Pesantren',
                'school_name' => 'PONDOK PESANTREN TAHFIDZ AL-HIKMAH',
                'school_location' => 'Kota Ternate, Maluku Utara',
                'tagline' => 'Mencetak Generasi Huffadz Qur’an, Beradab, dan Berwawasan Luas',
                'site_title' => 'Portal Layanan Santri & Wali • Ponpes Tahfidz Al-Hikmah',
                'theme_preset' => 'emerald_gold',
                'primary_color' => '#047857',
                'secondary_color' => '#eab308',
                'accent_color' => '#10b981',
                'bg_type' => 'gradient',
                'bg_color_start' => '#022c22',
                'bg_color_end' => '#064e3b',
                'bg_overlay_color' => '#022c22',
                'bg_gradient_angle' => '135',
                'footer_text' => 'Pondok Pesantren Tahfidz Al-Hikmah'
            ],
            'categories' => [
                [
                    'id' => 'cat_pon_web',
                    'name' => 'INFO PESANTREN',
                    'subname' => 'Website & PSB Santri Baru',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-mosque',
                    'badge' => 'Utama',
                    'order' => 1,
                    'is_active' => true
                ],
                [
                    'id' => 'cat_pon_tahfidz',
                    'name' => 'TAHFIDZ & KESANTRIAN',
                    'subname' => 'Mutabaah, Izin & Asrama',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-quran',
                    'badge' => 'Santri',
                    'order' => 2,
                    'is_active' => true
                ],
                [
                    'id' => 'cat_pon_emis',
                    'name' => 'EMIS PONTREN',
                    'subname' => 'Kemenag & Pendataan Santri',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-landmark',
                    'badge' => 'Kemenag',
                    'order' => 3,
                    'is_active' => true
                ],
                [
                    'id' => 'cat_pon_pay',
                    'name' => 'KEUANGAN & SPP',
                    'subname' => 'Portal Wali Santri',
                    'icon_type' => 'fa',
                    'fa_icon' => 'fa-wallet',
                    'badge' => 'Wali Santri',
                    'order' => 4,
                    'is_active' => true
                ]
            ],
            'shortcuts' => [
                [
                    'id' => 'sc_p1',
                    'category_id' => 'cat_pon_web',
                    'title' => 'Website Resmi Pesantren',
                    'url' => '#',
                    'description' => 'Kajian, agenda kegiatan santri, dan profil yayasan',
                    'badge' => 'Website',
                    'fa_icon' => 'fa-solid fa-moon',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'id' => 'sc_p2',
                    'category_id' => 'cat_pon_tahfidz',
                    'title' => 'Buku Mutabaah & Tahfidz Online',
                    'url' => '#',
                    'description' => 'Monitoring setoran hafalan Qur’an santri secara real-time',
                    'badge' => 'Tahfidz',
                    'fa_icon' => 'fa-solid fa-quran',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'id' => 'sc_p3',
                    'category_id' => 'cat_pon_tahfidz',
                    'title' => 'Sistem Perizinan & Disiplin Santri',
                    'url' => '#',
                    'description' => 'Pengajuan izin pulang dan catatan kedisiplinan santri asrama',
                    'badge' => 'Asrama',
                    'fa_icon' => 'fa-solid fa-id-card-clip',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 2
                ],
                [
                    'id' => 'sc_p4',
                    'category_id' => 'cat_pon_pay',
                    'title' => 'Pembayaran SPP & Syahriah',
                    'url' => '#',
                    'description' => 'Portal pembayaran iuran bulanan dan tabungan santri',
                    'badge' => 'Wali Santri',
                    'fa_icon' => 'fa-solid fa-money-bill-wave',
                    'is_pinned' => true,
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'id' => 'sc_p5',
                    'category_id' => 'cat_pon_emis',
                    'title' => 'EMIS Pontren Kemenag RI',
                    'url' => 'https://emis.kemenag.go.id/',
                    'description' => 'Pangkalan Data Pendidikan Keagamaan Islam Kemenag',
                    'badge' => 'Kemenag',
                    'fa_icon' => 'fa-solid fa-building-columns',
                    'is_pinned' => false,
                    'is_active' => true,
                    'order' => 1
                ]
            ]
        ]
    ];
}
