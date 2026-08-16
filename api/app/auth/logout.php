<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once '../../config/config.php';
require_once '../../config/Database.php';

$headers = apache_request_headers();
$token = '';
if (isset($headers['Authorization'])) {
    $token = str_replace('Bearer ', '', $headers['Authorization']);
} else {
    $rawInput = file_get_contents('php://input');
    $input = $rawInput ? json_decode($rawInput, true) : $_POST;
    $token = $input['token'] ?? '';
}

if (empty($token)) {
    echo json_encode(['status' => 'success', 'message' => 'Logged out.']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("UPDATE users SET session_token = NULL WHERE session_token = :token");
    $stmt->execute([':token' => $token]);
    
    echo json_encode(['status' => 'success', 'message' => 'Logged out.']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
