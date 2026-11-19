<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

$id = $_GET['id'] ?? $_POST['id'] ?? '';
if (!empty($id)) {
    $ok = increment_shortcut_click($id);
    echo json_encode(['success' => $ok]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Missing ID']);
