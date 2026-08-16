<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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
    
    if ($action === 'faqs') {
        $app_id = $_GET['app_id'] ?? ($input['app_id'] ?? null);
        if (!$app_id) {
            echo json_encode(['status' => 'error', 'message' => 'App ID required']); exit;
        }
        
        $stmt = $db->prepare("SELECT id, question, answer FROM faqs WHERE app_id = :app_id AND status = 'active' AND is_deleted = 0");
        $stmt->execute([':app_id' => $app_id]);
        $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'success', 'data' => $faqs]);
    }
    elseif ($action === 'tickets') {
        $user = getAuthUser($db);
        if (!$user) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); exit;
        }
        
        $stmt = $db->prepare("SELECT id, subject, category, message, status, admin_reply, created_at, updated_at FROM tickets WHERE user_id = :uid AND is_deleted = 0 ORDER BY created_at DESC");
        $stmt->execute([':uid' => $user['id']]);
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'success', 'data' => $tickets]);
    }
    elseif ($action === 'create_ticket') {
        $user = getAuthUser($db);
        if (!$user) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid method']); exit;
        }
        
        $category = trim($input['category'] ?? '');
        $subject = trim($input['subject'] ?? '');
        $message = trim($input['message'] ?? '');
        
        // Find which app_id this user belongs to based on user_apps if needed, or get from input
        $app_id = $input['app_id'] ?? 1; // Fallback to 1 if not provided
        
        if (empty($category) || empty($subject) || empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing fields']); exit;
        }
        
        // Count open or pending tickets
        $countStmt = $db->prepare("SELECT COUNT(*) as c FROM tickets WHERE user_id = :uid AND status IN ('open', 'pending') AND is_deleted = 0");
        $countStmt->execute([':uid' => $user['id']]);
        $active_tickets = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['c'];
        
        if ($active_tickets >= 2) {
            echo json_encode(['status' => 'error', 'code' => 'MAX_TICKETS', 'message' => 'Max 2 open tickets allowed.']);
            exit;
        }
        
        $stmt = $db->prepare("INSERT INTO tickets (user_id, app_id, category, subject, message, status, created_at, updated_at) VALUES (:uid, :app_id, :cat, :subj, :msg, 'open', NOW(), NOW())");
        $stmt->execute([
            ':uid' => $user['id'],
            ':app_id' => $app_id,
            ':cat' => $category,
            ':subj' => $subject,
            ':msg' => $message
        ]);
        
        echo json_encode(['status' => 'success', 'message' => 'Ticket created.']);
    }
    else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
