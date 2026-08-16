<?php
/**
 * Admin Login API Endpoint
 * Method: POST
 * Content-Type: application/json
 */

require_once '../config/Database.php';

// Check HTTP Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Sadece POST istekleri kabul edilir."]);
    exit;
}

// Start secure session
session_start();

// Get JSON payload
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// If not JSON, try normal POST array
if (!$data) {
    $data = $_POST;
}

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

// Validate inputs
if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Lütfen e-posta ve şifrenizi girin."]);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Fetch admin by email
    $stmt = $db->prepare("SELECT id, name, email, password, role FROM admins WHERE email = :email LIMIT 1");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $admin = $stmt->fetch();
    
    // Verify password
    if ($admin && password_verify($password, $admin['password'])) {
        
        // Login Successful
        // Generate a secure session token
        $session_token = bin2hex(random_bytes(32));
        
        // Store in $_SESSION
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_role'] = $admin['role'];
        $_SESSION['token'] = $session_token;
        
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Giriş başarılı.",
            "data" => [
                "id" => $admin['id'],
                "name" => $admin['name'],
                "email" => $admin['email'],
                "role" => $admin['role'],
                "token" => $session_token
            ]
        ]);
        
    } else {
        // Login Failed
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "E-posta veya şifre hatalı."]);
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Sunucu hatası: " . $e->getMessage()]);
}
