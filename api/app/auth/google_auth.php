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
$name = trim($input['name'] ?? '');
$google_id = trim($input['google_id'] ?? '');
$avatar_url = trim($input['avatar_url'] ?? '');
$gpa_code = trim($input['gpa_code'] ?? 'GOOGLE-PLAY-VERIFIED');
$app_id = intval($input['app_id'] ?? 1);

if (empty($email) || empty($app_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Email and app_id are required.']);
    exit;
}

if (empty($name)) {
    $parts = explode('@', $email);
    $name = ucfirst($parts[0]);
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if user already exists by email (including previously soft-deleted)
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $token = bin2hex(random_bytes(32));
    $user_id = null;
    
    if ($user) {
        $user_id = (int)$user['id'];
        
        if ((int)$user['is_banned'] === 1) {
            echo json_encode([
                'status' => 'banned',
                'code' => 'BANNED',
                'ban_reason' => $user['ban_reason'] ?? 'You have been banned.',
                'user' => ['id' => $user_id, 'name' => $user['name'], 'email' => $user['email']]
            ]);
            exit;
        }
        
        // Update user with Google info & fresh token (and restore if was deleted)
        $upd = $db->prepare("
            UPDATE users 
            SET session_token = :token, 
                google_id = COALESCE(:google_id, google_id),
                avatar_url = COALESCE(:avatar_url, avatar_url),
                google_verified = 1,
                auth_provider = 'google',
                is_deleted = 0,
                last_login_date = NOW(),
                last_login_ip = :ip
            WHERE id = :id
        ");
        $upd->execute([
            ':token' => $token,
            ':google_id' => !empty($google_id) ? $google_id : null,
            ':avatar_url' => !empty($avatar_url) ? $avatar_url : null,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            ':id' => $user_id
        ]);
        
        // Ensure user_apps mapping exists
        $stmtApp = $db->prepare("SELECT status FROM user_apps WHERE user_id = :user_id AND app_id = :app_id LIMIT 1");
        $stmtApp->execute([':user_id' => $user_id, ':app_id' => $app_id]);
        $appRow = $stmtApp->fetch(PDO::FETCH_ASSOC);
        
        if (!$appRow) {
            $insApp = $db->prepare("INSERT INTO user_apps (user_id, app_id, status) VALUES (:user_id, :app_id, 'pending')");
            $insApp->execute([':user_id' => $user_id, ':app_id' => $app_id]);
            $current_status = 'pending';
        } else {
            $current_status = $appRow['status'];
        }
        
        // If user already had approval_date, maintain approved status
        if (!empty($user['approval_date']) && $current_status === 'pending') {
            $current_status = 'approved';
        }
        
        echo json_encode([
            'status' => 'success',
            'token' => $token,
            'is_new_user' => false,
            'user' => [
                'id' => $user_id,
                'name' => $user['name'] ?: $name,
                'email' => $email,
                'avatar_url' => $user['avatar_url'] ?: $avatar_url,
                'status' => $current_status,
                'google_verified' => 1,
                'exempt_security' => (int)($user['exempt_security'] ?? 0),
                'exempt_screenshot' => (int)($user['exempt_screenshot'] ?? 0)
            ]
        ]);
        
    } else {
        // Register brand new user with Google Auth & Verified GPA
        $db->beginTransaction();
        
        $random_password = password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT);
        
        $ins = $db->prepare("
            INSERT INTO users (
                name, email, password, gpa_code, session_token, 
                auth_provider, google_id, google_verified, avatar_url,
                is_banned, created_at, last_login_date, last_login_ip
            ) VALUES (
                :name, :email, :password, :gpa_code, :token,
                'google', :google_id, 1, :avatar_url,
                0, NOW(), NOW(), :ip
            )
        ");
        
        $ins->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $random_password,
            ':gpa_code' => $gpa_code,
            ':token' => $token,
            ':google_id' => !empty($google_id) ? $google_id : null,
            ':avatar_url' => !empty($avatar_url) ? $avatar_url : null,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);
        
        $user_id = (int)$db->lastInsertId();
        
        // Add into user_apps with pending status
        $insApp = $db->prepare("INSERT INTO user_apps (user_id, app_id, status) VALUES (:user_id, :app_id, 'pending')");
        $insApp->execute([':user_id' => $user_id, ':app_id' => $app_id]);
        
        $db->commit();
        
        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'token' => $token,
            'is_new_user' => true,
            'user' => [
                'id' => $user_id,
                'name' => $name,
                'email' => $email,
                'avatar_url' => $avatar_url,
                'status' => 'pending',
                'google_verified' => 1,
                'exempt_security' => 0,
                'exempt_screenshot' => 0
            ]
        ]);
    }
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
