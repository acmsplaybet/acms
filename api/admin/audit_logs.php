<?php
require_once '../config/config.php';
require_once '../config/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if audit_logs table exists and if not, create it for robustness
    $db->exec("CREATE TABLE IF NOT EXISTS audit_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        admin_id INT NOT NULL DEFAULT 1,
        action VARCHAR(100) NOT NULL,
        details TEXT,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $stmt = $db->query("
        SELECT al.id, COALESCE(a.name, CONCAT('Admin (ID: ', al.admin_id, ')')) as admin_name, al.action, al.details, al.ip_address, al.created_at 
        FROM audit_logs al
        LEFT JOIN admins a ON al.admin_id = a.id
        ORDER BY al.created_at DESC 
        LIMIT 500
    ");
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'data' => $logs
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
}
?>
