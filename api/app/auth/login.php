<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once '../../config/config.php';
require_once '../../config/Database.php';

$rawInput = file_get_contents('php://input');
$input = $rawInput ? json_decode($rawInput, true) : $_POST;

$email = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');
$app_id = $input['app_id'] ?? null;

if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email AND is_deleted = 0");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['status' => 'error', 'code' => 'INVALID_CREDENTIALS', 'message' => 'Invalid email or password']);
        exit;
    }
    
    if ($user['lockout_time'] && strtotime($user['lockout_time']) > time()) {
        $wait_minutes = ceil((strtotime($user['lockout_time']) - time()) / 60);
        echo json_encode([
            'status' => 'error', 
            'code' => 'ACCOUNT_LOCKED', 
            'wait_minutes' => $wait_minutes,
            'message' => "Account is locked. Try again in $wait_minutes minutes."
        ]);
        exit;
    }
    
    if (!password_verify($password, $user['password'])) {
        $attempts = (int)($user['failed_login_attempts'] ?? 0) + 1;
        $lockout = null;
        if ($attempts >= 5) {
            $lockout = date('Y-m-d H:i:s', time() + (30 * 60)); // 30 mins
        }
        
        $upd = $db->prepare("UPDATE users SET failed_login_attempts = :attempts, lockout_time = :lockout WHERE id = :id");
        $upd->execute([':attempts' => $attempts, ':lockout' => $lockout, ':id' => $user['id']]);
        
        echo json_encode([
            'status' => 'error', 
            'code' => 'INVALID_CREDENTIALS', 
            'attempts_left' => max(0, 5 - $attempts),
            'message' => 'Invalid email or password'
        ]);
        exit;
    }
    
    if ((int)$user['is_banned'] === 1) {
        // Banlı kullanıcı token alıp giriş yapabilir, uygulama içinde BannedState gösterilir
        $token = bin2hex(random_bytes(32));
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $upd = $db->prepare("UPDATE users SET session_token = :token, last_login_date = NOW(), last_login_ip = :ip WHERE id = :id");
        $upd->execute([':token' => $token, ':ip' => $ip, ':id' => $user['id']]);
        echo json_encode([
            'status' => 'banned',
            'token'  => $token,
            'ban_reason' => $user['ban_reason'] ?? 'You have been banned from the system.',
            'user' => ['id' => (int)$user['id'], 'name' => $user['name'], 'email' => $user['email']]
        ]);
        exit;
    }
    
    $token = bin2hex(random_bytes(32));
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    
    $upd = $db->prepare("UPDATE users SET failed_login_attempts = 0, lockout_time = NULL, session_token = :token, last_login_date = NOW(), last_login_ip = :ip WHERE id = :id");
    $upd->execute([':token' => $token, ':ip' => $ip, ':id' => $user['id']]);
    
    echo json_encode([
        'status' => 'success',
        'token' => $token,
        'user' => [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'status' => 'active',
            'exempt_security' => (int)($user['exempt_security'] ?? 0),
            'exempt_screenshot' => (int)($user['exempt_screenshot'] ?? 0),
            'gamification_badge' => $user['gamification_badge'] ? explode(',', $user['gamification_badge']) : []
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
