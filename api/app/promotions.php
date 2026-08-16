<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once '../config/config.php';
require_once '../config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $app_id = $_GET['app_id'] ?? null;
    if (!$app_id) {
        echo json_encode(['status' => 'error', 'message' => 'App ID required']);
        exit;
    }
    
    $stmt = $db->prepare("
        SELECT id, title, description, badge_text, end_date 
        FROM promotions 
        WHERE app_id = :app_id AND status = 'active' AND is_deleted = 0 AND (end_date IS NULL OR end_date >= NOW())
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->execute([':app_id' => $app_id]);
    $promo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($promo) {
        echo json_encode(['status' => 'success', 'data' => [
            'id' => (int)$promo['id'],
            'title' => $promo['title'],
            'description' => $promo['description'],
            'badge_text' => $promo['badge_text'],
            'end_date' => $promo['end_date']
        ]]);
    } else {
        echo json_encode(['status' => 'success', 'data' => null]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
