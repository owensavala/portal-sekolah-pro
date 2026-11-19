<?php
/**
 * View & Utility Helpers
 */

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSS for body background based on admin config
 */
function generate_background_css($config, $prefix = '') {
    $type = $config['bg_type'] ?? 'gradient';
    $css = "";

    switch ($type) {
        case 'solid':
            $solid = $config['bg_solid_color'] ?? '#0f172a';
            $css = "background-color: {$solid};";
            break;

        case 'image':
            $img = $config['bg_image'] ?? '';
            $opacity = floatval($config['bg_overlay_opacity'] ?? 0.8);
            $overlayColor = $config['bg_overlay_color'] ?? ($config['bg_color_start'] ?? '#0f172a');
            $rgb = hex_to_rgb($overlayColor);
            if (!empty($img)) {
                if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://') || str_starts_with($img, '/')) {
                    $imgPath = $img;
                } else {
                    $imgPath = '/' . ltrim($img, '/');
                }
                $css = "background: linear-gradient(rgba({$rgb}, {$opacity}), rgba({$rgb}, {$opacity})), url('{$imgPath}') no-repeat center center fixed; background-size: cover;";
            } else {
                $start = $config['bg_color_start'] ?? '#082f49';
                $end = $config['bg_color_end'] ?? '#0f172a';
                $css = "background: linear-gradient(135deg, {$start} 0%, {$end} 100%); background-attachment: fixed;";
            }
            break;

        case 'gradient':
        default:
            $angle = intval($config['bg_gradient_angle'] ?? 135);
            $start = $config['bg_color_start'] ?? '#032b17';
            $end = $config['bg_color_end'] ?? '#0b532c';
            $css = "background: linear-gradient({$angle}deg, {$start} 0%, {$end} 100%); background-attachment: fixed;";
            break;
    }

    return $css;
}

/**
 * Generate dynamic CSS variables & classes for color scheme and animations
 */
function generate_theme_variables($config) {
    $primary = $config['primary_color'] ?? '#0284c7';
    $secondary = $config['secondary_color'] ?? '#38bdf8';
    $accent = $config['accent_color'] ?? '#0ea5e9';

    $pRGB = hex_to_rgb($primary);
    $sRGB = hex_to_rgb($secondary);
    $aRGB = hex_to_rgb($accent);

    return "
        :root {
            --primary: {$primary};
            --primary-rgb: {$pRGB};
            --secondary: {$secondary};
            --secondary-rgb: {$sRGB};
            --accent: {$accent};
            --accent-rgb: {$aRGB};
        }

        /* Dynamic Theme Colors */
        .theme-accent-text { color: var(--secondary) !important; }
        .theme-primary-text { color: var(--primary) !important; }
        .theme-accent-bg { background-color: var(--secondary) !important; }
        .theme-primary-bg { background-color: var(--primary) !important; }
        .theme-accent-border { border-color: var(--secondary) !important; }

        /* Glowing Dynamic Card Hover */
        .glass-card-dynamic {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            transform-style: preserve-3d;
        }
        .glass-card-dynamic:hover {
            transform: translateY(-8px) scale(1.02);
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(var(--secondary-rgb), 0.6);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3), 0 0 25px rgba(var(--secondary-rgb), 0.35);
        }
        .glass-card-dynamic:hover .card-icon-glow {
            transform: scale(1.15) rotate(4deg);
            filter: drop-shadow(0 0 12px var(--secondary));
            color: var(--secondary);
        }
        .glass-card-dynamic:hover .card-action-btn {
            background-color: var(--secondary);
            color: #0f172a;
            box-shadow: 0 4px 15px rgba(var(--secondary-rgb), 0.4);
        }

        /* Dynamic Filter Tab */
        .filter-tab.active {
            background-color: var(--secondary) !important;
            color: #0f172a !important;
            font-weight: 800 !important;
            border-color: transparent !important;
            box-shadow: 0 4px 15px rgba(var(--secondary-rgb), 0.5) !important;
        }
        .filter-tab.active i {
            color: #0f172a !important;
        }

        /* Dynamic Badge */
        .theme-badge-dynamic {
            background: rgba(var(--secondary-rgb), 0.2);
            color: var(--secondary);
            border: 1px solid rgba(var(--secondary-rgb), 0.35);
        }

        /* Dynamic Button */
        .btn-dynamic-accent {
            background: var(--secondary);
            color: #0f172a;
            box-shadow: 0 4px 15px rgba(var(--secondary-rgb), 0.3);
        }
        .btn-dynamic-accent:hover {
            background: var(--accent);
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(var(--accent-rgb), 0.45);
        }
    ";
}

function hex_to_rgb($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } elseif (strlen($hex) == 6) {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    } else {
        return "14, 165, 233";
    }
    return "{$r}, {$g}, {$b}";
}

/**
 * Handle secure file upload with deep security checks (MIME, double-extension, XSS sanitize)
 */
function handle_file_upload($file_array, $allowed_ext = ['png', 'jpg', 'jpeg', 'webp', 'svg', 'ico'], $max_size = 5242880) {
    if (empty($file_array) || $file_array['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Tidak ada file yang diunggah atau terjadi kesalahan upload.'];
    }

    if ($file_array['size'] > $max_size) {
        return ['success' => false, 'error' => 'Ukuran file melebihi batas maksimal (5MB).'];
    }

    $originalName = $file_array['name'];
    
    // Security 1: Block double extensions (e.g. test.php.png or test.phtml.jpg)
    $nameParts = explode('.', strtolower($originalName));
    if (count($nameParts) > 2) {
        $dangerousExts = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'phps', 'cgi', 'pl', 'py', 'sh', 'bash', 'exe', 'bat'];
        for ($i = 0; $i < count($nameParts) - 1; $i++) {
            if (in_array($nameParts[$i], $dangerousExts)) {
                return ['success' => false, 'error' => 'Percobaan upload file mencurigakan (double extension) diblokir demi keamanan.'];
            }
        }
    }

    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) {
        return ['success' => false, 'error' => 'Format file tidak diizinkan. Hanya (' . implode(', ', $allowed_ext) . ')'];
    }

    // Security 2: Validate MIME-type
    $tmpFile = $file_array['tmp_name'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $tmpFile);
    finfo_close($finfo);

    $allowedMimes = [
        'png'  => ['image/png', 'image/x-png'],
        'jpg'  => ['image/jpeg', 'image/pjpeg'],
        'jpeg' => ['image/jpeg', 'image/pjpeg'],
        'webp' => ['image/webp'],
        'svg'  => ['image/svg+xml', 'text/plain', 'text/xml'],
        'ico'  => ['image/x-icon', 'image/vnd.microsoft.icon', 'image/ico']
    ];

    if (isset($allowedMimes[$ext]) && !in_array($mimeType, $allowedMimes[$ext])) {
        return ['success' => false, 'error' => 'Konten file tidak sesuai dengan ekstensi gambar yang diizinkan.'];
    }

    // Security 3: SVG XSS sanitizer (check for malicious script tags)
    if ($ext === 'svg') {
        $svgContent = @file_get_contents($tmpFile);
        if ($svgContent !== false) {
            $dangerousPatterns = ['/<script/i', '/javascript\s*:/i', '/onerror\s*=/i', '/onload\s*=/i', '/onclick\s*=/i', '/<iframe/i'];
            foreach ($dangerousPatterns as $pattern) {
                if (preg_match($pattern, $svgContent)) {
                    return ['success' => false, 'error' => 'File SVG mengandung skrip yang tidak diizinkan (XSS Protection).'];
                }
            }
        }
    }

    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $safeName = uniqid('up_') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetPath = $uploadDir . $safeName;

    if (move_uploaded_file($tmpFile, $targetPath)) {
        return ['success' => true, 'path' => 'uploads/' . $safeName];
    } else {
        return ['success' => false, 'error' => 'Gagal memindahkan file ke direktori uploads.'];
    }
}

/**
 * Toast / Alert Message Session helpers
 */
function set_flash_message($type, $message) {
    $_SESSION['flash_msg'] = [
        'type' => $type,
        'message' => $message
    ];
}

function get_flash_message() {
    if (!empty($_SESSION['flash_msg'])) {
        $msg = $_SESSION['flash_msg'];
        unset($_SESSION['flash_msg']);
        return $msg;
    }
    return null;
}
