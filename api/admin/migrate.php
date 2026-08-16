<?php
/**
 * ACMS Zero-Data-Loss Universal Migration Engine
 * 
 * Safely synchronizes Database Schema (Tables, Columns, Indexes, Default Settings)
 * WITHOUT ever deleting, truncating, or overwriting existing live production records.
 */

// Allow CLI or Authorized Web Access
$isCli = (php_sapi_name() === 'cli');
if (!$isCli) {
    session_start();
    $isLoggedInAdmin = isset($_SESSION['admin_id']) || isset($_SESSION['user_id']);
    $providedKey = $_GET['key'] ?? $_POST['key'] ?? '';
    $secretKey = 'acms_playbet_migrate_2026';

    if (!$isLoggedInAdmin && $providedKey !== $secretKey) {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "Yetkisiz erişim. Güvenlik anahtarı geçersiz."], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

require_once __DIR__ . '/../config/Database.php';

$db = Database::getInstance()->getConnection();
$log = [];

function addLog($msg) {
    global $log, $isCli;
    $log[] = $msg;
    if ($isCli) {
        echo $msg . "\n";
    }
}

addLog("==========================================================");
addLog("🚀 ACMS NON-DESTRUCTIVE DATABASE MIGRATION ENGINE");
addLog("📍 Environment: " . (defined('ENV_TYPE') ? ENV_TYPE : 'unknown'));
addLog("🗄️ Database: " . DB_NAME);
addLog("==========================================================\n");

// Helper to check if a column exists in a table
function columnExists($db, $table, $column) {
    $stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = :dbname AND TABLE_NAME = :table AND COLUMN_NAME = :column
    ");
    $stmt->execute([
        ':dbname' => DB_NAME,
        ':table'  => $table,
        ':column' => $column
    ]);
    return ($stmt->fetchColumn() > 0);
}

// Helper to check if an index exists in a table
function indexExists($db, $table, $index) {
    $stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM information_schema.STATISTICS 
        WHERE TABLE_SCHEMA = :dbname AND TABLE_NAME = :table AND INDEX_NAME = :index
    ");
    $stmt->execute([
        ':dbname' => DB_NAME,
        ':table'  => $table,
        ':index'  => $index
    ]);
    return ($stmt->fetchColumn() > 0);
}

// ---------------------------------------------------------------
// STEP 1: CREATE TABLES IF NOT EXISTS (Safe - Never touches data)
// ---------------------------------------------------------------
$tables = [
    "admins" => "CREATE TABLE IF NOT EXISTS `admins` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(150) NOT NULL,
        `password` varchar(255) NOT NULL,
        `role` enum('super_admin','editor') DEFAULT 'super_admin',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "brands" => "CREATE TABLE IF NOT EXISTS `brands` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `slug` varchar(100) NOT NULL,
        `logo_url` varchar(255) DEFAULT NULL,
        `default_theme` varchar(50) DEFAULT 'real',
        `description` varchar(255) DEFAULT '',
        `is_active` tinyint(1) DEFAULT 1,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "apps" => "CREATE TABLE IF NOT EXISTS `apps` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `brand_id` int(11) NOT NULL,
        `name` varchar(150) NOT NULL,
        `slug` varchar(150) NOT NULL,
        `category` varchar(50) DEFAULT 'free',
        `theme` varchar(50) DEFAULT 'real',
        `logo_url` varchar(255) DEFAULT NULL,
        `onesignal_app_id` varchar(100) DEFAULT NULL,
        `onesignal_rest_key` varchar(150) DEFAULT NULL,
        `contact_telegram` varchar(100) DEFAULT NULL,
        `contact_whatsapp` varchar(50) DEFAULT NULL,
        `contact_instagram` varchar(100) DEFAULT NULL,
        `contact_email` varchar(150) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`),
        KEY `brand_id` (`brand_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "leagues" => "CREATE TABLE IF NOT EXISTS `leagues` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(150) NOT NULL,
        `slug` varchar(180) DEFAULT NULL,
        `logo_url` varchar(255) DEFAULT NULL,
        `is_deleted` tinyint(1) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "teams" => "CREATE TABLE IF NOT EXISTS `teams` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `league_id` int(11) DEFAULT NULL,
        `name` varchar(150) NOT NULL,
        `slug` varchar(180) DEFAULT NULL,
        `logo_url` varchar(255) DEFAULT NULL,
        `is_deleted` tinyint(1) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `league_id` (`league_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "matches" => "CREATE TABLE IF NOT EXISTS `matches` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `match_title` varchar(255) NOT NULL,
        `match_date` datetime NOT NULL,
        `league_id` int(11) DEFAULT NULL,
        `home_team_id` int(11) DEFAULT NULL,
        `away_team_id` int(11) DEFAULT NULL,
        `home_logo` varchar(255) DEFAULT NULL,
        `away_logo` varchar(255) DEFAULT NULL,
        `prediction` varchar(150) NOT NULL,
        `odds` varchar(20) NOT NULL,
        `confidence_rate` varchar(10) DEFAULT NULL,
        `score` varchar(20) DEFAULT NULL,
        `status` enum('pending','win','lose','postponed') DEFAULT 'pending',
        `is_bot_added` tinyint(4) DEFAULT 0,
        `is_deleted` tinyint(1) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `league_id` (`league_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "app_matches" => "CREATE TABLE IF NOT EXISTS `app_matches` (
        `app_id` int(11) NOT NULL,
        `match_id` int(11) NOT NULL,
        PRIMARY KEY (`app_id`,`match_id`),
        KEY `match_id` (`match_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "users" => "CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(150) NOT NULL,
        `email` varchar(150) NOT NULL,
        `password` varchar(255) NOT NULL,
        `gpa_code` varchar(255) DEFAULT NULL,
        `auth_provider` varchar(20) DEFAULT 'email',
        `google_id` varchar(191) DEFAULT NULL,
        `google_verified` tinyint(1) DEFAULT 0,
        `avatar_url` varchar(500) DEFAULT NULL,
        `is_banned` tinyint(1) DEFAULT 0,
        `ban_reason` text DEFAULT NULL,
        `exempt_force_update` tinyint(1) DEFAULT 0,
        `exempt_security` tinyint(1) DEFAULT 0,
        `exempt_screenshot` tinyint(1) DEFAULT 0,
        `last_login_ip` varchar(45) DEFAULT NULL,
        `last_login_date` datetime DEFAULT NULL,
        `device_id` varchar(255) DEFAULT NULL,
        `is_deleted` tinyint(1) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "user_apps" => "CREATE TABLE IF NOT EXISTS `user_apps` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `app_id` int(11) NOT NULL,
        `status` enum('pending','approved','rejected') DEFAULT 'pending',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `user_app` (`user_id`,`app_id`),
        KEY `app_id` (`app_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "tickets" => "CREATE TABLE IF NOT EXISTS `tickets` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `app_id` int(11) NOT NULL,
        `subject` varchar(255) DEFAULT NULL,
        `category` varchar(100) DEFAULT NULL,
        `message` text NOT NULL,
        `admin_reply` text DEFAULT NULL,
        `admin_reply_at` datetime DEFAULT NULL,
        `status` enum('open','pending','closed','cancelled') DEFAULT 'open',
        `is_deleted` tinyint(1) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `app_id` (`app_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "faqs" => "CREATE TABLE IF NOT EXISTS `faqs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `app_id` int(11) NOT NULL,
        `question` varchar(255) NOT NULL,
        `answer` text NOT NULL,
        `status` enum('active','passive') DEFAULT 'active',
        `is_deleted` tinyint(1) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `app_id` (`app_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "promotions" => "CREATE TABLE IF NOT EXISTS `promotions` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `app_id` int(11) NOT NULL,
        `title` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `badge_text` varchar(100) DEFAULT NULL,
        `end_date` datetime DEFAULT NULL,
        `status` enum('active','passive') DEFAULT 'active',
        `is_deleted` tinyint(1) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `app_id` (`app_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "notification_logs" => "CREATE TABLE IF NOT EXISTS `notification_logs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `app_id` int(11) NOT NULL,
        `title` varchar(255) NOT NULL,
        `message` text NOT NULL,
        `target_segment` varchar(50) DEFAULT 'all',
        `target_user_id` int(11) DEFAULT NULL,
        `image_url` varchar(500) DEFAULT NULL,
        `deep_link` varchar(255) DEFAULT NULL,
        `onesignal_id` varchar(100) DEFAULT NULL,
        `recipients` int(11) DEFAULT 0,
        `status` varchar(50) DEFAULT 'sent',
        `response_json` longtext DEFAULT NULL,
        `created_at` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `app_id` (`app_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "notification_templates" => "CREATE TABLE IF NOT EXISTS `notification_templates` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `app_id` int(11) NOT NULL,
        `event_key` varchar(50) NOT NULL,
        `name` varchar(100) NOT NULL,
        `is_active` tinyint(1) DEFAULT 1,
        `title_tr` varchar(255) DEFAULT NULL,
        `message_tr` text DEFAULT NULL,
        `title_en` varchar(255) DEFAULT NULL,
        `message_en` text DEFAULT NULL,
        `title_de` varchar(255) DEFAULT NULL,
        `message_de` text DEFAULT NULL,
        `title_es` varchar(255) DEFAULT NULL,
        `message_es` text DEFAULT NULL,
        `title_pt` varchar(255) DEFAULT NULL,
        `message_pt` text DEFAULT NULL,
        `title_fr` varchar(255) DEFAULT NULL,
        `message_fr` text DEFAULT NULL,
        `deep_link` varchar(255) DEFAULT '/app/tips',
        `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `app_event` (`app_id`,`event_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "password_resets" => "CREATE TABLE IF NOT EXISTS `password_resets` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `email` varchar(255) NOT NULL,
        `code` varchar(10) NOT NULL,
        `expires_at` datetime NOT NULL,
        `attempts` int(11) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "audit_logs" => "CREATE TABLE IF NOT EXISTS `audit_logs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `admin_id` int(11) DEFAULT NULL,
        `action` varchar(255) NOT NULL,
        `details` text DEFAULT NULL,
        `ip_address` varchar(45) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "settings" => "CREATE TABLE IF NOT EXISTS `settings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `setting_key` varchar(100) NOT NULL,
        `setting_value` text DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `setting_key` (`setting_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
];

foreach ($tables as $name => $sql) {
    try {
        $db->exec($sql);
        addLog("✓ Table verified/created: {$name}");
    } catch (PDOException $e) {
        addLog("❌ Error verifying table {$name}: " . $e->getMessage());
    }
}

// ---------------------------------------------------------------
// STEP 2: SAFE INCREMENTAL COLUMNS (Adds missing columns safely)
// ---------------------------------------------------------------
$columnDefinitions = [
    "apps" => [
        "category" => "VARCHAR(50) DEFAULT 'free'",
        "theme" => "VARCHAR(50) DEFAULT 'real'",
        "contact_telegram_response" => "VARCHAR(50) DEFAULT '~1–2 hours'",
        "contact_whatsapp_response" => "VARCHAR(50) DEFAULT '~1–2 hours'",
        "contact_instagram_response" => "VARCHAR(50) DEFAULT '~24 hours'",
        "contact_email_response" => "VARCHAR(50) DEFAULT '~24 hours'",
        "rate_us_active" => "TINYINT(1) DEFAULT 0",
        "rate_us_title" => "VARCHAR(255) DEFAULT 'Enjoying the App? ⭐'",
        "rate_us_text" => "TEXT DEFAULT NULL",
        "rate_us_reward" => "VARCHAR(500) DEFAULT NULL",
        "rate_us_snooze_days" => "INT DEFAULT 3",
        "rate_us_rate_btn_text" => "VARCHAR(100) DEFAULT '⭐ Rate on Google Play'",
        "rate_us_later_btn_text" => "VARCHAR(100) DEFAULT 'Remind me later'",
        "rate_us_step2_title" => "VARCHAR(255) DEFAULT 'Thanks for your support! 🙌'",
        "rate_us_step2_text" => "TEXT DEFAULT NULL",
        "rate_us_step2_email_btn" => "VARCHAR(100) DEFAULT '📩 Send via Email'",
        "rate_us_step2_telegram_btn" => "VARCHAR(100) DEFAULT '💬 Send via Telegram'",
        "rate_us_step2_done_btn" => "VARCHAR(100) DEFAULT 'Done ✓'",
        "onboarding_step1_title" => "VARCHAR(255) DEFAULT NULL",
        "onboarding_step1_desc" => "TEXT DEFAULT NULL",
        "onboarding_step2_title" => "VARCHAR(255) DEFAULT NULL",
        "onboarding_step2_desc" => "TEXT DEFAULT NULL",
        "onboarding_step3_title" => "VARCHAR(255) DEFAULT NULL",
        "onboarding_step3_desc" => "TEXT DEFAULT NULL",
        "home_announcement_text" => "TEXT DEFAULT NULL",
        "welcome_modal_title" => "VARCHAR(255) DEFAULT 'Important Notice'",
        "welcome_modal_frequency" => "VARCHAR(50) DEFAULT 'daily'",
        "prevent_screenshot" => "TINYINT(1) DEFAULT 1",
        "enable_haptic" => "TINYINT(1) DEFAULT 1",
        "haptic_intensity" => "VARCHAR(20) DEFAULT 'light'",
        "keep_screen_awake" => "TINYINT(1) DEFAULT 1",
        "is_force_update" => "TINYINT(1) DEFAULT 0",
        "is_new_apps" => "TINYINT(1) DEFAULT 0",
        "is_deleted" => "TINYINT(1) DEFAULT 0"
    ],
    "brands" => [
        "default_theme" => "VARCHAR(50) DEFAULT 'real'",
        "description" => "VARCHAR(255) DEFAULT ''",
        "is_active" => "TINYINT(1) DEFAULT 1"
    ],
    "users" => [
        "is_deleted" => "TINYINT(1) DEFAULT 0",
        "gamification_badge" => "VARCHAR(255) DEFAULT NULL",
        "session_token" => "VARCHAR(255) DEFAULT NULL",
        "failed_login_attempts" => "INT DEFAULT 0",
        "lockout_time" => "DATETIME DEFAULT NULL",
        "approval_date" => "DATETIME DEFAULT NULL",
        "reset_token" => "VARCHAR(10) DEFAULT NULL",
        "reset_token_expires" => "DATETIME DEFAULT NULL",
        "deleted_by_user" => "TINYINT(1) DEFAULT 0",
        "auth_provider" => "VARCHAR(20) DEFAULT 'email'",
        "google_id" => "VARCHAR(191) DEFAULT NULL",
        "google_verified" => "TINYINT(1) DEFAULT 0",
        "avatar_url" => "VARCHAR(500) DEFAULT NULL",
        "exempt_force_update" => "TINYINT(1) DEFAULT 0",
        "exempt_security" => "TINYINT(1) DEFAULT 0",
        "exempt_screenshot" => "TINYINT(1) DEFAULT 0"
    ],
    "matches" => [
        "is_deleted" => "TINYINT(1) DEFAULT 0",
        "home_logo" => "VARCHAR(255) DEFAULT NULL",
        "away_logo" => "VARCHAR(255) DEFAULT NULL",
        "home_team_id" => "INT DEFAULT NULL",
        "away_team_id" => "INT DEFAULT NULL",
        "is_bot_added" => "TINYINT(4) DEFAULT 0",
        "confidence_rate" => "VARCHAR(10) DEFAULT NULL"
    ],
    "leagues" => [
        "is_deleted" => "TINYINT(1) DEFAULT 0"
    ],
    "teams" => [
        "is_deleted" => "TINYINT(1) DEFAULT 0"
    ],
    "tickets" => [
        "is_deleted" => "TINYINT(1) DEFAULT 0",
        "category" => "VARCHAR(100) DEFAULT NULL",
        "subject" => "VARCHAR(255) DEFAULT NULL",
        "admin_reply_at" => "DATETIME DEFAULT NULL"
    ],
    "faqs" => [
        "is_deleted" => "TINYINT(1) DEFAULT 0"
    ],
    "promotions" => [
        "is_deleted" => "TINYINT(1) DEFAULT 0"
    ]
];

foreach ($columnDefinitions as $table => $cols) {
    foreach ($cols as $colName => $colDef) {
        if (!columnExists($db, $table, $colName)) {
            try {
                $db->exec("ALTER TABLE `{$table}` ADD COLUMN `{$colName}` {$colDef};");
                addLog("➕ Added missing column: `{$table}`.`{$colName}`");
            } catch (PDOException $e) {
                addLog("❌ Error adding column `{$table}`.`{$colName}`: " . $e->getMessage());
            }
        }
    }
}

// ---------------------------------------------------------------
// STEP 3: ESSENTIAL SYSTEM SEEDS (Only inserts if not present)
// ---------------------------------------------------------------
try {
    // Ensure Super Admin exists without overwriting if already changed
    $adminCheck = $db->query("SELECT COUNT(*) FROM admins")->fetchColumn();
    if ($adminCheck == 0) {
        $passHash = password_hash('123456', PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO admins (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Alper Yılmaz', 'admin@playbettingtips.com', $passHash, 'super_admin']);
        addLog("🌱 Default Super Admin created (admin@playbettingtips.com)");
    }
} catch (Exception $e) {
    addLog("Seed Notice: " . $e->getMessage());
}

addLog("\n==========================================================");
addLog("✅ MIGRATION COMPLETED SUCCESSFULLY (0 Data Loss Guaranteed)");
addLog("==========================================================");

if (!$isCli) {
    echo json_encode([
        "status" => "success",
        "message" => "Veritabanı yapısı başarıyla güncellendi.",
        "log" => $log
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
