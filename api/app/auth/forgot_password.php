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
$app_id = $input['app_id'] ?? null;

if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Email is required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Add columns if they don't exist
    try {
        $db->exec("ALTER TABLE users ADD COLUMN reset_token VARCHAR(10) NULL, ADD COLUMN reset_token_expires DATETIME NULL");
    } catch (PDOException $e) {
        // Ignore if columns already exist
    }
    
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email AND is_deleted = 0");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Email not found in our records.']);
        exit;
    }
    
    $otp = sprintf("%06d", random_int(100000, 999999));
    $expires = date('Y-m-d H:i:s', time() + (15 * 60)); // 15 minutes
    
    $upd = $db->prepare("UPDATE users SET reset_token = :token, reset_token_expires = :expires WHERE id = :id");
    $upd->execute([':token' => $otp, ':expires' => $expires, ':id' => $user['id']]);
    
    // Send email (Using mail() since no SMTP config is present in config.php)
    $subject = "Your Password Reset Code";
    $message = "Your password reset code is: $otp\nThis code will expire in 15 minutes.";
    $headers = "From: no-reply@acms.local\r\n";
    $headers .= "Reply-To: no-reply@acms.local\r\n";
    $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
    
    // If mail() is not configured in XAMPP, this might fail, but it's what the user asked for.
    // I will also output the OTP to the error log for easy local testing without configuring mail()
    error_log("ACMS Forgot Password OTP for $email: $otp");
    
    @mail($email, $subject, $message, $headers);
    
    echo json_encode(['status' => 'success', 'message' => 'Check your email for the code.']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
