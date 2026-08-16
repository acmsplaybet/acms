<?php
require_once '../config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Create settings table
    $db->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Insert defaults
    $defaults = [
        'bot_api_url' => 'https://realmobilebet.com/bpiv2/bpav2/api/bpa_history.php',
        'bot_cron_fetch' => '09:00, 14:00',
        'bot_cron_result' => '30',
        'bot_status' => '1'
    ];
    
    $stmt = $db->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($defaults as $k => $v) {
        $stmt->execute([$k, $v]);
    }
    
    echo "Settings table created and seeded.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
