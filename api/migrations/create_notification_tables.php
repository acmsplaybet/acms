<?php
require_once __DIR__ . '/../config/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    // 1. Ensure onesignal_app_id and onesignal_api_key exist in apps table
    $columnsStmt = $db->query("SHOW COLUMNS FROM apps");
    $existingColumns = $columnsStmt->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('onesignal_app_id', $existingColumns)) {
        $db->exec("ALTER TABLE apps ADD COLUMN onesignal_app_id VARCHAR(100) NULL AFTER tawk_to_id");
        echo "[Migration] Added onesignal_app_id to apps table.<br>\n";
    }

    if (!in_array('onesignal_api_key', $existingColumns)) {
        $db->exec("ALTER TABLE apps ADD COLUMN onesignal_api_key VARCHAR(255) NULL AFTER onesignal_app_id");
        echo "[Migration] Added onesignal_api_key to apps table.<br>\n";
    }

    // 2. Create notification_logs table if not exists
    $createTableSql = "
    CREATE TABLE IF NOT EXISTS notification_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        app_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        target_segment VARCHAR(50) DEFAULT 'all',
        target_user_id INT NULL,
        image_url VARCHAR(500) NULL,
        deep_link VARCHAR(255) NULL,
        onesignal_id VARCHAR(100) NULL,
        recipients INT DEFAULT 0,
        status VARCHAR(50) DEFAULT 'sent',
        response_json LONGTEXT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_app_id (app_id),
        INDEX idx_created_at (created_at),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $db->exec($createTableSql);
    echo "[Migration] notification_logs table is ready.<br>\n";

    echo "[Migration Completed Successfully]";
} catch (Exception $e) {
    echo "[Migration Error] " . $e->getMessage();
}
