<?php
require_once __DIR__ . '/../config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $queries = [
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS auth_provider VARCHAR(20) DEFAULT 'email'",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS google_id VARCHAR(191) NULL",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS google_verified TINYINT(1) DEFAULT 0",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar_url VARCHAR(500) NULL"
    ];
    
    foreach ($queries as $q) {
        try {
            $db->exec($q);
            echo "Executed: $q\n";
        } catch (Exception $e) {
            echo "Notice: " . $e->getMessage() . "\n";
        }
    }
    
    echo "Migration completed successfully.\n";
} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
}
