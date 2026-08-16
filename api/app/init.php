<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/config.php';
require_once '../config/Database.php';

$app_id = isset($_GET['app_id']) ? intval($_GET['app_id']) : null;
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : null;

if (!$app_id && !$slug) {
    echo json_encode(['status' => 'error', 'message' => 'App ID or Slug is required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Uygulamayı ve Marka(Theme) bilgisini bul
    $sql = "
        SELECT a.*, b.slug as brand_slug 
        FROM apps a
        LEFT JOIN brands b ON a.brand_id = b.id
        WHERE a.is_deleted = 0 
    ";
    
    $params = [];
    if ($app_id) {
        $sql .= " AND a.id = :id";
        $params[':id'] = $app_id;
    } else {
        $sql .= " AND a.slug = :slug";
        $params[':slug'] = $slug;
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$app) {
        echo json_encode(['status' => 'error', 'message' => 'App not found']);
        exit;
    }
    
    // Legal Metinleri Ayrıştır
    $legal = ['privacy' => '', 'terms' => '', 'about' => ''];
    if (!empty($app['legal_texts_json'])) {
        $parsed = json_decode($app['legal_texts_json'], true);
        if(is_array($parsed)) {
            $legal['privacy'] = $parsed['privacy'] ?? '';
            $legal['terms'] = $parsed['terms'] ?? '';
            $legal['about'] = $parsed['about'] ?? '';
        }
    }
    
    // Aktif Promosyonu Bul
    $promo = null;
    $promoStmt = $db->prepare("
        SELECT id, title, badge_text as badge, end_date 
        FROM promotions 
        WHERE app_id = :app_id AND status = 'active' AND is_deleted = 0 AND (end_date IS NULL OR end_date >= NOW())
        ORDER BY id DESC LIMIT 1
    ");
    $promoStmt->execute([':app_id' => $app['id']]);
    $promoData = $promoStmt->fetch(PDO::FETCH_ASSOC);
    if ($promoData) {
        $promo = [
            'id' => (int)$promoData['id'],
            'title' => $promoData['title'],
            'badge' => $promoData['badge'],
            'end_date' => $promoData['end_date'],
            'target_url' => ''
        ];
    }
    
    // VIP Hub (Aynı Markadaki Diğer Aktif Uygulamalar)
    $vipHub = [];
    $vipStmt = $db->prepare("
        SELECT id, name, slug, logo_url, play_store_link, vip_hub_description 
        FROM apps 
        WHERE brand_id = :brand_id AND id != :current_app_id AND is_deleted = 0
    ");
    $vipStmt->execute([
        ':brand_id' => $app['brand_id'],
        ':current_app_id' => $app['id']
    ]);
    while($v = $vipStmt->fetch(PDO::FETCH_ASSOC)) {
        $vipHub[] = [
            'id' => (int)$v['id'],
            'name' => $v['name'] ?? '',
            'slug' => $v['slug'] ?? '',
            'logo_url' => $v['logo_url'] ?? '',
            'play_store_link' => $v['play_store_link'] ?? '',
            'vip_hub_description' => $v['vip_hub_description'] ?? ''
        ];
    }
    
    // Base URL formatlayıcı
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/acms/";
    $logoUrl = !empty($app['logo_url']) ? $baseUrl . 'admin/' . $app['logo_url'] : '';
    
    // Response Yapısı Oluştur
    $response = [
        'status' => 'success',
        'data' => [
            'app_id' => (int)$app['id'],
            'app_name' => $app['name'] ?? '',
            'slug' => $app['slug'] ?? '',
            'theme' => $app['theme'] ?? 'real',
            'logo_url' => $logoUrl,
            'primary_color' => $app['primary_color'] ?? '#000000',
            'secondary_color' => $app['secondary_color'] ?? '#333333',
            'accent_color' => $app['accent_color'] ?? '#FF0000',
            'bg_color' => $app['bg_color'] ?? '#060d1a',
            'font_family' => $app['font_family'] ?? 'Inter',
            'maintenance' => (bool)$app['maintenance_mode'],
            'min_required_version' => $app['min_required_version'] ?? '1.0.0',
            'web_version' => $app['app_version'] ?? '1.0.0',
            'forced_login' => (bool)$app['forced_login'],
            'prevent_screenshot' => (bool)($app['prevent_screenshot'] ?? 1),
            'enable_haptic' => (bool)($app['enable_haptic'] ?? 1),
            'haptic_intensity' => $app['haptic_intensity'] ?? 'light',
            'keep_screen_awake' => (bool)($app['keep_screen_awake'] ?? 1),
            'guest_tips_limit' => (int)($app['guest_tips_limit'] ?? 3),
            'play_store_link' => $app['play_store_link'] ?? '',
            'onesignal_app_id' => $app['onesignal_app_id'] ?? '',
            'appmetrica_key' => $app['appmetrica_key'] ?? '',
            
            'social' => [
                'telegram' => $app['contact_telegram'] ?? '',
                'whatsapp' => $app['contact_whatsapp'] ?? '',
                'instagram' => $app['contact_instagram'] ?? ''
            ],
            
            'contact' => [
                'email'               => $app['contact_email'] ?? '',
                'telegram'            => $app['contact_telegram'] ?? '',
                'whatsapp'            => $app['contact_whatsapp'] ?? '',
                'instagram'           => $app['contact_instagram'] ?? '',
                'telegram_response'   => $app['contact_telegram_response'] ?? '~1–2 hours',
                'whatsapp_response'   => $app['contact_whatsapp_response'] ?? '~1–2 hours',
                'instagram_response'  => $app['contact_instagram_response'] ?? '~24 hours',
                'email_response'      => $app['contact_email_response'] ?? '~24 hours',
            ],
            
            'tawk_to_id' => $app['tawk_to_id'] ?? '',
            
            'announcement_modal' => [
                'active'    => (bool)($app['welcome_modal_active'] ?? 0),
                'title'     => $app['welcome_modal_title'] ?? 'Important Notice',
                'text'      => $app['welcome_modal_text'] ?? '',
                'frequency' => $app['welcome_modal_frequency'] ?? 'daily'
            ],
            
            'guide_steps' => [
                $app['guide_step_1'] ?? '',
                $app['guide_step_2'] ?? '',
                $app['guide_step_3'] ?? ''
            ],
            
            'post_register_text' => $app['post_register_text'] ?? '',
            'empty_state_text' => $app['empty_state_text'] ?? '',
            'home_announcement_text' => $app['home_announcement_text'] ?? '',
            
            'onboarding_steps' => [
                ['title' => $app['onboarding_step1_title'] ?? 'High Win Rate', 'desc' => $app['onboarding_step1_desc'] ?? 'Get access to premium betting tips with a highly proven success record.'],
                ['title' => $app['onboarding_step2_title'] ?? 'Daily Safe Picks', 'desc' => $app['onboarding_step2_desc'] ?? 'Our experts analyze hundreds of matches to bring you the safest picks daily.'],
                ['title' => $app['onboarding_step3_title'] ?? 'Join the VIP Family', 'desc' => $app['onboarding_step3_desc'] ?? 'Become a VIP member today and start winning consistently.']
            ],
            'rate_us' => [
                'active'            => ($app['rate_us_active'] == 1),
                'title'             => $app['rate_us_title'] ?? 'Enjoying the App? ⭐',
                'text'              => $app['rate_us_text'] ?? '',
                'reward'            => $app['rate_us_reward'] ?? '',
                'snooze_days'       => (int)($app['rate_us_snooze_days'] ?? 3),
                'rate_btn_text'     => $app['rate_us_rate_btn_text'] ?? '⭐ Rate on Google Play',
                'later_btn_text'    => $app['rate_us_later_btn_text'] ?? 'Remind me later',
                'step2_title'       => $app['rate_us_step2_title'] ?? 'Thanks for your support! 🙌',
                'step2_text'        => $app['rate_us_step2_text'] ?? '',
                'step2_email_btn'   => $app['rate_us_step2_email_btn'] ?? '📩 Send via Email',
                'step2_telegram_btn'=> $app['rate_us_step2_telegram_btn'] ?? '💬 Send via Telegram',
                'step2_done_btn'    => $app['rate_us_step2_done_btn'] ?? 'Done ✓',
            ],
            
            'active_promo' => $promo,
            'vip_hub_apps' => $vipHub,
            
            'privacy_policy' => $legal['privacy'],
            'terms_of_use' => $legal['terms'],
            'about_us' => $legal['about']
        ]
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
