<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

$q = strtolower(trim($_GET['q'] ?? ''));
if (strlen($q) < 1) {
    echo json_encode(['results' => []]);
    exit;
}

$shortcuts = get_shortcuts(true);
$categories = get_categories();
$catMap = [];
foreach ($categories as $cat) {
    $catMap[$cat['id']] = $cat['name'];
}

$results = [];
foreach ($shortcuts as $sc) {
    $title = strtolower($sc['title'] ?? '');
    $desc = strtolower($sc['description'] ?? '');
    $badge = strtolower($sc['badge'] ?? '');
    $catName = strtolower($catMap[$sc['category_id'] ?? ''] ?? '');

    if (str_contains($title, $q) || str_contains($desc, $q) || str_contains($badge, $q) || str_contains($catName, $q)) {
        $results[] = [
            'id' => $sc['id'],
            'title' => $sc['title'],
            'url' => $sc['url'],
            'description' => $sc['description'] ?? '',
            'badge' => $sc['badge'] ?? '',
            'category' => $catMap[$sc['category_id'] ?? ''] ?? 'Umum',
            'icon' => $sc['fa_icon'] ?? 'fa-solid fa-link',
            'target' => $sc['target'] ?? '_blank'
        ];
    }
}

echo json_encode(['results' => array_slice($results, 0, 10)]);
