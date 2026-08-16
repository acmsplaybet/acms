<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once '../config/config.php';
require_once '../config/Database.php';

function getAuthUser($db) {
    $headers = apache_request_headers();
    $token = '';
    if (isset($headers['Authorization'])) {
        $token = str_replace('Bearer ', '', $headers['Authorization']);
    } else {
        $rawInput = file_get_contents('php://input');
        $input = $rawInput ? json_decode($rawInput, true) : $_POST;
        $token = $_GET['token'] ?? ($input['token'] ?? '');
    }
    
    if (empty($token)) return null;
    
    $stmt = $db->prepare("SELECT * FROM users WHERE session_token = :token AND is_deleted = 0");
    $stmt->execute([':token' => $token]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $rawInput = file_get_contents('php://input');
    $input = $rawInput ? json_decode($rawInput, true) : $_POST;
    
    $action = $_GET['action'] ?? ($input['action'] ?? '');
    if (empty($action) && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = 'get';
    }
    
    $user = getAuthUser($db);
    if (!$user) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
    
    if ($action === 'get') {
        $badges = [];
        if (!empty($user['gamification_badge'])) {
            $badges = array_filter(array_map('trim', explode(',', $user['gamification_badge'])));
        }
        
        // Status'u user_apps tablosundan oku (admin panelin onay butonu buraya yazıyor)
        $appId = $_GET['app_id'] ?? 1;
        $stmtStatus = $db->prepare("SELECT status FROM user_apps WHERE user_id = :uid AND app_id = :aid ORDER BY id DESC LIMIT 1");
        $stmtStatus->execute([':uid' => $user['id'], ':aid' => $appId]);
        $userAppRow = $stmtStatus->fetch(PDO::FETCH_ASSOC);
        $status = ((int)$user['is_banned'] === 1) ? 'banned' : ($userAppRow ? $userAppRow['status'] : (empty($user['approval_date']) ? 'pending' : 'approved'));
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'id' => (int)$user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'avatar_url' => $user['avatar_url'] ?? '',
                'auth_provider' => $user['auth_provider'] ?? 'email',
                'google_verified' => (int)($user['google_verified'] ?? 0),
                'gpa_code' => $user['gpa_code'] ?? '',
                'status' => $status,
                'badges' => array_values($badges),
                'approval_date' => $user['approval_date'],
                'exempt_security' => (int)($user['exempt_security'] ?? 0),
                'exempt_screenshot' => (int)($user['exempt_screenshot'] ?? 0),
                'last_login_date' => $user['last_login_date'],
                'ban_reason' => ((int)$user['is_banned'] === 1) ? ($user['ban_reason'] ?? 'No reason provided.') : null
            ]
        ]);
    }
    elseif ($action === 'change_password') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid method']); exit;
        }
        $old_password = $input['old_password'] ?? '';
        $new_password = $input['new_password'] ?? '';
        
        if (!password_verify($old_password, $user['password'])) {
            echo json_encode(['status' => 'error', 'code' => 'WRONG_PASSWORD', 'message' => 'Incorrect old password']);
            exit;
        }
        if (strlen($new_password) < 8) {
            echo json_encode(['status' => 'error', 'message' => 'New password must be at least 8 characters']);
            exit;
        }
        
        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE users SET password = :pass WHERE id = :id");
        $stmt->execute([':pass' => $hashed, ':id' => $user['id']]);
        
        echo json_encode(['status' => 'success', 'message' => 'Password updated.']);
    }
    elseif ($action === 'update_gpa') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid method']); exit;
        }
        $gpa_code = trim($input['gpa_code'] ?? '');
        if (empty($gpa_code)) {
            echo json_encode(['status' => 'error', 'message' => 'GPA code is required']); exit;
        }
        
        // Check uniqueness
        $chk = $db->prepare("SELECT id FROM users WHERE gpa_code = :gpa AND id != :id");
        $chk->execute([':gpa' => $gpa_code, ':id' => $user['id']]);
        if ($chk->fetch()) {
            echo json_encode(['status' => 'error', 'code' => 'GPA_EXISTS', 'message' => 'GPA code is already used by another account']);
            exit;
        }
        
        // Set approval_date to NULL for pending status
        $stmt = $db->prepare("UPDATE users SET gpa_code = :gpa, approval_date = NULL WHERE id = :id");
        $stmt->execute([':gpa' => $gpa_code, ':id' => $user['id']]);
        
        echo json_encode(['status' => 'success', 'message' => 'Order code updated. Pending approval.']);
    }
    elseif ($action === 'delete_account') {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid method']); exit;
        }
        
        $isGoogleUser = ($user['auth_provider'] ?? '') === 'google' || ((int)($user['google_verified'] ?? 0) === 1);
        
        if (!$isGoogleUser) {
            $password = $input['password'] ?? '';
            if (empty($password) || !password_verify($password, $user['password'])) {
                echo json_encode(['status' => 'error', 'code' => 'WRONG_PASSWORD', 'message' => 'Incorrect password']);
                exit;
            }
        }
        
        try {
            $db->exec("ALTER TABLE users ADD COLUMN deleted_by_user TINYINT(1) DEFAULT 0");
        } catch (PDOException $e) {
            // Column may already exist
        }
        
        $stmt = $db->prepare("UPDATE users SET is_deleted = 1, session_token = NULL, deleted_by_user = 1 WHERE id = :id");
        $stmt->execute([':id' => $user['id']]);
        
        echo json_encode(['status' => 'success', 'message' => 'Account deleted successfully.']);
    }
    else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
