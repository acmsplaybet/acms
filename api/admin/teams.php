<?php
require_once '../config/Database.php';

header('Content-Type: application/json; charset=utf-8');

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';
    if ($action === 'list') {
        $where = "t.is_deleted = 0";
        $params = [];
        if (!empty($_GET['league_id'])) {
            $where .= " AND (t.league_id = ? OR t.league_id IS NULL)";
            $params[] = intval($_GET['league_id']);
        }
        
        $stmt = $db->prepare("
            SELECT t.*, l.name as league_name 
            FROM teams t 
            LEFT JOIN leagues l ON t.league_id = l.id
            WHERE $where 
            ORDER BY t.name ASC
        ");
        $stmt->execute($params);
        echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true) ?: $_POST;
    $action = $data['action'] ?? '';

    if ($action === 'save') {
        $id = intval($data['id'] ?? 0);
        $league_id = intval($data['league_id'] ?? 0);
        $name = trim($data['name'] ?? '');
        $logo_url = trim($data['logo_url'] ?? '');
        
        if (empty($name)) {
            echo json_encode(['status' => 'error', 'message' => 'Takım adı zorunludur.']);
            exit;
        }

        $league_id_val = $league_id > 0 ? $league_id : null;
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($name));
        if ($id > 0) {
            $stmt = $db->prepare("UPDATE teams SET name = ?, slug = ?, logo_url = ?, league_id = ? WHERE id = ?");
            try {
                $stmt->execute([$name, $slug, $logo_url, $league_id_val, $id]);
                echo json_encode(['status' => 'success', 'message' => 'Takım güncellendi.']);
            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => 'Hata: ' . $e->getMessage()]);
            }
        } else {
            $stmt = $db->prepare("INSERT INTO teams (name, slug, logo_url, league_id) VALUES (?, ?, ?, ?)");
            try {
                $stmt->execute([$name, $slug, $logo_url, $league_id_val]);
                echo json_encode(['status' => 'success', 'message' => 'Takım eklendi.']);
            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => 'Hata: ' . $e->getMessage()]);
            }
        }
        exit;
    }

    if ($action === 'delete') {
        $id = intval($data['id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("UPDATE teams SET is_deleted = 1 WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'Takım silindi.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Geçersiz ID']);
        }
        exit;
    }

    if ($action === 'bulk_delete') {
        $ids = $data['ids'] ?? [];
        if (is_array($ids) && count($ids) > 0) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $db->prepare("UPDATE teams SET is_deleted = 1 WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            echo json_encode(['status' => 'success', 'message' => count($ids) . ' adet takım silindi.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Geçersiz ID listesi']);
        }
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek.']);
?>
