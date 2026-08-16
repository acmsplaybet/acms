<?php
// api/admin/notifications.php
// Admin API for Push Notification Dispatch and Logs

require_once '../config/Database.php';
require_once '../config/onesignal_helper.php';
require_once 'audit_helper.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

if ($method === 'GET') {
    // 1. Get list of active apps with OneSignal configuration status
    if ($action === 'get_apps') {
        try {
            $stmt = $db->query("
                SELECT id, name, slug, app_type, onesignal_app_id, 
                       (CASE WHEN onesignal_app_id IS NOT NULL AND onesignal_app_id != '' AND onesignal_api_key IS NOT NULL AND onesignal_api_key != '' THEN 1 ELSE 0 END) as has_onesignal
                FROM apps 
                WHERE is_deleted = 0 
                ORDER BY name ASC
            ");
            $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $apps]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
        }
        exit;
    }

    // 2. Get Audience & OneSignal live subscriber counts
    if ($action === 'get_audience_stats') {
        try {
            $app_id = isset($_GET['app_id']) ? intval($_GET['app_id']) : 0;
            if (!$app_id) {
                echo json_encode(['status' => 'error', 'message' => 'App ID gereklidir.']);
                exit;
            }

            // 1. App Details
            $appStmt = $db->prepare("SELECT id, name, onesignal_app_id, onesignal_api_key FROM apps WHERE id = ?");
            $appStmt->execute([$app_id]);
            $app = $appStmt->fetch(PDO::FETCH_ASSOC);

            // 2. DB User Counts
            $stmtAll = $db->prepare("SELECT COUNT(*) FROM user_apps ua JOIN users u ON ua.user_id = u.id WHERE ua.app_id = ? AND u.is_deleted = 0");
            $stmtAll->execute([$app_id]);
            $totalUsers = (int)$stmtAll->fetchColumn();

            $stmtVip = $db->prepare("SELECT COUNT(*) FROM user_apps ua JOIN users u ON ua.user_id = u.id WHERE ua.app_id = ? AND ua.status = 'approved' AND u.is_deleted = 0");
            $stmtVip->execute([$app_id]);
            $vipUsers = (int)$stmtVip->fetchColumn();

            $stmtPending = $db->prepare("SELECT COUNT(*) FROM user_apps ua JOIN users u ON ua.user_id = u.id WHERE ua.app_id = ? AND ua.status = 'pending' AND u.is_deleted = 0");
            $stmtPending->execute([$app_id]);
            $pendingUsers = (int)$stmtPending->fetchColumn();

            // 3. Live OneSignal Subscribers Count & Segment Breakdown
            $onesignalTotal = null;
            $onesignalVip = null;
            $onesignalPending = null;
            $onesignalMessageable = null;

            if ($app && !empty($app['onesignal_app_id']) && !empty($app['onesignal_api_key'])) {
                $osAppId = trim($app['onesignal_app_id']);
                $osApiKey = trim($app['onesignal_api_key']);

                // Fetch App Summary
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/players?app_id=" . $osAppId . "&limit=300");
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Basic ' . $osApiKey,
                    'Content-Type: application/json; charset=utf-8'
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_TIMEOUT, 6);
                $osRes = curl_exec($ch);
                $osHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($osHttp >= 200 && $osHttp < 300 && $osRes) {
                    $osData = json_decode($osRes, true);
                    $players = $osData['players'] ?? [];
                    $onesignalTotal = intval($osData['total_count'] ?? count($players));
                    
                    $vipCount = 0;
                    $pendingCount = 0;
                    $msgCount = 0;

                    foreach ($players as $p) {
                        $tags = $p['tags'] ?? [];
                        $status = $tags['status'] ?? '';
                        if ($status === 'approved') {
                            $vipCount++;
                        } elseif ($status === 'pending') {
                            $pendingCount++;
                        }
                        if (empty($p['invalid_identifier'])) {
                            $msgCount++;
                        }
                    }

                    $onesignalVip = $vipCount;
                    $onesignalPending = $pendingCount;
                    $onesignalMessageable = $msgCount;
                }
            }

            echo json_encode([
                'status' => 'success',
                'data' => [
                    'app_id' => $app_id,
                    'db_total' => $totalUsers,
                    'db_vip' => $vipUsers,
                    'db_pending' => $pendingUsers,
                    'os_total' => $onesignalTotal,
                    'os_vip' => $onesignalVip,
                    'os_pending' => $onesignalPending,
                    'os_messageable' => $onesignalMessageable,
                    'has_onesignal' => !empty($app['onesignal_app_id']) && !empty($app['onesignal_api_key'])
                ]
            ]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    // 2. Get Notification Logs
    if ($action === 'list_logs') {
        try {
            $app_id = isset($_GET['app_id']) && $_GET['app_id'] !== '' ? intval($_GET['app_id']) : null;
            $page = max(1, intval($_GET['page'] ?? 1));
            $limit = max(10, min(100, intval($_GET['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;

            $where = "WHERE 1=1";
            $params = [];
            if ($app_id) {
                $where .= " AND l.app_id = :app_id";
                $params[':app_id'] = $app_id;
            }

            // Total count
            $countStmt = $db->prepare("SELECT COUNT(*) FROM notification_logs l $where");
            $countStmt->execute($params);
            $total = (int)$countStmt->fetchColumn();

            // Logs with App Name
            $sql = "
                SELECT l.*, a.name as app_name, u.name as target_user_name
                FROM notification_logs l
                LEFT JOIN apps a ON l.app_id = a.id
                LEFT JOIN users u ON l.target_user_id = u.id
                $where
                ORDER BY l.id DESC
                LIMIT :limit OFFSET :offset
            ";
            $stmt = $db->prepare($sql);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'status' => 'success',
                'data' => $logs,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
        }
        exit;
    }

    // 3. Search users for targeted user push
    if ($action === 'search_users') {
        try {
            $q = trim($_GET['q'] ?? '');
            $app_id = isset($_GET['app_id']) && $_GET['app_id'] !== '' ? intval($_GET['app_id']) : null;

            $where = "WHERE u.is_deleted = 0 AND (u.name LIKE ? OR u.email LIKE ? OR u.gpa_code LIKE ? OR u.id = ?)";
            $params = ["%$q%", "%$q%", "%$q%", is_numeric($q) ? intval($q) : 0];

            $join = "LEFT JOIN user_apps ua ON u.id = ua.user_id";
            if (!empty($app_id)) {
                $join .= " AND ua.app_id = ?";
                array_unshift($params, intval($app_id));
            }

            $sql = "
                SELECT u.id, u.name, u.email, u.gpa_code, COALESCE(ua.status, 'pending') as status
                FROM users u
                $join
                $where
                ORDER BY u.id DESC
                LIMIT 15
            ";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $users]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    // 4. Get Automated Notification Templates
    if ($action === 'get_template') {
        try {
            $app_id = intval($_GET['app_id'] ?? 0);
            $event_key = trim($_GET['event_key'] ?? 'vip_approval');

            $stmt = $db->prepare("SELECT * FROM notification_templates WHERE app_id = ? AND event_key = ?");
            $stmt->execute([$app_id, $event_key]);
            $tpl = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$tpl) {
                // Return default fallback
                $tpl = [
                    'app_id' => $app_id,
                    'event_key' => $event_key,
                    'name' => 'VIP Üyelik Onay Bildirimi',
                    'is_active' => 1,
                    'title_tr' => '🎉 VIP Üyeliğiniz Onaylandı!',
                    'message_tr' => 'Tebrikler {name}! Sipariş kodunuz onaylandı. Artık tüm VIP tahmin ve analizlere sınırsız erişebilirsiniz.',
                    'title_en' => '🎉 VIP Membership Approved!',
                    'message_en' => 'Congratulations {name}! Your order code has been approved. You now have full unlimited access to all VIP tips.',
                    'title_de' => '🎉 VIP-Mitgliedschaft Genehmigt!',
                    'message_de' => 'Herzlichen Glückwunsch {name}! Ihr Bestellcode wurde bestätigt. Sie haben jetzt unbegrenzten Zugriff auf alle VIP-Tipps.',
                    'title_es' => '🎉 ¡Membresía VIP Aprobada!',
                    'message_es' => '¡Felicitaciones {name}! Tu código de pedido ha sido aprobado. Ahora tienes acceso ilimitado a todas las predicciones VIP.',
                    'title_pt' => '🎉 Associação VIP Aprovada!',
                    'message_pt' => 'Parabéns {name}! Seu código de pedido foi aprovado. Agora você tem acesso ilimitado a todas as dicas VIP.',
                    'title_fr' => '🎉 Adhésion VIP Approuvée!',
                    'message_fr' => 'Félicitations {name}! Votre code de commande a été validé. Vous avez maintenant un accès illimité à tous les pronostics VIP.',
                    'deep_link' => '/app/tips'
                ];
            }

            echo json_encode(['status' => 'success', 'data' => $tpl]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (empty($input)) $input = $_POST;

    $action = $input['action'] ?? $action;

    // Save Notification Template
    if ($action === 'save_template') {
        try {
            $app_id = intval($input['app_id'] ?? 0);
            $event_key = trim($input['event_key'] ?? 'vip_approval');
            $name = trim($input['name'] ?? 'VIP Üyelik Onay Bildirimi');
            $is_active = isset($input['is_active']) ? (int)$input['is_active'] : 1;
            $deep_link = trim($input['deep_link'] ?? '/app/tips');

            $title_tr = trim($input['title_tr'] ?? '');
            $message_tr = trim($input['message_tr'] ?? '');
            $title_en = trim($input['title_en'] ?? '');
            $message_en = trim($input['message_en'] ?? '');
            $title_de = trim($input['title_de'] ?? '');
            $message_de = trim($input['message_de'] ?? '');
            $title_es = trim($input['title_es'] ?? '');
            $message_es = trim($input['message_es'] ?? '');
            $title_pt = trim($input['title_pt'] ?? '');
            $message_pt = trim($input['message_pt'] ?? '');
            $title_fr = trim($input['title_fr'] ?? '');
            $message_fr = trim($input['message_fr'] ?? '');

            if (!$app_id || empty($title_tr) || empty($message_tr)) {
                echo json_encode(['status' => 'error', 'message' => 'Lütfen en az Türkçe başlık ve mesaj alanını doldurun.']);
                exit;
            }

            $sql = "
                INSERT INTO notification_templates
                (app_id, event_key, name, is_active, title_tr, message_tr, title_en, message_en, title_de, message_de, title_es, message_es, title_pt, message_pt, title_fr, message_fr, deep_link)
                VALUES
                (:app_id, :event_key, :name, :is_active, :title_tr, :message_tr, :title_en, :message_en, :title_de, :message_de, :title_es, :message_es, :title_pt, :message_pt, :title_fr, :message_fr, :deep_link)
                ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                is_active = VALUES(is_active),
                title_tr = VALUES(title_tr),
                message_tr = VALUES(message_tr),
                title_en = VALUES(title_en),
                message_en = VALUES(message_en),
                title_de = VALUES(title_de),
                message_de = VALUES(message_de),
                title_es = VALUES(title_es),
                message_es = VALUES(message_es),
                title_pt = VALUES(title_pt),
                message_pt = VALUES(message_pt),
                title_fr = VALUES(title_fr),
                message_fr = VALUES(message_fr),
                deep_link = VALUES(deep_link),
                updated_at = NOW()
            ";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':app_id' => $app_id,
                ':event_key' => $event_key,
                ':name' => $name,
                ':is_active' => $is_active,
                ':title_tr' => $title_tr,
                ':message_tr' => $message_tr,
                ':title_en' => $title_en,
                ':message_en' => $message_en,
                ':title_de' => $title_de,
                ':message_de' => $message_de,
                ':title_es' => $title_es,
                ':message_es' => $message_es,
                ':title_pt' => $title_pt,
                ':message_pt' => $message_pt,
                ':title_fr' => $title_fr,
                ':message_fr' => $message_fr,
                ':deep_link' => $deep_link
            ]);

            log_action($db, 'save_notification_template', "Saved template $event_key for App ID $app_id");
            echo json_encode(['status' => 'success', 'message' => 'Otomatik bildirim şablonu başarıyla kaydedildi.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    // Send Push Notification
    if ($action === 'send') {
        $app_id = intval($input['app_id'] ?? 0);
        $title = trim($input['title'] ?? '');
        $message = trim($input['message'] ?? '');
        $target_segment = $input['target_segment'] ?? 'all';
        $target_user_id = !empty($input['target_user_id']) ? intval($input['target_user_id']) : null;
        $image_url = !empty($input['image_url']) ? trim($input['image_url']) : null;
        $deep_link = !empty($input['deep_link']) ? trim($input['deep_link']) : '/app/tips';
        $is_test = !empty($input['is_test']) && in_array($input['is_test'], [true, 1, '1', 'true'], true);

        if (!$app_id || empty($title) || empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Lütfen Uygulama, Başlık ve Mesaj alanlarını eksiksiz doldurun.']);
            exit;
        }

        $result = send_onesignal_notification(
            $db,
            $app_id,
            $title,
            $message,
            $target_segment,
            $target_user_id,
            $image_url,
            $deep_link,
            $is_test
        );

        if ($result['status'] === 'success') {
            log_action($db, 'send_push_notification', "Push sent for App ID: $app_id, Segment: $target_segment, Title: $title");
        }

        echo json_encode($result);
        exit;
    }

    // Delete a log
    if ($action === 'delete_log') {
        $log_id = intval($input['id'] ?? 0);
        if (!$log_id) {
            echo json_encode(['status' => 'error', 'message' => 'Geçersiz Log ID']);
            exit;
        }
        try {
            $stmt = $db->prepare("DELETE FROM notification_logs WHERE id = ?");
            $stmt->execute([$log_id]);
            echo json_encode(['status' => 'success', 'message' => 'Bildirim kaydı silindi.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    // Clear all logs
    if ($action === 'clear_logs') {
        $app_id = !empty($input['app_id']) ? intval($input['app_id']) : null;
        try {
            if ($app_id) {
                $stmt = $db->prepare("DELETE FROM notification_logs WHERE app_id = ?");
                $stmt->execute([$app_id]);
            } else {
                $db->exec("TRUNCATE TABLE notification_logs");
            }
            log_action($db, 'clear_notification_logs', "Cleared notification logs for app: " . ($app_id ?: 'ALL'));
            echo json_encode(['status' => 'success', 'message' => 'Bildirim geçmişi temizlendi.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek.']);
