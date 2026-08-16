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
            SELECT f.id, f.app_id, a.name as app_name, f.question, f.answer, f.status, f.created_at 
            FROM faqs f
            LEFT JOIN apps a ON f.app_id = a.id
            WHERE f.is_deleted = 0
            ORDER BY f.id DESC
        ");
        $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $faqs]);
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
    $question = trim($input['question'] ?? '');
    $answer = trim($input['answer'] ?? '');
    $status = $input['status'] ?? 'active';

    if (empty($app_id) || empty($question) || empty($answer)) {
        echo json_encode(['status' => 'error', 'message' => 'Uygulama, Soru ve Cevap alanları zorunludur.']);
        exit;
    }

    try {
        if (empty($id)) {
            $stmt = $db->prepare("INSERT INTO faqs (app_id, question, answer, status) VALUES (:app_id, :question, :answer, :status)");
            $stmt->execute([
                ':app_id' => $app_id,
                ':question' => $question,
                ':answer' => $answer,
                ':status' => $status
            ]);
            $new_id = $db->lastInsertId();
            log_action($db, 'add_faq', "FAQ eklendi ID: $new_id, App ID: $app_id");
            echo json_encode(['status' => 'success', 'message' => 'SSS başarıyla eklendi.']);
        } else {
            $stmt = $db->prepare("UPDATE faqs SET app_id = :app_id, question = :question, answer = :answer, status = :status WHERE id = :id");
            $stmt->execute([
                ':app_id' => $app_id,
                ':question' => $question,
                ':answer' => $answer,
                ':status' => $status,
                ':id' => $id
            ]);
            log_action($db, 'update_faq', "FAQ güncellendi ID: $id, App ID: $app_id");
            echo json_encode(['status' => 'success', 'message' => 'SSS başarıyla güncellendi.']);
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
        $stmt = $db->prepare("UPDATE faqs SET is_deleted = 1 WHERE id = :id");
        $stmt->execute([':id' => $id]);
        log_action($db, 'delete_faq', "FAQ silindi (Soft Delete) ID: $id");
        echo json_encode(['status' => 'success', 'message' => 'SSS silindi.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
else {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz action parametresi.']);
}
