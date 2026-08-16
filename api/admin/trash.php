<?php
require_once '../config/config.php';
require_once '../config/Database.php';
require_once 'audit_helper.php';
$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$db = Database::getInstance()->getConnection();

if ($action === 'list') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }

    try {
        $data = [
            'apps' => [],
            'matches' => [],
            'leagues' => [],
            'teams' => [],
            'users' => []
        ];

        // Fetch deleted apps
        $stmt = $db->query("SELECT a.id, a.name as title, b.name as brand_name, a.created_at FROM apps a LEFT JOIN brands b ON a.brand_id = b.id WHERE a.is_deleted = 1");
        $data['apps'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch deleted matches
        $stmt = $db->query("SELECT id, match_title as title, match_date as created_at FROM matches WHERE is_deleted = 1");
        $data['matches'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch deleted leagues (leagues doesn't have country or created_at)
        $stmt = $db->query("SELECT id, name as title, NULL as brand_name, NULL as created_at FROM leagues WHERE is_deleted = 1");
        $data['leagues'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch deleted teams (teams doesn't have is_deleted, so we return empty)
        // Eğer teams için soft-delete eklenecekse burası güncellenir.
        $data['teams'] = [];

        // Fetch deleted users
        $stmt = $db->query("SELECT id, name as title, email as brand_name, created_at, COALESCE(deleted_by_user, 0) as deleted_by_user FROM users WHERE is_deleted = 1");
        $data['users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'success', 'data' => $data]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
elseif ($action === 'restore') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $type = $input['type'] ?? '';
    $id = $input['id'] ?? null;

    $allowed_tables = ['app' => 'apps', 'match' => 'matches', 'league' => 'leagues', 'team' => 'teams', 'user' => 'users'];

    if (!$id || !isset($allowed_tables[$type])) {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz veya eksik veri.']);
        exit;
    }

    try {
        $table = $allowed_tables[$type];
        $stmt = $db->prepare("UPDATE $table SET is_deleted = 0 WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        $nameField = ($type === 'user') ? 'name' : (($type === 'match') ? 'match_title' : 'name');
        $nameStmt = $db->prepare("SELECT $nameField FROM $table WHERE id = ?");
        $nameStmt->execute([$id]);
        $item_name = $nameStmt->fetchColumn() ?: 'Bilinmeyen Öğe';
        
        log_action($db, 'restore_from_trash', ucfirst($type) . " ID: $id ($item_name) restored.");
        echo json_encode(['status' => 'success', 'message' => 'Kayıt başarıyla geri yüklendi.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Geri yükleme sırasında hata: ' . $e->getMessage()]);
    }
}
elseif ($action === 'hard_delete') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $type = $input['type'] ?? '';
    $id = $input['id'] ?? null;

    $allowed_tables = ['app' => 'apps', 'match' => 'matches', 'league' => 'leagues', 'team' => 'teams', 'user' => 'users'];

    if (!$id || !isset($allowed_tables[$type])) {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz veya eksik veri.']);
        exit;
    }

    try {
        $table = $allowed_tables[$type];
        // Note: Due to foreign keys, deleting users might fail if there are records in user_apps.
        // It's best to cascade or handle them manually.
        
        $nameField = ($type === 'user') ? 'name' : (($type === 'match') ? 'match_title' : 'name');
        $nameStmt = $db->prepare("SELECT $nameField FROM $table WHERE id = ?");
        $nameStmt->execute([$id]);
        $item_name = $nameStmt->fetchColumn() ?: 'Bilinmeyen Öğe';
        
        $db->beginTransaction();
        
        if ($table === 'users') {
            $stmt1 = $db->prepare("DELETE FROM user_apps WHERE user_id = :id");
            $stmt1->execute([':id' => $id]);
        }
        
        $stmt = $db->prepare("DELETE FROM $table WHERE id = :id AND is_deleted = 1");
        $stmt->execute([':id' => $id]);
        
        $db->commit();
        
        log_action($db, 'hard_delete', ucfirst($type) . " ID: $id ($item_name) permanently deleted.");
        echo json_encode(['status' => 'success', 'message' => 'Kayıt kalıcı olarak silindi.']);
    } catch (PDOException $e) {
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Silme işlemi sırasında hata: ' . $e->getMessage()]);
    }
}
elseif ($action === 'bulk_restore') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $type = $input['type'] ?? '';
    $ids = $input['ids'] ?? [];

    $allowed_tables = ['app' => 'apps', 'match' => 'matches', 'league' => 'leagues', 'team' => 'teams', 'user' => 'users'];

    if (empty($ids) || !is_array($ids) || !isset($allowed_tables[$type])) {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz veya eksik veri.']);
        exit;
    }

    try {
        $table = $allowed_tables[$type];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $db->prepare("UPDATE $table SET is_deleted = 0 WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        
        log_action($db, 'bulk_restore_from_trash', count($ids) . " " . ucfirst($type) . "(s) restored.");
        echo json_encode(['status' => 'success', 'message' => count($ids) . ' adet kayıt başarıyla geri yüklendi.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Geri yükleme sırasında hata: ' . $e->getMessage()]);
    }
}
elseif ($action === 'bulk_hard_delete') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $type = $input['type'] ?? '';
    $ids = $input['ids'] ?? [];

    $allowed_tables = ['app' => 'apps', 'match' => 'matches', 'league' => 'leagues', 'team' => 'teams', 'user' => 'users'];

    if (empty($ids) || !is_array($ids) || !isset($allowed_tables[$type])) {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz veya eksik veri.']);
        exit;
    }

    try {
        $table = $allowed_tables[$type];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $db->beginTransaction();
        
        if ($table === 'users') {
            $stmt1 = $db->prepare("DELETE FROM user_apps WHERE user_id IN ($placeholders)");
            $stmt1->execute($ids);
        }
        
        $stmt = $db->prepare("DELETE FROM $table WHERE id IN ($placeholders) AND is_deleted = 1");
        $stmt->execute($ids);
        
        $db->commit();
        
        log_action($db, 'bulk_hard_delete', count($ids) . " " . ucfirst($type) . "(s) permanently deleted.");
        echo json_encode(['status' => 'success', 'message' => count($ids) . ' adet kayıt kalıcı olarak silindi.']);
    } catch (PDOException $e) {
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Silme işlemi sırasında hata: ' . $e->getMessage()]);
    }
}
elseif ($action === 'trash_count') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }
    try {
        $total = 0;
        $total += $db->query("SELECT COUNT(id) FROM apps WHERE is_deleted = 1")->fetchColumn();
        $total += $db->query("SELECT COUNT(id) FROM matches WHERE is_deleted = 1")->fetchColumn();
        $total += $db->query("SELECT COUNT(id) FROM leagues WHERE is_deleted = 1")->fetchColumn();
        $total += $db->query("SELECT COUNT(id) FROM users WHERE is_deleted = 1")->fetchColumn();
        
        echo json_encode(['status' => 'success', 'count' => $total]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
else {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz action parametresi.']);
}
?>
