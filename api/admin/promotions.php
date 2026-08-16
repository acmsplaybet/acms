<?php
require_once '../config/config.php';
require_once '../config/Database.php';
require_once 'audit_helper.php';

// JSON body POST isteklerinde action $_POST'ta görünmez, php://input'tan okumak gerekir
$rawInput = file_get_contents('php://input');
$jsonInput = $rawInput ? json_decode($rawInput, true) : null;
$action = $_GET['action'] ?? ($jsonInput['action'] ?? ($_POST['action'] ?? ''));
$db = Database::getInstance()->getConnection();

if ($action === 'list') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }
    try {
        $stmt = $db->query("
            SELECT p.id, p.app_id, a.name as app_name, p.title, p.description, p.badge_text, p.end_date, p.status, p.created_at 
            FROM promotions p
            LEFT JOIN apps a ON p.app_id = a.id
            WHERE p.is_deleted = 0
            ORDER BY p.id DESC
        ");
        $promos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $promos]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
elseif ($action === 'save') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }
    
    $input = $jsonInput ?: $_POST;

    $id = $input['id'] ?? null;
    $app_id = $input['app_id'] ?? null;
    $title = trim($input['title'] ?? '');
    $description = trim($input['description'] ?? '');
    $badge_text = trim($input['badge_text'] ?? '');
    $end_date = $input['end_date'] ?? null;
    $status = $input['status'] ?? 'active';

    if (empty($app_id) || empty($title) || empty($end_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Uygulama, Başlık ve Bitiş Tarihi alanları zorunludur.']);
        exit;
    }

    try {
        if (empty($id)) {
            $stmt = $db->prepare("INSERT INTO promotions (app_id, title, description, badge_text, end_date, status) VALUES (:app_id, :title, :description, :badge_text, :end_date, :status)");
            $stmt->execute([
                ':app_id' => $app_id,
                ':title' => $title,
                ':description' => $description,
                ':badge_text' => $badge_text,
                ':end_date' => $end_date,
                ':status' => $status
            ]);
            $new_id = $db->lastInsertId();
            log_action($db, 'add_promotion', "Promosyon eklendi ID: $new_id, App ID: $app_id");
            echo json_encode(['status' => 'success', 'message' => 'Promosyon başarıyla eklendi.']);
        } else {
            $stmt = $db->prepare("UPDATE promotions SET app_id = :app_id, title = :title, description = :description, badge_text = :badge_text, end_date = :end_date, status = :status WHERE id = :id");
            $stmt->execute([
                ':app_id' => $app_id,
                ':title' => $title,
                ':description' => $description,
                ':badge_text' => $badge_text,
                ':end_date' => $end_date,
                ':status' => $status,
                ':id' => $id
            ]);
            log_action($db, 'update_promotion', "Promosyon güncellendi ID: $id, App ID: $app_id");
            echo json_encode(['status' => 'success', 'message' => 'Promosyon başarıyla güncellendi.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
elseif ($action === 'delete') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }

    $id = ($jsonInput ?? [])['id'] ?? null;

    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'ID zorunludur.']);
        exit;
    }

    try {
        $stmt = $db->prepare("UPDATE promotions SET is_deleted = 1 WHERE id = :id");
        $stmt->execute([':id' => $id]);
        log_action($db, 'delete_promotion', "Promosyon silindi (Soft Delete) ID: $id");
        echo json_encode(['status' => 'success', 'message' => 'Promosyon silindi.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
else {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz action parametresi.']);
}
