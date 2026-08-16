<?php
require_once '../config/config.php';
require_once '../config/Database.php';
require_once 'audit_helper.php';

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
        $statusFilter = $_GET['status'] ?? '';
        
        $sql = "
            SELECT t.id, t.user_id, u.name as user_name, t.app_id, a.name as app_name, 
                   t.subject, t.category, t.status, t.created_at, t.updated_at
            FROM tickets t
            LEFT JOIN users u ON t.user_id = u.id
            LEFT JOIN apps a ON t.app_id = a.id
            WHERE t.is_deleted = 0
        ";
        $params = [];
        if (!empty($statusFilter)) {
            $sql .= " AND t.status = :status";
            $params[':status'] = $statusFilter;
        }
        $sql .= " ORDER BY t.id DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'success', 'data' => $tickets]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
elseif ($action === 'get') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Ticket ID zorunludur.']);
        exit;
    }
    
    try {
        $stmt = $db->prepare("
            SELECT t.*, u.name as user_name, a.name as app_name
            FROM tickets t
            LEFT JOIN users u ON t.user_id = u.id
            LEFT JOIN apps a ON t.app_id = a.id
            WHERE t.id = :id AND t.is_deleted = 0
        ");
        $stmt->execute([':id' => $id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($ticket) {
            echo json_encode(['status' => 'success', 'data' => $ticket]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Ticket bulunamadı.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
elseif ($action === 'reply') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }
    
    $input = $jsonInput ?: $_POST;
    $id = $input['id'] ?? null;
    $admin_reply = trim($input['admin_reply'] ?? '');
    
    if (!$id || empty($admin_reply)) {
        echo json_encode(['status' => 'error', 'message' => 'Ticket ID ve Yanıt metni zorunludur.']);
        exit;
    }
    
    try {
        $stmt = $db->prepare("UPDATE tickets SET admin_reply = :admin_reply, status = 'closed', admin_reply_at = NOW(), updated_at = NOW() WHERE id = :id");
        $stmt->execute([
            ':admin_reply' => $admin_reply,
            ':id' => $id
        ]);
        log_action($db, 'reply_ticket', "Ticket yanıtlandı ve kapatıldı. ID: $id");
        echo json_encode(['status' => 'success', 'message' => 'Yanıt başarıyla gönderildi ve talep çözüldü.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
elseif ($action === 'update_status') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }
    
    $input = $jsonInput ?: $_POST;
    $id = $input['id'] ?? null;
    $status = $input['status'] ?? null;
    
    if (!$id || !$status) {
        echo json_encode(['status' => 'error', 'message' => 'ID ve Statü zorunludur.']);
        exit;
    }
    
    try {
        $stmt = $db->prepare("UPDATE tickets SET status = :status, updated_at = NOW() WHERE id = :id");
        $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
        log_action($db, 'update_ticket_status', "Ticket statüsü güncellendi. ID: $id, Yeni Statü: $status");
        echo json_encode(['status' => 'success', 'message' => 'Statü başarıyla güncellendi.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
elseif ($action === 'delete') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }

    $input = $jsonInput ?: $_POST;
    $id = $input['id'] ?? null;

    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'ID zorunludur.']);
        exit;
    }

    try {
        $stmt = $db->prepare("UPDATE tickets SET is_deleted = 1, updated_at = NOW() WHERE id = :id");
        $stmt->execute([':id' => $id]);
        log_action($db, 'delete_ticket', "Ticket silindi (Soft Delete) ID: $id");
        echo json_encode(['status' => 'success', 'message' => 'Destek talebi silindi.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
elseif ($action === 'count') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }
    try {
        $stmt = $db->query("SELECT COUNT(id) as cnt FROM tickets WHERE status = 'open' AND is_deleted = 0");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $res['cnt']]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
else {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz action parametresi.']);
}
