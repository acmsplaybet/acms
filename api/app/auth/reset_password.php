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
$otp_code = trim($input['otp_code'] ?? '');
$new_password = trim($input['new_password'] ?? '');
$app_id = $input['app_id'] ?? null;

if (empty($email) || empty($otp_code) || empty($new_password)) {
    echo json_encode(['status' => 'error', 'message' => 'Email, OTP code, and new password are required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email AND reset_token = :token AND reset_token_expires > NOW() AND is_deleted = 0");
    $stmt->execute([':email' => $email, ':token' => $otp_code]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired code']);
        exit;
    }
    
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $upd = $db->prepare("UPDATE users SET password = :pass, reset_token = NULL, reset_token_expires = NULL WHERE id = :id");
    $upd->execute([':pass' => $hashed_password, ':id' => $user['id']]);
    
    echo json_encode(['status' => 'success', 'message' => 'Password reset! Please login.']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
