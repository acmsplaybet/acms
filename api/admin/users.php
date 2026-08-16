<?php
require_once '../config/config.php';
require_once '../config/Database.php';
require_once '../config/onesignal_helper.php';
require_once 'audit_helper.php';
// GÜVENLİK NOTU: Bu uç nokta henüz sunucu tarafında kimlik doğrulaması YAPMAMAKTADIR.
// Panel token'ı sadece istemci tarafında tutulmaktadır.
// Ortak bir auth katmanı (JWT/Session) yazılana kadar bu dosya açık kabul edilmelidir.

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

$db = Database::getInstance()->getConnection();

if ($action === 'list') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }

    try {
        $where = ["u.is_deleted = 0"];
        $params = [];

        if (isset($_GET['status']) && $_GET['status'] !== '') {
            if ($_GET['status'] === 'banned') {
                $where[] = "u.is_banned = 1";
            } else {
                $where[] = "ua.status = :status";
                $where[] = "u.is_banned = 0";
                $params[':status'] = $_GET['status'];
            }
        }

        if (!empty($_GET['app_id'])) {
            $where[] = "ua.app_id = :app_id";
            $params[':app_id'] = $_GET['app_id'];
        }

        $whereClause = implode(" AND ", $where);

        $stmt = $db->prepare("
            SELECT 
                u.id as user_id, 
                u.name, 
                u.email, 
                u.gpa_code, 
                u.auth_provider,
                u.google_verified,
                u.avatar_url,
                u.is_banned,
                u.exempt_security,
                u.exempt_screenshot,
                u.deleted_by_user,
                u.last_login_ip, 
                u.created_at,
                a.id as app_id,
                a.name as app_name,
                ua.status
            FROM user_apps ua
            INNER JOIN users u ON ua.user_id = u.id
            INNER JOIN apps a ON ua.app_id = a.id
            WHERE $whereClause
            ORDER BY ua.id DESC
        ");
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'success', 'data' => $users]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
elseif ($action === 'update_status') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $user_id = $input['user_id'] ?? null;
    $app_id = $input['app_id'] ?? null;
    $new_status = $input['new_status'] ?? null;

    if (!$user_id || !$app_id || !in_array($new_status, ['approved', 'rejected', 'pending'])) {
        echo json_encode(['status' => 'error', 'message' => 'Eksik veya geçersiz veri.']);
        exit;
    }

    try {
        $stmt = $db->prepare("UPDATE user_apps SET status = :status WHERE user_id = :user_id AND app_id = :app_id");
        $stmt->execute([
            ':status' => $new_status,
            ':user_id' => $user_id,
            ':app_id' => $app_id
        ]);
        
        // Update global approval_date in users table based on the new status
        if ($new_status === 'approved') {
            $upd = $db->prepare("UPDATE users SET approval_date = NOW() WHERE id = :id");
            $upd->execute([':id' => $user_id]);

            // Auto-trigger OneSignal VIP Approval Notification
            send_vip_approval_notification($db, $app_id, $user_id);
        } elseif ($new_status === 'pending') {
            $chk = $db->prepare("SELECT id FROM user_apps WHERE user_id = :id AND status = 'approved'");
            $chk->execute([':id' => $user_id]);
            if (!$chk->fetch()) {
                $upd = $db->prepare("UPDATE users SET approval_date = NULL WHERE id = :id");
                $upd->execute([':id' => $user_id]);
            }
        }
        
        $nameStmt = $db->prepare("SELECT name FROM users WHERE id = ?");
        $nameStmt->execute([$user_id]);
        $user_name = $nameStmt->fetchColumn() ?: 'Bilinmeyen Kullanıcı';
        
        log_action($db, 'update_user_status', "User ID: $user_id ($user_name), App ID: $app_id status updated to $new_status");
        echo json_encode(['status' => 'success', 'message' => 'Üyelik durumu güncellendi.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Güncelleme hatası: ' . $e->getMessage()]);
    }
}
elseif ($action === 'ban') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $user_id = $input['user_id'] ?? null;
    $ban_reason = $input['ban_reason'] ?? 'Yönetici tarafından yasaklandı.';

    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'Kullanıcı ID gereklidir.']);
        exit;
    }

    try {
        $db->beginTransaction();

        // Kullanıcıyı banla
        $stmt = $db->prepare("UPDATE users SET is_banned = 1, ban_reason = :ban_reason WHERE id = :user_id");
        $stmt->execute([
            ':ban_reason' => $ban_reason,
            ':user_id' => $user_id
        ]);

        // Çapraz Ban (Tüm üyelikleri rejected yap)
        $stmt2 = $db->prepare("UPDATE user_apps SET status = 'rejected' WHERE user_id = :user_id");
        $stmt2->execute([':user_id' => $user_id]);

        $db->commit();
        $nameStmt = $db->prepare("SELECT name FROM users WHERE id = ?");
        $nameStmt->execute([$user_id]);
        $user_name = $nameStmt->fetchColumn() ?: 'Bilinmeyen Kullanıcı';
        
        log_action($db, 'ban_user', "User ID: $user_id ($user_name) banned. Reason: $ban_reason");
        echo json_encode(['status' => 'success', 'message' => 'Kullanıcı başarıyla banlandı ve tüm üyelikleri reddedildi.']);
    } catch (PDOException $e) {
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Ban işlemi sırasında hata: ' . $e->getMessage()]);
    }
}
elseif ($action === 'unban') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $user_id = $input['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'Kullanıcı ID gereklidir.']);
        exit;
    }

    try {
        $stmt = $db->prepare("UPDATE users SET is_banned = 0, ban_reason = NULL WHERE id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        
        $nameStmt = $db->prepare("SELECT name FROM users WHERE id = ?");
        $nameStmt->execute([$user_id]);
        $user_name = $nameStmt->fetchColumn() ?: 'Bilinmeyen Kullanıcı';
        
        log_action($db, 'unban_user', "User ID: $user_id ($user_name) unbanned.");
        echo json_encode(['status' => 'success', 'message' => 'Kullanıcının banı başarıyla kaldırıldı.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Ban kaldırma sırasında hata: ' . $e->getMessage()]);
    }
}
elseif ($action === 'bulk_update') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $user_ids = $input['user_ids'] ?? [];
    $bulk_action = $input['bulk_action'] ?? ''; // 'approve', 'reject', 'pending', 'ban', 'unban', 'delete'

    if (empty($user_ids) || !is_array($user_ids) || !in_array($bulk_action, ['approve', 'reject', 'pending', 'ban', 'unban', 'delete'])) {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz veya eksik veri.']);
        exit;
    }

    try {
        $db->beginTransaction();
        $placeholders = implode(',', array_fill(0, count($user_ids), '?'));

        if (in_array($bulk_action, ['approve', 'reject', 'pending'])) {
            $status_map = ['approve' => 'approved', 'reject' => 'rejected', 'pending' => 'pending'];
            $db_status = $status_map[$bulk_action];
            $stmt = $db->prepare("UPDATE user_apps SET status = ? WHERE user_id IN ($placeholders)");
            $params = array_merge([$db_status], $user_ids);
            $stmt->execute($params);

            if ($bulk_action === 'approve') {
                $upd = $db->prepare("UPDATE users SET approval_date = NOW() WHERE id IN ($placeholders)");
                $upd->execute($user_ids);

                // Find app_id for each user to send notification
                $userAppStmt = $db->prepare("SELECT user_id, app_id FROM user_apps WHERE user_id IN ($placeholders) AND status = 'approved'");
                $userAppStmt->execute($user_ids);
                $approvedPairs = $userAppStmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($approvedPairs as $pair) {
                    send_vip_approval_notification($db, $pair['app_id'], $pair['user_id']);
                }
            }
        } elseif ($bulk_action === 'ban') {
            $stmt = $db->prepare("UPDATE users SET is_banned = 1, ban_reason = 'Toplu işlem ile banlandı.' WHERE id IN ($placeholders)");
            $stmt->execute($user_ids);
            
            $stmt2 = $db->prepare("UPDATE user_apps SET status = 'rejected' WHERE user_id IN ($placeholders)");
            $stmt2->execute($user_ids);
        } elseif ($bulk_action === 'unban') {
            $stmt = $db->prepare("UPDATE users SET is_banned = 0, ban_reason = NULL WHERE id IN ($placeholders)");
            $stmt->execute($user_ids);
        } elseif ($bulk_action === 'delete') {
            $stmt = $db->prepare("UPDATE users SET is_deleted = 1 WHERE id IN ($placeholders)");
            $stmt->execute($user_ids);
        }

        $db->commit();
        log_action($db, 'bulk_update_users', "Bulk action: $bulk_action on ".count($user_ids)." users.");
        echo json_encode(['status' => 'success', 'message' => count($user_ids) . ' kullanıcı başarıyla güncellendi.']);
    } catch (PDOException $e) {
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Toplu işlem sırasında hata: ' . $e->getMessage()]);
    }
}
elseif ($action === 'get_user') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Kullanıcı ID eksik.']);
        exit;
    }

    try {
        $stmt = $db->prepare("SELECT id, name, email, gpa_code, is_banned, ban_reason, exempt_force_update, exempt_security, exempt_screenshot, gamification_badge, last_login_ip, last_login_date FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'Kullanıcı bulunamadı.']);
            exit;
        }

        $stmtApps = $db->prepare("
            SELECT a.id as app_id, a.name as app_name, ua.status, ua.created_at
            FROM user_apps ua
            INNER JOIN apps a ON ua.app_id = a.id
            WHERE ua.user_id = :user_id
        ");
        $stmtApps->execute([':user_id' => $id]);
        $apps = $stmtApps->fetchAll(PDO::FETCH_ASSOC);

        $similar_users = [];
        $email_parts = explode('@', $user['email']);
        $username = $email_parts[0];
        
        // Daha geniş bir eşleşme için e-posta adının ilk 3 harfini alalım
        $search_prefix = strlen($username) >= 3 ? substr($username, 0, 3) : $username;
        
        $similar_query = "
            SELECT 
                u.id, u.name, u.email, u.last_login_ip, u.is_banned,
                GROUP_CONCAT(a.name SEPARATOR ', ') as registered_apps
            FROM users u
            LEFT JOIN user_apps ua ON u.id = ua.user_id
            LEFT JOIN apps a ON ua.app_id = a.id
            WHERE u.id != :id AND (";
        
        $similar_params = [':id' => $id];
        
        $conditions = [];
        if (!empty($user['last_login_ip'])) {
            $conditions[] = "u.last_login_ip = :ip";
            $similar_params[':ip'] = $user['last_login_ip'];
        }
        
        if (!empty($search_prefix)) {
            $conditions[] = "u.email LIKE :email_like";
            $similar_params[':email_like'] = '%' . $search_prefix . '%';
        }
        
        if (!empty($conditions)) {
            $similar_query .= implode(' OR ', $conditions) . ") GROUP BY u.id";
            $stmtSimilar = $db->prepare($similar_query);
            $stmtSimilar->execute($similar_params);
            $similar_results = $stmtSimilar->fetchAll(PDO::FETCH_ASSOC);
            
            // Hangi kelimeden eşleştiğini frontend'e bildirmek için veriyi zenginleştiriyoruz
            foreach ($similar_results as $sim) {
                $match_reason = '';
                $match_word = '';
                
                if (!empty($user['last_login_ip']) && $sim['last_login_ip'] === $user['last_login_ip']) {
                    $match_reason = 'ip';
                    $match_word = $user['last_login_ip'];
                } else {
                    $match_reason = 'email';
                    $match_word = $search_prefix;
                }
                
                $sim['match_reason'] = $match_reason;
                $sim['match_word'] = $match_word;
                $similar_users[] = $sim;
            }
        }

        echo json_encode(['status' => 'success', 'data' => ['user' => $user, 'apps' => $apps, 'similar_users' => $similar_users, 'search_prefix' => $search_prefix]]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
elseif ($action === 'save_user') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = $input['id'] ?? null;
    $name = $input['name'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $gpa_code = $input['gpa_code'] ?? null;
    $exempt_force_update = $input['exempt_force_update'] ?? 0;
    $exempt_security = $input['exempt_security'] ?? 0;
    $exempt_screenshot = $input['exempt_screenshot'] ?? 0;
    
    if (isset($input['gamification_badge']) && is_array($input['gamification_badge'])) {
        $gamification_badge = implode(',', $input['gamification_badge']);
    } else {
        $gamification_badge = $input['gamification_badge'] ?? '';
    }
    
    $app_ids = $input['app_ids'] ?? [];

    if (empty($name) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Ad Soyad ve E-posta zorunludur.']);
        exit;
    }

    try {
        $db->beginTransaction();

        if (empty($id)) {
            // INSERT
            if (empty($password)) {
                echo json_encode(['status' => 'error', 'message' => 'Yeni kullanıcı oluştururken şifre zorunludur.']);
                exit;
            }

            $hashed = password_hash($password, PASSWORD_BCRYPT);
            
            $stmt = $db->prepare("
                INSERT INTO users (name, email, password, gpa_code, exempt_force_update, exempt_security, exempt_screenshot, gamification_badge)
                VALUES (:name, :email, :password, :gpa_code, :exempt_force_update, :exempt_security, :exempt_screenshot, :gamification_badge)
            ");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashed,
                ':gpa_code' => $gpa_code,
                ':exempt_force_update' => $exempt_force_update,
                ':exempt_security' => $exempt_security,
                ':exempt_screenshot' => $exempt_screenshot,
                ':gamification_badge' => $gamification_badge
            ]);
            
            $new_user_id = $db->lastInsertId();
            
            if (is_array($app_ids) && !empty($app_ids)) {
                $stmtIns = $db->prepare("INSERT IGNORE INTO user_apps (user_id, app_id, status) VALUES (?, ?, 'approved')");
                foreach ($app_ids as $a_id) {
                    $stmtIns->execute([$new_user_id, $a_id]);
                }
            }

            $db->commit();
            echo json_encode(['status' => 'success', 'message' => 'Kullanıcı başarıyla oluşturuldu.']);
        } else {
            // UPDATE
            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $db->prepare("
                    UPDATE users 
                    SET name = :name, email = :email, password = :password, gpa_code = :gpa_code, 
                        exempt_force_update = :exempt_force_update, exempt_security = :exempt_security, exempt_screenshot = :exempt_screenshot, gamification_badge = :gamification_badge
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':name' => $name,
                    ':email' => $email,
                    ':password' => $hashed,
                    ':gpa_code' => $gpa_code,
                    ':exempt_force_update' => $exempt_force_update,
                    ':exempt_security' => $exempt_security,
                    ':exempt_screenshot' => $exempt_screenshot,
                    ':gamification_badge' => $gamification_badge,
                    ':id' => $id
                ]);
            } else {
                $stmt = $db->prepare("
                    UPDATE users 
                    SET name = :name, email = :email, gpa_code = :gpa_code, 
                        exempt_force_update = :exempt_force_update, exempt_security = :exempt_security, exempt_screenshot = :exempt_screenshot, gamification_badge = :gamification_badge
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':name' => $name,
                    ':email' => $email,
                    ':gpa_code' => $gpa_code,
                    ':exempt_force_update' => $exempt_force_update,
                    ':exempt_security' => $exempt_security,
                    ':exempt_screenshot' => $exempt_screenshot,
                    ':gamification_badge' => $gamification_badge,
                    ':id' => $id
                ]);
            }
            
            // Sync user_apps
            if (is_array($app_ids)) {
                if (!empty($app_ids)) {
                    $placeholders = implode(',', array_fill(0, count($app_ids), '?'));
                    $stmtDel = $db->prepare("DELETE FROM user_apps WHERE user_id = ? AND app_id NOT IN ($placeholders)");
                    $params = array_merge([$id], $app_ids);
                    $stmtDel->execute($params);

                    $stmtIns = $db->prepare("INSERT IGNORE INTO user_apps (user_id, app_id, status) VALUES (?, ?, 'approved')");
                    foreach ($app_ids as $a_id) {
                        $stmtIns->execute([$id, $a_id]);
                    }
                } else {
                    $stmtDel = $db->prepare("DELETE FROM user_apps WHERE user_id = ?");
                    $stmtDel->execute([$id]);
                }
            }

            $db->commit();
            echo json_encode(['status' => 'success', 'message' => 'Kullanıcı bilgileri güncellendi.']);
        }
    } catch (PDOException $e) {
        $db->rollBack();
        // Hata yakalama, özellikle email unique hatası
        if ($e->getCode() == 23000) {
            echo json_encode(['status' => 'error', 'message' => 'Bu e-posta adresi zaten kullanılıyor.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
        }
    }
}
elseif ($action === 'pending_count') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
        exit;
    }
    try {
        $stmt = $db->query("SELECT COUNT(*) FROM user_apps WHERE status = 'pending'");
        $count = $stmt->fetchColumn();
        echo json_encode(['status' => 'success', 'count' => $count]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası.']);
    }
}
else {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz action parametresi.']);
}
