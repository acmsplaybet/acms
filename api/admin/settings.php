<?php
require_once '../config/config.php';
require_once '../config/Database.php';
require_once 'audit_helper.php';

$rawInput = file_get_contents('php://input');
$jsonInput = $rawInput ? json_decode($rawInput, true) : null;
$action = $_GET['action'] ?? ($jsonInput['action'] ?? ($_POST['action'] ?? ''));

$db = Database::getInstance()->getConnection();

if ($action === 'get_smtp') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }
    try {
        $keys = ['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_from_name', 'smtp_from_email', 'smtp_encryption'];
        $placeholders = implode(',', array_fill(0, count($keys), '?'));
        
        $stmt = $db->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ($placeholders)");
        $stmt->execute($keys);
        
        $data = [];
        foreach ($keys as $k) {
            $data[$k] = ''; // Default boş string
        }
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[$row['setting_key']] = $row['setting_value'];
        }
        
        echo json_encode(['status' => 'success', 'data' => $data]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
elseif ($action === 'save_smtp') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }
    
    $input = $jsonInput ?: $_POST;
    $keys = ['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_from_name', 'smtp_from_email', 'smtp_encryption'];
    
    try {
        $db->beginTransaction();
        
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :val) ON DUPLICATE KEY UPDATE setting_value = :val2");
        
        foreach ($keys as $k) {
            $val = $input[$k] ?? '';
            if ($k === 'smtp_password' && empty($val)) {
                continue; 
            }
            $stmt->execute([
                ':key' => $k,
                ':val' => $val,
                ':val2' => $val
            ]);
        }
        
        $db->commit();
        log_action($db, 'update_smtp', "SMTP ayarları güncellendi.");
        echo json_encode(['status' => 'success', 'message' => 'SMTP ayarları başarıyla kaydedildi.']);
    } catch (PDOException $e) {
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
else {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz action parametresi.']);
}
