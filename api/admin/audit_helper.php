<?php
/**
 * ACMS Audit Logger Helper
 */
function log_action($db, $action, $details, $admin_id = 1) {
    try {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        
        $stmt = $db->prepare("INSERT INTO audit_logs (admin_id, action, details, ip_address, created_at) VALUES (:admin_id, :action, :details, :ip_address, NOW())");
        $stmt->execute([
            ':admin_id' => $admin_id,
            ':action' => $action,
            ':details' => $details,
            ':ip_address' => $ip_address
        ]);
    } catch (PDOException $e) {
        // Loglama hatası ana işlemi durdurmamalı
        error_log("Audit Log Hatası: " . $e->getMessage());
    }
}
?>
