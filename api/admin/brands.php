<?php
require_once '../config/Database.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'DB error']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';

if ($action === 'list') {
    $stmt = $db->query("SELECT b.*, COUNT(a.id) as app_count FROM brands b LEFT JOIN apps a ON a.brand_id = b.id AND a.is_deleted = 0 GROUP BY b.id ORDER BY b.id ASC");
    echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}
elseif ($action === 'create') {
    $name          = trim($_POST['name'] ?? '');
    $slug          = trim($_POST['slug'] ?? '');
    $default_theme = trim($_POST['default_theme'] ?? 'real');
    $description   = trim($_POST['description'] ?? '');
    if (!$name || !$slug) { echo json_encode(['status'=>'error','message'=>'Ad ve slug zorunlu']); exit; }
    $stmt = $db->prepare("INSERT INTO brands (name, slug, default_theme, description, is_active) VALUES (?, ?, ?, ?, 1)");
    $stmt->execute([$name, $slug, $default_theme, $description]);
    echo json_encode(['status' => 'success', 'id' => $db->lastInsertId()]);
}
elseif ($action === 'update') {
    $id            = intval($_POST['id']);
    $name          = trim($_POST['name'] ?? '');
    $slug          = trim($_POST['slug'] ?? '');
    $default_theme = trim($_POST['default_theme'] ?? 'real');
    $description   = trim($_POST['description'] ?? '');
    $is_active     = isset($_POST['is_active']) ? 1 : 0;
    $stmt = $db->prepare("UPDATE brands SET name=?, slug=?, default_theme=?, description=?, is_active=? WHERE id=?");
    $stmt->execute([$name, $slug, $default_theme, $description, $is_active, $id]);
    echo json_encode(['status' => 'success']);
}
elseif ($action === 'delete') {
    $id = intval($_POST['id']);
    $check = $db->prepare("SELECT COUNT(*) FROM apps WHERE brand_id = ? AND is_deleted = 0");
    $check->execute([$id]);
    if ($check->fetchColumn() > 0) {
        echo json_encode(['status'=>'error','message'=>'Bu markaya bağlı uygulamalar var. Önce uygulamaları silin veya başka markaya taşıyın.']);
        exit;
    }
    $db->prepare("DELETE FROM brands WHERE id = ?")->execute([$id]);
    echo json_encode(['status' => 'success']);
}
