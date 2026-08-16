<?php
/**
 * ACMS - Maç (Tahmin) Yönetimi API'si
 * Dokümantasyon referansı: 6.5 Maç (Tahmin) Yönetimi Detayları
 *
 * GET  ?action=get_leagues                 -> Lig listesi (boşsa örnek verilerle doldurulur)
 * GET  ?action=get_apps                    -> Dağıtım için aktif uygulama listesi
 * GET  ?action=list[&status=&app_id=&date_from=&date_to=]  -> Maç havuzu (filtreli)
 * GET  ?action=get_match&id=X              -> Tek maç + atanmış uygulama ID'leri
 * GET  ?action=pending_count               -> Bekleyen maç sayısı (badge için)
 *
 * POST action=add            -> Yeni maç + çoklu uygulama dağıtımı
 * POST action=update         -> Maç güncelle + dağıtımı yeniden yaz
 * POST action=delete         -> Soft delete (is_deleted = 1)
 * POST action=set_result     -> Tek maçı sonuçlandır (status + score)
 * POST action=bulk_resolve   -> Birden fazla maçı tek istekte sonuçlandır
 *
 * Tüm yanıtlar {status, message?, data?} zarfı ile döner.
 *
 * GÜVENLİK NOTU: Bu uç nokta henüz sunucu tarafında kimlik doğrulaması YAPMAMAKTADIR.
 * Panel token'ı sadece istemci tarafında (localStorage + JS route guard) tutulmaktadır.
 * Ortak bir auth katmanı yazılana kadar bu dosya açık kabul edilmelidir.
 */

require_once '../config/Database.php';
require_once 'audit_helper.php';
header('Content-Type: application/json; charset=utf-8');

/** Geçerli maç durumları (matches.status enum ile birebir aynı olmalı) */
const ACMS_MATCH_STATUSES = ['pending', 'win', 'lose', 'postponed'];

/**
 * Karşılaşma adını " - " veya " vs " ayracına göre ev sahibi / deplasman olarak böler.
 * Ayraç bulunamazsa home_team başlığın tamamı, away_team null döner.
 */
function acms_parse_match_title($title)
{
    $parts = preg_split('/\s+(?:-|vs\.?|VS\.?)\s+/u', $title, 2);
    return [
        'home_team' => trim($parts[0] ?? $title),
        'away_team' => isset($parts[1]) ? trim($parts[1]) : null,
    ];
}

/** Sonuç durumu için skor zorunluluğunu ve formatını denetler. */
function acms_validate_score($status, $score)
{
    $score = trim((string) $score);

    // Kazandı / Kaybetti durumlarında skor zorunludur.
    if (in_array($status, ['win', 'lose'], true)) {
        if ($score === '') {
            return [null, 'Kazandı / Kaybetti durumu için skor girilmesi zorunludur (Örn: 2-1).'];
        }
        if (!preg_match('/^\d{1,2}\s*[-:]\s*\d{1,2}$/', $score)) {
            return [null, 'Skor formatı geçersiz. Örnek: 2-1'];
        }
        // Kanonik forma çevir: "2 - 1" -> "2-1"
        $score = preg_replace('/\s*[-:]\s*/', '-', $score);
        return [$score, null];
    }

    // Bekliyor / Ertelendi durumunda skor tutulmaz.
    return [$score === '' ? null : $score, null];
}

function getOrAddTeamId(PDO $db, $input) {
    if (empty(trim($input))) return null;
    if (is_numeric($input) && intval($input) > 0) {
        return intval($input);
    }
    $name = trim($input);
    $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($name));
    $stmt = $db->prepare("INSERT INTO teams (name, slug) VALUES (?, ?)");
    $stmt->execute([$name, $slug]);
    return $db->lastInsertId();
}

function getOrAddLeagueId(PDO $db, $input) {
    if (empty(trim($input))) return null;
    if (is_numeric($input) && intval($input) > 0) {
        return intval($input);
    }
    $name = trim($input);
    $slug = trim(preg_replace('/[^a-z0-9]+/i', '-', strtolower($name)), '-');
    $stmt = $db->prepare("INSERT INTO leagues (name, slug) VALUES (?, ?)");
    $stmt->execute([$name, $slug]);
    return $db->lastInsertId();
}

/** Verilen maç ID'sinin dağıtım listesini (app_matches) baştan yazar. */
function acms_sync_match_apps(PDO $db, $match_id, $app_ids)
{
    $stmt = $db->prepare("DELETE FROM app_matches WHERE match_id = ?");
    $stmt->execute([$match_id]);

    if (empty($app_ids) || !is_array($app_ids)) {
        return 0;
    }

    // Aynı ID'nin iki kez gelmesi PRIMARY KEY hatası vermesin.
    $app_ids = array_unique(array_filter(array_map('intval', $app_ids)));
    if (empty($app_ids)) {
        return 0;
    }

    $stmt = $db->prepare("INSERT INTO app_matches (app_id, match_id) VALUES (?, ?)");
    foreach ($app_ids as $app_id) {
        $stmt->execute([$app_id, $match_id]);
    }

    return count($app_ids);
}

try {
    $db = Database::getInstance()->getConnection();
    $method = $_SERVER['REQUEST_METHOD'];

    // ==========================================================
    // GET İSTEKLERİ
    // ==========================================================
    if ($method === 'GET') {
        $action = $_GET['action'] ?? 'list';

        // ---- Lig listesi (otomatik tohumlama ile) ----
        if ($action === 'get_leagues') {
            $stmt = $db->query("SELECT COUNT(*) FROM leagues WHERE is_deleted = 0");
            if ($stmt->fetchColumn() == 0) {
                $db->exec("INSERT INTO leagues (name, slug) VALUES
                    ('Şampiyonlar Ligi', 'sampiyonlar-ligi'),
                    ('Premier Lig', 'premier-lig'),
                    ('La Liga', 'la-liga')
                    ON DUPLICATE KEY UPDATE is_deleted = 0");
            }

            $stmt = $db->query("SELECT id, name, slug, logo_url FROM leagues WHERE is_deleted = 0 ORDER BY name ASC");
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            exit;
        }

        // ---- Dağıtım için uygulama listesi ----
        if ($action === 'get_apps') {
            $stmt = $db->query("
                SELECT a.id, a.name, a.app_type, b.name AS brand_name
                FROM apps a
                LEFT JOIN brands b ON a.brand_id = b.id
                WHERE a.is_deleted = 0
                ORDER BY b.name ASC, a.name ASC
            ");
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            exit;
        }

        // ---- Bekleyen maç sayısı (sidebar / dashboard badge) ----
        if ($action === 'pending_count') {
            $stmt = $db->query("SELECT COUNT(*) FROM matches WHERE is_deleted = 0 AND status = 'pending'");
            echo json_encode(['status' => 'success', 'data' => ['count' => (int) $stmt->fetchColumn()]]);
            exit;
        }

        // ---- Tek maç (düzenleme ekranı için) ----
        if ($action === 'get_match') {
            $match_id = intval($_GET['id'] ?? 0);
            if ($match_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Geçersiz maç ID.']);
                exit;
            }

            $stmt = $db->prepare("
                SELECT m.*, l.name AS league_name, ht.name as home_team_name, at.name as away_team_name
                FROM matches m
                LEFT JOIN leagues l ON m.league_id = l.id
                LEFT JOIN teams ht ON m.home_team_id = ht.id
                LEFT JOIN teams at ON m.away_team_id = at.id
                WHERE m.id = ? AND m.is_deleted = 0
            ");
            $stmt->execute([$match_id]);
            $match = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$match) {
                echo json_encode(['status' => 'error', 'message' => 'Maç bulunamadı.']);
                exit;
            }

            // Atanmış uygulamalar (checkbox'ları işaretlemek için)
            $stmt = $db->prepare("SELECT app_id FROM app_matches WHERE match_id = ?");
            $stmt->execute([$match_id]);
            $match['app_ids'] = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

            // datetime-local input'u için "YYYY-MM-DDTHH:MM" formatı
            $match['match_date_input'] = $match['match_date']
                ? date('Y-m-d\TH:i', strtotime($match['match_date']))
                : '';

            $match = array_merge($match, acms_parse_match_title($match['match_title']));

            echo json_encode(['status' => 'success', 'data' => $match]);
            exit;
        }

        // ---- Maç havuzu listesi (filtreli) ----
        if ($action === 'list') {
            $where = ['m.is_deleted = 0'];
            $params = [];

            // Durum filtresi
            if (!empty($_GET['status']) && in_array($_GET['status'], ACMS_MATCH_STATUSES, true)) {
                $where[] = 'm.status = ?';
                $params[] = $_GET['status'];
            }

            // Uygulama filtresi (belirli bir uygulamada yayınlananlar)
            if (!empty($_GET['app_id'])) {
                $where[] = 'EXISTS (SELECT 1 FROM app_matches am2 WHERE am2.match_id = m.id AND am2.app_id = ?)';
                $params[] = intval($_GET['app_id']);
            }

            // Lig filtresi
            if (!empty($_GET['league_id'])) {
                $where[] = 'm.league_id = ?';
                $params[] = intval($_GET['league_id']);
            }

            // Tarih aralığı
            if (!empty($_GET['date_from'])) {
                $where[] = 'DATE(m.match_date) >= ?';
                $params[] = $_GET['date_from'];
            }
            if (!empty($_GET['date_to'])) {
                $where[] = 'DATE(m.match_date) <= ?';
                $params[] = $_GET['date_to'];
            }

            $query = "
                SELECT m.*, l.name AS league_name, l.logo_url as league_logo,
                       (SELECT GROUP_CONCAT(a.name ORDER BY a.name SEPARATOR ', ')
                          FROM app_matches am
                          JOIN apps a ON am.app_id = a.id AND a.is_deleted = 0
                         WHERE am.match_id = m.id) AS published_apps,
                       (SELECT COUNT(*)
                          FROM app_matches am3
                          JOIN apps a3 ON am3.app_id = a3.id AND a3.is_deleted = 0
                         WHERE am3.match_id = m.id) AS published_count,
                       ht.logo_url AS ht_logo, ht.name AS ht_name,
                       at.logo_url AS at_logo, at.name AS at_name
                FROM matches m
                LEFT JOIN leagues l ON m.league_id = l.id
                LEFT JOIN teams ht ON m.home_team_id = ht.id
                LEFT JOIN teams at ON m.away_team_id = at.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY m.match_date DESC, m.id DESC
            ";

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Webview'in "vs / skor" gösterimi için takım adlarını da ayrıştırıp döndür.
            foreach ($matches as &$m) {
                $m = array_merge($m, acms_parse_match_title($m['match_title']));
            }
            unset($m);

            echo json_encode(['status' => 'success', 'data' => $matches]);
            exit;
        }

        echo json_encode(['status' => 'error', 'message' => 'Bilinmeyen GET aksiyonu: ' . $action]);
        exit;
    }

    // ==========================================================
    // POST İSTEKLERİ
    // ==========================================================
    if ($method === 'POST') {
        // Gövde JSON veya form-data olarak gelebilir.
        $data = json_decode(file_get_contents("php://input"), true);
        if (!is_array($data) && !empty($_POST)) {
            $data = $_POST;
        }
        if (!is_array($data)) {
            $data = [];
        }

        $action = $data['action'] ?? '';

        // ---- Soft Delete ----
        if ($action === 'delete') {
            $match_id = intval($data['id'] ?? 0);
            if ($match_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Geçersiz maç ID.']);
                exit;
            }

            $stmt = $db->prepare("UPDATE matches SET is_deleted = 1 WHERE id = ?");
            $stmt->execute([$match_id]);

            if ($stmt->rowCount() === 0) {
                echo json_encode(['status' => 'error', 'message' => 'Maç bulunamadı veya zaten silinmiş.']);
                exit;
            }

            $nameStmt = $db->prepare("SELECT match_title FROM matches WHERE id = ?");
            $nameStmt->execute([$match_id]);
            $match_name = $nameStmt->fetchColumn() ?: 'Bilinmeyen Maç';
            
            log_action($db, 'delete_match', "Match ID: $match_id ($match_name) deleted.");
            echo json_encode(['status' => 'success', 'message' => 'Maç silindi (Soft Delete).']);
            exit;
        }

        if ($action === 'bulk_delete') {
            $ids = $data['ids'] ?? [];
            if (is_array($ids) && count($ids) > 0) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $db->prepare("UPDATE matches SET is_deleted = 1 WHERE id IN ($placeholders)");
                $stmt->execute($ids);
                log_action($db, 'bulk_delete_matches', count($ids) . " matches deleted.");
                echo json_encode(['status' => 'success', 'message' => count($ids) . ' adet maç silindi.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Geçersiz ID listesi']);
            }
            exit;
        }

        // ---- Tek maçı sonuçlandır (listedeki ✔ / ✖ butonları) ----
        if ($action === 'set_result') {
            $match_id = intval($data['id'] ?? 0);
            $status = trim($data['status'] ?? '');

            if ($match_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Geçersiz maç ID.']);
                exit;
            }
            if (!in_array($status, ACMS_MATCH_STATUSES, true)) {
                echo json_encode(['status' => 'error', 'message' => 'Geçersiz maç durumu.']);
                exit;
            }

            list($score, $score_error) = acms_validate_score($status, $data['score'] ?? '');
            if ($score_error) {
                echo json_encode(['status' => 'error', 'message' => $score_error]);
                exit;
            }

            $stmt = $db->prepare("UPDATE matches SET status = ?, score = ? WHERE id = ? AND is_deleted = 0");
            $stmt->execute([$status, $score, $match_id]);

            $nameStmt = $db->prepare("SELECT match_title FROM matches WHERE id = ?");
            $nameStmt->execute([$match_id]);
            $match_name = $nameStmt->fetchColumn() ?: 'Bilinmeyen Maç';
            
            log_action($db, 'set_match_result', "Match ID: $match_id ($match_name) result set to $status (Score: $score)");
            
            // Maç merkezden sonuçlandığı an, bağlı tüm uygulamalar aynı satırı okuduğu için
            // dağıtım tarafında ek bir güncelleme gerekmez (tek kaynak: matches tablosu).
            echo json_encode([
                'status' => 'success',
                'message' => 'Maç sonucu güncellendi ve tüm bağlı uygulamalara yansıtıldı.',
            ]);
            exit;
        }

        // ---- Toplu sonuçlandırma ----
        if ($action === 'bulk_resolve') {
            $items = $data['items'] ?? [];
            if (empty($items) || !is_array($items)) {
                echo json_encode(['status' => 'error', 'message' => 'Sonuçlandırılacak maç seçilmedi.']);
                exit;
            }

            $db->beginTransaction();
            $stmt = $db->prepare("UPDATE matches SET status = ?, score = ? WHERE id = ? AND is_deleted = 0");
            $updated = 0;
            $skipped = [];

            foreach ($items as $item) {
                $match_id = intval($item['id'] ?? 0);
                $status = trim($item['status'] ?? '');

                if ($match_id <= 0 || !in_array($status, ACMS_MATCH_STATUSES, true)) {
                    $skipped[] = $match_id;
                    continue;
                }

                list($score, $score_error) = acms_validate_score($status, $item['score'] ?? '');
                if ($score_error) {
                    $skipped[] = $match_id;
                    continue;
                }

                $stmt->execute([$status, $score, $match_id]);
                $updated++;
            }

            $db->commit();

            $message = $updated . ' maç sonuçlandırıldı.';
            if (!empty($skipped)) {
                $message .= ' ' . count($skipped) . ' maç eksik/hatalı veri nedeniyle atlandı.';
            }

            log_action($db, 'bulk_resolve_matches', "Bulk resolve: $updated matches updated, " . count($skipped) . " skipped.");
            
            echo json_encode([
                'status' => 'success',
                'message' => $message,
                'data' => ['updated' => $updated, 'skipped' => $skipped],
            ]);
            exit;
        }

        // ---- Ekleme / Güncelleme (ortak doğrulama) ----
        if ($action === 'add' || $action === 'update') {
            $league_input = $data['league_id'] ?? '';
            $home_team_input = $data['home_team_id'] ?? '';
            $away_team_input = $data['away_team_id'] ?? '';
            $match_date = trim($data['match_date'] ?? '');
            $prediction = trim($data['prediction'] ?? '');
            $odds = trim($data['odds'] ?? '');
            $status = trim($data['status'] ?? 'pending');
            $confidence_rate = trim($data['confidence_rate'] ?? '');
            $app_ids = $data['app_ids'] ?? [];

            $league_id = getOrAddLeagueId($db, $league_input);

            if (empty($league_id) || $match_date === '' || $prediction === '') {
                echo json_encode(['status' => 'error', 'message' => 'Lütfen zorunlu alanları doldurun.']);
                exit;
            }

            if (!in_array($status, ACMS_MATCH_STATUSES, true)) {
                echo json_encode(['status' => 'error', 'message' => 'Geçersiz maç durumu.']);
                exit;
            }

            // "2026-08-09T21:00" (datetime-local) -> "2026-08-09 21:00:00"
            $timestamp = strtotime(str_replace('T', ' ', $match_date));
            if ($timestamp === false) {
                echo json_encode(['status' => 'error', 'message' => 'Tarih formatı geçersiz.']);
                exit;
            }
            $match_date = date('Y-m-d H:i:s', $timestamp);

            // Oran boş bırakılabilir, girildiyse sayısal olmalı (matches.odds varchar(20)).
            if ($odds !== '') {
                $odds = str_replace(',', '.', $odds);
                if (!is_numeric($odds) || (float) $odds <= 0) {
                    echo json_encode(['status' => 'error', 'message' => 'Oran geçerli bir sayı olmalıdır (Örn: 1.85).']);
                    exit;
                }
                $odds = number_format((float) $odds, 2, '.', '');
            } else {
                $odds = '0.00';
            }

            list($score, $score_error) = acms_validate_score($status, $data['score'] ?? '');
            if ($score_error) {
                echo json_encode(['status' => 'error', 'message' => $score_error]);
                exit;
            }

            // Lig kontrolüne gerek kalmadı, çünkü ID bulunamazsa create ediyoruz.

            $home_team_id = getOrAddTeamId($db, $home_team_input);
            $away_team_id = getOrAddTeamId($db, $away_team_input);

            $ht_name = '';
            $at_name = '';
            if ($home_team_id) {
                $st = $db->prepare("SELECT name FROM teams WHERE id = ?"); $st->execute([$home_team_id]); $ht_name = $st->fetchColumn();
            }
            if ($away_team_id) {
                $st = $db->prepare("SELECT name FROM teams WHERE id = ?"); $st->execute([$away_team_id]); $at_name = $st->fetchColumn();
            }
            $match_title = trim($ht_name . ' - ' . $at_name);

            if ($action === 'add') {
                $db->beginTransaction();

                $stmt = $db->prepare("
                    INSERT INTO matches (league_id, match_title, match_date, prediction, odds, score, status, home_team_id, away_team_id, confidence_rate)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$league_id, $match_title, $match_date, $prediction, $odds, $score, $status, $home_team_id, $away_team_id, $confidence_rate]);
                $match_id = (int) $db->lastInsertId();

                $assigned = acms_sync_match_apps($db, $match_id, $app_ids);

                $db->commit();
                
                log_action($db, 'add_match', "Match ID: $match_id ($match_title) added.");

                echo json_encode([
                    'status' => 'success',
                    'message' => $assigned > 0
                        ? 'Maç eklendi ve ' . $assigned . ' uygulamaya dağıtıldı.'
                        : 'Maç eklendi. (Henüz hiçbir uygulamaya dağıtılmadı)',
                    'data' => ['id' => $match_id, 'assigned_apps' => $assigned],
                ]);
                exit;
            }

            // ---- update ----
            $match_id = intval($data['id'] ?? 0);
            if ($match_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Geçersiz maç ID.']);
                exit;
            }

            $stmt = $db->prepare("SELECT COUNT(*) FROM matches WHERE id = ? AND is_deleted = 0");
            $stmt->execute([$match_id]);
            if ($stmt->fetchColumn() == 0) {
                echo json_encode(['status' => 'error', 'message' => 'Güncellenecek maç bulunamadı.']);
                exit;
            }

            $db->beginTransaction();

            $stmt = $db->prepare("
                UPDATE matches SET
                    league_id = ?, match_title = ?, match_date = ?,
                    prediction = ?, odds = ?, score = ?, status = ?,
                    home_team_id = ?, away_team_id = ?, confidence_rate = ?
                WHERE id = ?
            ");
            $stmt->execute([$league_id, $match_title, $match_date, $prediction, $odds, $score, $status, $home_team_id, $away_team_id, $confidence_rate, $match_id]);

            $assigned = acms_sync_match_apps($db, $match_id, $app_ids);

            $db->commit();
            
            log_action($db, 'update_match', "Match ID: $match_id ($match_title) updated.");

            echo json_encode([
                'status' => 'success',
                'message' => 'Maç güncellendi. (' . $assigned . ' uygulamada yayında)',
                'data' => ['id' => $match_id, 'assigned_apps' => $assigned],
            ]);
            exit;
        }

        echo json_encode(['status' => 'error', 'message' => 'Bilinmeyen POST aksiyonu.']);
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Desteklenmeyen istek metodu: ' . $method]);

} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => 'Sistem hatası: ' . $e->getMessage()]);
}
