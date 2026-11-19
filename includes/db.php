<?php
/**
 * Database Helper - Atomic JSON Store
 * Safe, fast, zero-configuration persistence compatible with PHP 8.2+
 */

defined('DATA_PATH') or define('DATA_PATH', __DIR__ . '/../data');

function json_read($filename, $default = []) {
    $filepath = DATA_PATH . '/' . $filename;
    if (!file_exists($filepath)) {
        return $default;
    }
    $content = file_get_contents($filepath);
    if ($content === false || trim($content) === '') {
        return $default;
    }
    $data = json_decode($content, true);
    return (json_last_error() === JSON_ERROR_NONE) ? $data : $default;
}

function json_write($filename, $data) {
    if (!is_dir(DATA_PATH)) {
        mkdir(DATA_PATH, 0755, true);
    }
    $filepath = DATA_PATH . '/' . $filename;
    $tempFile = $filepath . '.' . uniqid('tmp_', true);
    
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }

    $fp = fopen($tempFile, 'w');
    if (!$fp) return false;

    if (flock($fp, LOCK_EX)) {
        fwrite($fp, $json);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        return rename($tempFile, $filepath);
    } else {
        fclose($fp);
        @unlink($tempFile);
        return false;
    }
}

// Config getters & setters
function get_config() {
    return json_read('config.json', []);
}

function save_config($data) {
    return json_write('config.json', $data);
}

// Categories
function get_categories($only_active = false) {
    $cats = json_read('categories.json', []);
    if ($only_active) {
        $cats = array_filter($cats, fn($c) => !empty($c['is_active']));
    }
    usort($cats, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
    return array_values($cats);
}

function save_categories($data) {
    return json_write('categories.json', $data);
}

// Shortcuts
function get_shortcuts($only_active = false, $category_id = null) {
    $items = json_read('shortcuts.json', []);
    if ($only_active) {
        $items = array_filter($items, fn($i) => !empty($i['is_active']));
    }
    if ($category_id !== null) {
        $items = array_filter($items, fn($i) => ($i['category_id'] ?? '') === $category_id);
    }
    usort($items, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
    return array_values($items);
}

function save_shortcuts($data) {
    return json_write('shortcuts.json', $data);
}

// Sliders
function get_sliders($only_active = false) {
    $sliders = json_read('sliders.json', []);
    if ($only_active) {
        $sliders = array_filter($sliders, fn($s) => !empty($s['is_active']));
    }
    usort($sliders, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
    return array_values($sliders);
}

function save_sliders($data) {
    return json_write('sliders.json', $data);
}

// Admin
function get_admin() {
    return json_read('admin.json', []);
}

function save_admin($data) {
    return json_write('admin.json', $data);
}

// Click counter
function increment_shortcut_click($shortcut_id) {
    $shortcuts = get_shortcuts();
    $found = false;
    foreach ($shortcuts as &$sc) {
        if (($sc['id'] ?? '') === $shortcut_id) {
            $sc['clicks'] = ($sc['clicks'] ?? 0) + 1;
            $found = true;
            break;
        }
    }
    if ($found) {
        save_shortcuts($shortcuts);
        return true;
    }
    return false;
}
