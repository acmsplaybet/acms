<?php
require_once '../config/Database.php';

header('Content-Type: application/json; charset=utf-8');

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';
    if ($action === 'list') {
        $stmt = $db->query("SELECT * FROM leagues WHERE is_deleted = 0 ORDER BY name ASC");
        echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true) ?: $_POST;
    $action = $data['action'] ?? '';

    if ($action === 'save') {
        $id = intval($data['id'] ?? 0);
        $name = trim($data['name'] ?? '');
        $logo_url = trim($data['logo_url'] ?? '');
        
        if (empty($name)) {
            echo json_encode(['status' => 'error', 'message' => 'Lig adı zorunludur.']);
            exit;
        }

        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($name));

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE leagues SET name = ?, slug = ?, logo_url = ? WHERE id = ?");
            try {
                $stmt->execute([$name, $slug, $logo_url, $id]);
                echo json_encode(['status' => 'success', 'message' => 'Lig güncellendi.']);
            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => 'Hata: ' . $e->getMessage()]);
            }
        } else {
            $stmt = $db->prepare("INSERT INTO leagues (name, slug, logo_url) VALUES (?, ?, ?)");
            try {
                $stmt->execute([$name, $slug, $logo_url]);
                echo json_encode(['status' => 'success', 'message' => 'Lig eklendi.']);
            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => 'Hata: ' . $e->getMessage()]);
            }
        }
        exit;
    }

    if ($action === 'delete') {
        $id = intval($data['id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("UPDATE leagues SET is_deleted = 1 WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'Lig silindi.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Geçersiz ID']);
        }
        exit;
    }

    if ($action === 'bulk_delete') {
        $ids = $data['ids'] ?? [];
        if (is_array($ids) && count($ids) > 0) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $db->prepare("UPDATE leagues SET is_deleted = 1 WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            echo json_encode(['status' => 'success', 'message' => count($ids) . ' adet lig silindi.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Geçersiz ID listesi']);
        }
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek.']);
?>
