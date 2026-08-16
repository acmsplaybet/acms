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

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');
$gpa_code = trim($input['gpa_code'] ?? '');
$app_id = $input['app_id'] ?? null;

if (empty($name) || empty($email) || empty($password) || empty($app_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Check email
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'code' => 'EMAIL_EXISTS', 'message' => 'Email already registered']);
        exit;
    }
    
    // Check GPA Code if provided
    if (!empty($gpa_code)) {
        $stmt = $db->prepare("SELECT id FROM users WHERE gpa_code = :gpa_code");
        $stmt->execute([':gpa_code' => $gpa_code]);
        if ($stmt->fetch()) {
            echo json_encode(['status' => 'error', 'code' => 'GPA_EXISTS', 'message' => 'GPA code already used']);
            exit;
        }
    }
    
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $token = bin2hex(random_bytes(32));
    
    $db->beginTransaction();
    
    $stmt = $db->prepare("INSERT INTO users (name, email, password, gpa_code, session_token, is_banned, created_at) VALUES (:name, :email, :password, :gpa_code, :token, 0, NOW())");
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => $hashed_password,
        ':gpa_code' => !empty($gpa_code) ? $gpa_code : null,
        ':token' => $token
    ]);
    $user_id = $db->lastInsertId();
    
    // Insert into user_apps
    $stmtApp = $db->prepare("INSERT INTO user_apps (user_id, app_id) VALUES (:user_id, :app_id)");
    $stmtApp->execute([':user_id' => $user_id, ':app_id' => $app_id]);
    
    $db->commit();
    
    http_response_code(201);
    echo json_encode([
        'status' => 'success',
        'token' => $token,
        'user' => [
            'id' => (int)$user_id,
            'name' => $name,
            'email' => $email,
            'status' => 'pending'
        ]
    ]);
    
} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
