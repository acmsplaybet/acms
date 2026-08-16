<?php
require_once '../config/Database.php';
require_once 'audit_helper.php';
header('Content-Type: application/json; charset=utf-8');

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // If we just want brands list for the select box
    if(isset($_GET['action']) && $_GET['action'] == 'get_brands') {
        try {
            $stmt = $db->query("SELECT COUNT(*) FROM brands WHERE is_active = 1");
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                // Auto-insert sample brands
                $db->exec("INSERT INTO brands (name, slug, is_active) VALUES 
                    ('Real Mobile Bet', 'real-mobile', 1), 
                    ('Alex Betting Tips', 'alex-tips', 1), 
                    ('Pep Predictions', 'pep-predict', 1)");
            }

            $stmt = $db->query("SELECT id, name FROM brands WHERE is_active = 1 ORDER BY name ASC");
            $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $brands]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
        }
        exit;
    }

    // Get single app for editing
    if(isset($_GET['action']) && $_GET['action'] == 'get_app' && isset($_GET['id'])) {
        try {
            $stmt = $db->prepare("SELECT * FROM apps WHERE id = ? AND is_deleted = 0");
            $stmt->execute([intval($_GET['id'])]);
            $app = $stmt->fetch(PDO::FETCH_ASSOC);
            if($app) {
                echo json_encode(['status' => 'success', 'data' => $app]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Uygulama bulunamadı.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
        }
        exit;
    }

    // List all apps
    try {
        $whereClause = "WHERE a.is_deleted = 0";
        $params = [];
        if (isset($_GET['brand_id']) && $_GET['brand_id'] !== '') {
            $whereClause .= " AND a.brand_id = ?";
            $params[] = intval($_GET['brand_id']);
        }

        $query = "
            SELECT 
                a.id, a.name as app_name, a.app_type, a.frontend_url, a.is_active, 
                b.name as brand_name 
            FROM apps a
            LEFT JOIN brands b ON a.brand_id = b.id
            $whereClause
            ORDER BY a.id DESC
        ";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'success', 'data' => $apps]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
    exit;
}

if ($method === 'POST') {
    $data = $_POST;
    if(empty($data)) {
        $raw = json_decode(file_get_contents("php://input"), true);
        if(is_array($raw)) $data = $raw;
    }
    
    if(!isset($data['name']) && !isset($data['action'])) {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz veri gönderimi.']);
        exit;
    }

    // Handle Delete
    if(isset($data['action']) && $data['action'] === 'delete' && isset($data['id'])) {
        try {
            $del_id = intval($data['id']);
            $nameStmt = $db->prepare("SELECT name FROM apps WHERE id = ?");
            $nameStmt->execute([$del_id]);
            $app_name = $nameStmt->fetchColumn() ?: 'Bilinmeyen Uygulama';
            
            $stmt = $db->prepare("UPDATE apps SET is_deleted = 1 WHERE id = ?");
            $stmt->execute([$del_id]);
            log_action($db, 'delete_app', "App ID: $del_id ($app_name) deleted.");
            echo json_encode(['status' => 'success', 'message' => 'Uygulama başarıyla silindi (Soft Delete).']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Silme hatası: ' . $e->getMessage()]);
        }
        exit;
    }

    if(!isset($data['name']) || !isset($data['brand_id']) || !isset($data['frontend_url']) || !isset($data['slug'])) {
        echo json_encode(['status' => 'error', 'message' => 'Lütfen zorunlu alanları (Marka, Ad, Slug, URL) doldurun.']);
        exit;
    }

    // Basic
    $brand_id = intval($data['brand_id']);
    $name = trim($data['name']);
    $slug = trim($data['slug']);
    $app_type = $data['app_type'] ?? 'free';
    $theme = $data['theme'] ?? 'real';
    $price = isset($data['price']) ? floatval($data['price']) : 0.00;
    $frontend_url = trim($data['frontend_url']);

    // Design
    $primary_color = $data['primary_color'] ?? '#000000';
    $secondary_color = $data['secondary_color'] ?? '#333333';
    $accent_color = $data['accent_color'] ?? '#FF0000';
    $bg_color = $data['bg_color'] ?? '#060d1a';
    $font_family = $data['font_family'] ?? 'Inter';
    $logo_url = $data['logo_url'] ?? null;
    $favicon_url = $data['favicon_url'] ?? null;

    // File Uploads Handling
    $uploadDir = __DIR__ . '/../../admin/uploads/apps/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('logo_') . '.' . $ext;
        if(move_uploaded_file($_FILES['logo_file']['tmp_name'], $uploadDir . $filename)) {
            $logo_url = 'uploads/apps/' . $filename;
        }
    }
    if (isset($_FILES['favicon_file']) && $_FILES['favicon_file']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['favicon_file']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('fav_') . '.' . $ext;
        if(move_uploaded_file($_FILES['favicon_file']['tmp_name'], $uploadDir . $filename)) {
            $favicon_url = 'uploads/apps/' . $filename;
        }
    }

    // Security & Tech
    $user_agent = $data['user_agent'] ?? ($data['custom_user_agent'] ?? '');
    $is_ios_allowed = isset($data['is_ios_allowed']) && in_array($data['is_ios_allowed'], ['1', 1, true, 'on'], true) ? 1 : 0;
    $maintenance_mode = isset($data['maintenance_mode']) && in_array($data['maintenance_mode'], ['1', 1, true, 'on'], true) ? 1 : 0;
    $forced_login = isset($data['forced_login']) && in_array($data['forced_login'], ['1', 1, true, 'on'], true) ? 1 : 0;
    $prevent_screenshot = isset($data['prevent_screenshot']) && in_array($data['prevent_screenshot'], ['1', 1, true, 'on'], true) ? 1 : 0;
    $welcome_modal_active = isset($data['welcome_modal_active']) && in_array($data['welcome_modal_active'], ['1', 1, true, 'on'], true) ? 1 : 0;
    $is_force_update = isset($data['is_force_update']) && in_array($data['is_force_update'], ['1', 1, true, 'on'], true) ? 1 : 0;
    $app_version = $data['app_version'] ?? '1.0.0';
    $min_version = $data['min_version'] ?? '1.0.0';
    $min_required_version = $data['min_required_version'] ?? '1.0.0';
    $history_limit_days = isset($data['history_limit_days']) ? intval($data['history_limit_days']) : (isset($data['past_days_limit']) ? intval($data['past_days_limit']) : 10);
    $guest_tips_limit = isset($data['guest_tips_limit']) ? intval($data['guest_tips_limit']) : 3;

    // Contact
    $contact_telegram = $data['contact_telegram'] ?? null;
    $contact_email = $data['contact_email'] ?? null;
    $contact_whatsapp = $data['contact_whatsapp'] ?? null;
    $contact_instagram = $data['contact_instagram'] ?? null;
    $contact_telegram_response = $data['contact_telegram_response'] ?? null;
    $contact_email_response = $data['contact_email_response'] ?? null;
    $contact_whatsapp_response = $data['contact_whatsapp_response'] ?? null;
    $contact_instagram_response = $data['contact_instagram_response'] ?? null;

    // Integrations
    $onesignal_app_id = $data['onesignal_app_id'] ?? null;
    $onesignal_api_key = $data['onesignal_api_key'] ?? null;
    $custom_scripts = $data['custom_scripts'] ?? ($data['custom_script'] ?? null);
    $tawk_to_id = $data['tawk_to_id'] ?? null;

    // UX & Metinler
    $legal_texts_json = $data['legal_texts_json'] ?? null;
    $announcement_popup = $data['announcement_popup'] ?? null;
    $welcome_modal_text = $data['welcome_modal_text'] ?? null;
    $guide_step_1 = $data['guide_step_1'] ?? null;
    $guide_step_2 = $data['guide_step_2'] ?? null;
    $guide_step_3 = $data['guide_step_3'] ?? null;
    $post_register_text = $data['post_register_text'] ?? null;
    $empty_state_text = $data['empty_state_text'] ?? null;
    $rate_us_active = isset($data['rate_us_active']) && in_array($data['rate_us_active'], ['1', 1, true, 'on'], true) ? 1 : 0;
    $rate_us_title = $data['rate_us_title'] ?? null;
    $rate_us_text = $data['rate_us_text'] ?? null;
    $rate_us_reward = $data['rate_us_reward'] ?? null;
    $rate_us_snooze_days = isset($data['rate_us_snooze_days']) ? intval($data['rate_us_snooze_days']) : 3;
    $rate_us_rate_btn_text = $data['rate_us_rate_btn_text'] ?? null;
    $rate_us_later_btn_text = $data['rate_us_later_btn_text'] ?? null;
    $rate_us_step2_title = $data['rate_us_step2_title'] ?? null;
    $rate_us_step2_text = $data['rate_us_step2_text'] ?? null;
    $rate_us_step2_email_btn = $data['rate_us_step2_email_btn'] ?? null;
    $rate_us_step2_telegram_btn = $data['rate_us_step2_telegram_btn'] ?? null;
    $rate_us_step2_done_btn = $data['rate_us_step2_done_btn'] ?? null;
    $play_store_link = $data['play_store_link'] ?? null;
    $vip_hub_description = $data['vip_hub_description'] ?? null;
    
    $onboarding_step1_title = $data['onboarding_step1_title'] ?? null;
    $onboarding_step1_desc = $data['onboarding_step1_desc'] ?? null;
    $onboarding_step2_title = $data['onboarding_step2_title'] ?? null;
    $onboarding_step2_desc = $data['onboarding_step2_desc'] ?? null;
    $onboarding_step3_title = $data['onboarding_step3_title'] ?? null;
    $onboarding_step3_desc = $data['onboarding_step3_desc'] ?? null;
    $home_announcement_text = $data['home_announcement_text'] ?? null;
    $welcome_modal_title = $data['welcome_modal_title'] ?? 'Important Notice';
    $welcome_modal_frequency = $data['welcome_modal_frequency'] ?? 'daily';
    
    // Haptic & Hardware Controls
    $enable_haptic = isset($data['enable_haptic']) && in_array($data['enable_haptic'], ['1', 1, true, 'on'], true) ? 1 : 0;
    $haptic_intensity = $data['haptic_intensity'] ?? 'light';
    $keep_screen_awake = isset($data['keep_screen_awake']) && in_array($data['keep_screen_awake'], ['1', 1, true, 'on'], true) ? 1 : 0;

    // Auto-migration for all app settings columns
    try {
        $db->exec("ALTER TABLE apps ADD COLUMN IF NOT EXISTS is_force_update TINYINT(1) DEFAULT 0");
        $db->exec("ALTER TABLE apps ADD COLUMN IF NOT EXISTS welcome_modal_title VARCHAR(255) DEFAULT 'Important Notice'");
        $db->exec("ALTER TABLE apps ADD COLUMN IF NOT EXISTS welcome_modal_frequency VARCHAR(50) DEFAULT 'daily'");
        $db->exec("ALTER TABLE apps ADD COLUMN IF NOT EXISTS prevent_screenshot TINYINT(1) DEFAULT 1");
        $db->exec("ALTER TABLE apps ADD COLUMN IF NOT EXISTS enable_haptic TINYINT(1) DEFAULT 1");
        $db->exec("ALTER TABLE apps ADD COLUMN IF NOT EXISTS haptic_intensity VARCHAR(20) DEFAULT 'light'");
        $db->exec("ALTER TABLE apps ADD COLUMN IF NOT EXISTS keep_screen_awake TINYINT(1) DEFAULT 1");
    } catch (Exception $e) {
        try { $db->exec("ALTER TABLE apps ADD COLUMN is_force_update TINYINT(1) DEFAULT 0"); } catch (Exception $e2) {}
        try { $db->exec("ALTER TABLE apps ADD COLUMN welcome_modal_title VARCHAR(255) DEFAULT 'Important Notice'"); } catch (Exception $e2) {}
        try { $db->exec("ALTER TABLE apps ADD COLUMN welcome_modal_frequency VARCHAR(50) DEFAULT 'daily'"); } catch (Exception $e2) {}
        try { $db->exec("ALTER TABLE apps ADD COLUMN prevent_screenshot TINYINT(1) DEFAULT 1"); } catch (Exception $e2) {}
        try { $db->exec("ALTER TABLE apps ADD COLUMN enable_haptic TINYINT(1) DEFAULT 1"); } catch (Exception $e2) {}
        try { $db->exec("ALTER TABLE apps ADD COLUMN haptic_intensity VARCHAR(20) DEFAULT 'light'"); } catch (Exception $e2) {}
        try { $db->exec("ALTER TABLE apps ADD COLUMN keep_screen_awake TINYINT(1) DEFAULT 1"); } catch (Exception $e2) {}
    }

    try {
        if(isset($data['action']) && $data['action'] === 'update' && isset($data['id'])) {
            $app_id = intval($data['id']);
            
            // Preserve old URLs if not uploaded
            $stmt = $db->prepare("SELECT logo_url, favicon_url FROM apps WHERE id = ?");
            $stmt->execute([$app_id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$logo_url && $existing) $logo_url = $existing['logo_url'];
            if (!$favicon_url && $existing) $favicon_url = $existing['favicon_url'];
            $stmt = $db->prepare("
                UPDATE apps SET 
                    brand_id=?, theme=?, name=?, slug=?, app_type=?, price=?, frontend_url=?, 
                    primary_color=?, secondary_color=?, accent_color=?, bg_color=?, font_family=?, logo_url=?, favicon_url=?,
                    user_agent=?, is_ios_allowed=?, min_version=?, min_required_version=?, is_force_update=?, history_limit_days=?,
                    onesignal_app_id=?, onesignal_api_key=?, custom_scripts=?, tawk_to_id=?,
                    legal_texts_json=?, announcement_popup=?, maintenance_mode=?, app_version=?,
                    contact_telegram=?, contact_email=?, contact_whatsapp=?, contact_instagram=?,
                    contact_telegram_response=?, contact_email_response=?, contact_whatsapp_response=?, contact_instagram_response=?,
                    forced_login=?, prevent_screenshot=?, welcome_modal_active=?, welcome_modal_text=?, welcome_modal_title=?, welcome_modal_frequency=?, guest_tips_limit=?,
                    enable_haptic=?, haptic_intensity=?, keep_screen_awake=?,
                    guide_step_1=?, guide_step_2=?, guide_step_3=?, post_register_text=?,
                    empty_state_text=?, rate_us_active=?, rate_us_title=?, rate_us_text=?, rate_us_reward=?, rate_us_snooze_days=?,
                    rate_us_rate_btn_text=?, rate_us_later_btn_text=?, rate_us_step2_title=?, rate_us_step2_text=?,
                    rate_us_step2_email_btn=?, rate_us_step2_telegram_btn=?, rate_us_step2_done_btn=?, play_store_link=?, vip_hub_description=?,
                    onboarding_step1_title=?, onboarding_step1_desc=?, onboarding_step2_title=?, onboarding_step2_desc=?, onboarding_step3_title=?, onboarding_step3_desc=?, home_announcement_text=?
                WHERE id = ?
            ");
            $stmt->execute([
                $brand_id, $theme, $name, $slug, $app_type, $price, $frontend_url,
                $primary_color, $secondary_color, $accent_color, $bg_color, $font_family, $logo_url, $favicon_url,
                $user_agent, $is_ios_allowed, $min_version, $min_required_version, $is_force_update, $history_limit_days,
                $onesignal_app_id, $onesignal_api_key, $custom_scripts, $tawk_to_id,
                $legal_texts_json, $announcement_popup, $maintenance_mode, $app_version,
                $contact_telegram, $contact_email, $contact_whatsapp, $contact_instagram,
                $contact_telegram_response, $contact_email_response, $contact_whatsapp_response, $contact_instagram_response,
                $forced_login, $prevent_screenshot, $welcome_modal_active, $welcome_modal_text, $welcome_modal_title, $welcome_modal_frequency, $guest_tips_limit,
                $enable_haptic, $haptic_intensity, $keep_screen_awake,
                $guide_step_1, $guide_step_2, $guide_step_3, $post_register_text,
                $empty_state_text, $rate_us_active, $rate_us_title, $rate_us_text, $rate_us_reward, $rate_us_snooze_days,
                $rate_us_rate_btn_text, $rate_us_later_btn_text, $rate_us_step2_title, $rate_us_step2_text,
                $rate_us_step2_email_btn, $rate_us_step2_telegram_btn, $rate_us_step2_done_btn, $play_store_link, $vip_hub_description,
                $onboarding_step1_title, $onboarding_step1_desc, $onboarding_step2_title, $onboarding_step2_desc, $onboarding_step3_title, $onboarding_step3_desc, $home_announcement_text,
                intval($data['id'])
            ]);
            log_action($db, 'update_app', "App ID: $app_id ($name) updated.");
            echo json_encode(['status' => 'success', 'message' => 'Uygulama başarıyla güncellendi.']);
        } else {
            $stmt = $db->prepare("
                INSERT INTO apps (
                    brand_id, theme, name, slug, app_type, price, frontend_url, 
                    primary_color, secondary_color, accent_color, bg_color, font_family, logo_url, favicon_url,
                    user_agent, is_ios_allowed, min_version, min_required_version, is_force_update, history_limit_days,
                    onesignal_app_id, onesignal_api_key, custom_scripts, tawk_to_id,
                    legal_texts_json, announcement_popup, maintenance_mode, app_version,
                    contact_telegram, contact_email, contact_whatsapp, contact_instagram,
                    contact_telegram_response, contact_email_response, contact_whatsapp_response, contact_instagram_response,
                    forced_login, prevent_screenshot, welcome_modal_active, welcome_modal_text, welcome_modal_title, welcome_modal_frequency, guest_tips_limit,
                    enable_haptic, haptic_intensity, keep_screen_awake,
                    guide_step_1, guide_step_2, guide_step_3, post_register_text,
                    empty_state_text, rate_us_active, rate_us_title, rate_us_text, rate_us_reward, rate_us_snooze_days,
                    rate_us_rate_btn_text, rate_us_later_btn_text, rate_us_step2_title, rate_us_step2_text,
                    rate_us_step2_email_btn, rate_us_step2_telegram_btn, rate_us_step2_done_btn, play_store_link, vip_hub_description,
                    onboarding_step1_title, onboarding_step1_desc, onboarding_step2_title, onboarding_step2_desc, onboarding_step3_title, onboarding_step3_desc, home_announcement_text
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?, ?,
                    ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?, ?
                )
            ");
            
            $stmt->execute([
                $brand_id, $theme, $name, $slug, $app_type, $price, $frontend_url,
                $primary_color, $secondary_color, $accent_color, $bg_color, $font_family, $logo_url, $favicon_url,
                $user_agent, $is_ios_allowed, $min_version, $min_required_version, $is_force_update, $history_limit_days,
                $onesignal_app_id, $onesignal_api_key, $custom_scripts, $tawk_to_id,
                $legal_texts_json, $announcement_popup, $maintenance_mode, $app_version,
                $contact_telegram, $contact_email, $contact_whatsapp, $contact_instagram,
                $contact_telegram_response, $contact_email_response, $contact_whatsapp_response, $contact_instagram_response,
                $forced_login, $prevent_screenshot, $welcome_modal_active, $welcome_modal_text, $welcome_modal_title, $welcome_modal_frequency, $guest_tips_limit,
                $enable_haptic, $haptic_intensity, $keep_screen_awake,
                $guide_step_1, $guide_step_2, $guide_step_3, $post_register_text,
                $empty_state_text, $rate_us_active, $rate_us_title, $rate_us_text, $rate_us_reward, $rate_us_snooze_days,
                $rate_us_rate_btn_text, $rate_us_later_btn_text, $rate_us_step2_title, $rate_us_step2_text,
                $rate_us_step2_email_btn, $rate_us_step2_telegram_btn, $rate_us_step2_done_btn, $play_store_link, $vip_hub_description,
                $onboarding_step1_title, $onboarding_step1_desc, $onboarding_step2_title, $onboarding_step2_desc, $onboarding_step3_title, $onboarding_step3_desc, $home_announcement_text
            ]);
            
            $new_app_id = $db->lastInsertId();
            log_action($db, 'add_app', "App ID: $new_app_id ($name) added.");
            echo json_encode(['status' => 'success', 'message' => 'Uygulama başarıyla eklendi.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Kayıt hatası: ' . $e->getMessage()]);
    }
    exit;
}
