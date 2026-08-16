<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
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
    $token = $input['token'] ?? ($_GET['token'] ?? '');
}

if (empty($token)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'code' => 'INVALID_TOKEN', 'message' => 'Token not provided']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT * FROM users WHERE session_token = :token AND is_deleted = 0");
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'code' => 'INVALID_TOKEN', 'message' => 'Invalid or expired token']);
        exit;
    }
    
    if ((int)$user['is_banned'] === 1) {
        // HTTP 200 döndür, frontend BannedState göstersin, apiCall 403 handler'ı tetiklenmesin
        echo json_encode([
            'status' => 'banned',
            'code'   => 'BANNED',
            'ban_reason' => $user['ban_reason'] ?? 'You have been banned.',
            'user' => ['id' => (int)$user['id'], 'name' => $user['name'], 'email' => $user['email']]
        ]);
        exit;
    }
    
    $upd = $db->prepare("UPDATE users SET last_login_date = NOW() WHERE id = :id");
    $upd->execute([':id' => $user['id']]);
    
    $app_id = $input['app_id'] ?? ($_GET['app_id'] ?? 1);
    $uaStmt = $db->prepare("SELECT status FROM user_apps WHERE user_id = :uid AND app_id = :aid");
    $uaStmt->execute([':uid' => $user['id'], ':aid' => $app_id]);
    $uaStatus = $uaStmt->fetchColumn() ?: 'pending';

    echo json_encode([
        'status' => 'success',
        'token' => $token,
        'user' => [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'status' => $uaStatus,
            'exempt_security' => (int)($user['exempt_security'] ?? 0),
            'exempt_screenshot' => (int)($user['exempt_screenshot'] ?? 0),
            'gamification_badge' => $user['gamification_badge'] ? explode(',', $user['gamification_badge']) : [],
            'approval_date' => $user['approval_date']
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
