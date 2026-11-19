<?php
/**
 * Vercel Serverless PHP Front Controller & Router
 * Dispatches requests to the appropriate PHP scripts or serves them cleanly.
 */

$rootDir = dirname(__DIR__);
chdir($rootDir);

$rawUri = $_SERVER['REQUEST_URI'] ?? '/';
$uriPath = parse_url($rawUri, PHP_URL_PATH) ?? '/';
$uri = trim($uriPath, '/');

// 1. Root / Home
if ($uri === '' || $uri === 'index.php') {
    require $rootDir . '/index.php';
    exit;
}

// 2. Admin routes
if (str_starts_with($uri, 'admin')) {
    $sub = substr($uri, 5); // remove 'admin'
    $sub = trim($sub, '/');
    if ($sub === '' || $sub === 'index.php') {
        require $rootDir . '/admin/index.php';
        exit;
    }
    
    $target = $rootDir . '/admin/' . $sub;
    if (file_exists($target) && !is_dir($target)) {
        require $target;
        exit;
    }
    if (file_exists($target . '.php')) {
        require $target . '.php';
        exit;
    }
}

// 3. API routes
if (str_starts_with($uri, 'api/')) {
    $sub = substr($uri, 4);
    $sub = trim($sub, '/');
    $target = __DIR__ . '/' . $sub;
    if (file_exists($target) && !is_dir($target)) {
        require $target;
        exit;
    }
    if (file_exists($target . '.php')) {
        require $target . '.php';
        exit;
    }
}

// 4. Direct file in root
$target = $rootDir . '/' . $uri;
if (file_exists($target) && !is_dir($target)) {
    $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
    if ($ext === 'php') {
        require $target;
        exit;
    }
    
    // Serve static asset with appropriate Content-Type header
    $mimes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'json'  => 'application/json',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'webp'  => 'image/webp',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2'
    ];
    if (isset($mimes[$ext])) {
        header('Content-Type: ' . $mimes[$ext]);
        readfile($target);
        exit;
    }
}

// Default fallback
require $rootDir . '/index.php';
