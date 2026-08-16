<?php
require_once 'c:/xampp/htdocs/acms/api/config/Database.php';
try {
    $db = Database::getInstance()->getConnection();
    $queries = [
        "ALTER TABLE apps ADD COLUMN contact_telegram_response VARCHAR(50) DEFAULT '~1–2 hours';",
        "ALTER TABLE apps ADD COLUMN contact_whatsapp_response VARCHAR(50) DEFAULT '~1–2 hours';",
        "ALTER TABLE apps ADD COLUMN contact_instagram_response VARCHAR(50) DEFAULT '~24 hours';",
        "ALTER TABLE apps ADD COLUMN contact_email_response VARCHAR(50) DEFAULT '~24 hours';"
    ];
    foreach ($queries as $q) {
        try {
            $db->exec($q);
            echo "Success: $q\n";
        } catch (Exception $e) {
            echo "Failed or already exists: $q\n";
        }
    }
    
    $sql = "
    ALTER TABLE apps
        ADD COLUMN IF NOT EXISTS rate_us_active TINYINT(1) DEFAULT 0,
        ADD COLUMN IF NOT EXISTS rate_us_title VARCHAR(255) DEFAULT 'Enjoying the App? ⭐',
        ADD COLUMN IF NOT EXISTS rate_us_text TEXT,
        ADD COLUMN IF NOT EXISTS rate_us_reward VARCHAR(500),
        ADD COLUMN IF NOT EXISTS rate_us_snooze_days INT DEFAULT 3,
        ADD COLUMN IF NOT EXISTS rate_us_rate_btn_text VARCHAR(100) DEFAULT '⭐ Rate on Google Play',
        ADD COLUMN IF NOT EXISTS rate_us_later_btn_text VARCHAR(100) DEFAULT 'Remind me later',
        ADD COLUMN IF NOT EXISTS rate_us_step2_title VARCHAR(255) DEFAULT 'Thanks for your support! 🙌',
        ADD COLUMN IF NOT EXISTS rate_us_step2_text TEXT,
        ADD COLUMN IF NOT EXISTS rate_us_step2_email_btn VARCHAR(100) DEFAULT '📩 Send via Email',
        ADD COLUMN IF NOT EXISTS rate_us_step2_telegram_btn VARCHAR(100) DEFAULT '💬 Send via Telegram',
        ADD COLUMN IF NOT EXISTS rate_us_step2_done_btn VARCHAR(100) DEFAULT 'Done ✓';
    ";
    
    $db->exec($sql);
    echo "Columns added successfully!\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Columns already exist.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage();
}

try {
    $sql2 = "
    ALTER TABLE apps ADD COLUMN IF NOT EXISTS category VARCHAR(50) DEFAULT 'free';
    ALTER TABLE apps ADD COLUMN IF NOT EXISTS theme VARCHAR(50) DEFAULT 'real';
    ALTER TABLE brands ADD COLUMN IF NOT EXISTS default_theme VARCHAR(50) DEFAULT 'real';
    ALTER TABLE brands ADD COLUMN IF NOT EXISTS description VARCHAR(255) DEFAULT '';
    ALTER TABLE brands ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1;
    ALTER TABLE brands ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
    ";
    $db->exec($sql2);
    echo "Brands/Apps columns added successfully!\n";
} catch (Exception $e) {
    echo "Error adding brands/apps columns: " . $e->getMessage();
}
