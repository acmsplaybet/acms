<?php
require_once '../config/config.php';
require_once '../config/Database.php';
require_once 'audit_helper.php';

header('Content-Type: application/json; charset=utf-8');

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

$json_input = json_decode(file_get_contents('php://input'), true);
if (empty($action) && isset($json_input['action'])) {
    $action = $json_input['action'];
}

// --- Yardımcı Fonksiyonlar ---
function getLeagueId($db, $league_name, $league_logo = '') {
    $stmt = $db->prepare("SELECT id, logo_url FROM leagues WHERE name = ? LIMIT 1");
    $stmt->execute([$league_name]);
    $league = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($league) {
        if ($league_logo && empty($league['logo_url'])) {
            $stmt = $db->prepare("UPDATE leagues SET is_deleted = 0, logo_url = ? WHERE id = ?");
            $stmt->execute([$league_logo, $league['id']]);
        } else {
            $stmt = $db->prepare("UPDATE leagues SET is_deleted = 0 WHERE id = ?");
            $stmt->execute([$league['id']]);
        }
        return $league['id'];
    }
    $slug = trim(preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim($league_name))), '-');
    $stmt = $db->prepare("INSERT INTO leagues (name, slug, logo_url) VALUES (?, ?, ?)");
    $stmt->execute([$league_name, $slug, $league_logo]);
    return $db->lastInsertId();
}

function getTeamId($db, $team_name, $flag_url = '', $league_id = null) {
    $stmt = $db->prepare("SELECT id, logo_url, league_id FROM teams WHERE name = ? LIMIT 1");
    $stmt->execute([$team_name]);
    $team = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($team) {
        // Logo sadece boşsa güncelle, league_id ise her zaman güncelle
        $new_logo = ($flag_url && empty($team['logo_url'])) ? $flag_url : $team['logo_url'];
        $new_league = ($league_id !== null) ? $league_id : $team['league_id'];
        $stmt = $db->prepare("UPDATE teams SET is_deleted = 0, logo_url = ?, league_id = ? WHERE id = ?");
        $stmt->execute([$new_logo, $new_league, $team['id']]);
        return $team['id'];
    }
    $slug = trim(preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim($team_name))), '-');
    $stmt = $db->prepare("INSERT INTO teams (name, slug, logo_url, league_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$team_name, $slug, $flag_url, $league_id]);
    return $db->lastInsertId();
}

function mapStatusToAcms($result) {
    $result = strtoupper(trim($result));
    if ($result === 'WIN' || $result === 'WON') return 'win';
    if ($result === 'LOSE' || $result === 'LOST') return 'lose';
    if ($result === 'REFUND' || $result === 'VOID') return 'refund';
    return 'pending';
}

if ($action === 'get_apps') {
    if ($method !== 'GET') { echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']); exit; }
    try {
        $stmt = $db->query("SELECT id, name, bot_coupon_name FROM apps WHERE is_deleted = 0 ORDER BY name ASC");
        $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $apps]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'DB Hatası: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'save_mapping') {
    if ($method !== 'POST') { echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']); exit; }
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['mappings']) || !is_array($input['mappings'])) {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz veri.']); exit;
    }
    try {
        $db->beginTransaction();
        $stmt = $db->prepare("UPDATE apps SET bot_coupon_name = ? WHERE id = ?");
        foreach ($input['mappings'] as $mapping) {
            $app_id = intval($mapping['app_id']);
            $coupon_name = $mapping['coupon_name'] === '' ? null : $mapping['coupon_name'];
            $stmt->execute([$coupon_name, $app_id]);
        }
        $db->commit();
        log_action($db, 'update_bot_mapping', "Bot coupon mappings updated.");
        echo json_encode(['status' => 'success', 'message' => 'Eşleştirmeler başarıyla kaydedildi.']);
    } catch (PDOException $e) {
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'DB Hatası: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'sync_bot') {
    if ($method !== 'POST') { echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']); exit; }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $input_date = $input['date'] ?? date('Y-m-d');
    $formatted_date = date('d-m-Y', strtotime($input_date));
    
    $url = "https://realmobilebet.com/bpiv2/bpav2/api/bpa_history.php";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $result = curl_exec($ch);
    curl_close($ch);
    
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Dış API ulaşılamadı.']); exit;
    }
    
    $data = json_decode($result, true);
    if (!isset($data[$formatted_date])) {
        echo json_encode(['status' => 'error', 'message' => "$formatted_date tarihi için dış API'de veri bulunamadı."]); exit;
    }
    
    $dateData = $data[$formatted_date];
    
    $stmt = $db->query("SELECT id, name, bot_coupon_name FROM apps WHERE is_deleted = 0 AND bot_coupon_name IS NOT NULL AND bot_coupon_name != ''");
    $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($apps)) {
        echo json_encode(['status' => 'error', 'message' => 'Kupon ismi eşleştirilmiş hiçbir uygulama bulunamadı.']); exit;
    }
    
    $log_messages = [];
    $total_added = 0;
    $total_updated = 0;
    $total_assigned = 0;
    
    try {
        $db->beginTransaction();
        
        $aggregated = [];
        // Map app id by name for fast lookup
        $appMap = [];
        foreach ($apps as $app) {
            $appMap[$app['name']] = $app['id'];
            $coupon_name = $app['bot_coupon_name'];
            
            if (isset($dateData[$coupon_name])) {
                foreach ($dateData[$coupon_name]['matches'] as $m) {
                    $formatLogo = function($url) {
                        if (empty($url)) return '';
                        if (strpos($url, 'http') === 0) return $url;
                        return 'https://realmobilebet.com/bpiv2/bpav2/' . $url;
                    };

                    $key = $m['home'] . ' - ' . $m['away'] . '|' . $m['tip'] . '|' . $m['time'];
                    if (!isset($aggregated[$key])) {
                        $aggregated[$key] = [
                            'time' => $m['time'],
                            'league' => $m['leagueName'],
                            'league_logo' => $formatLogo($m['flagUrl'] ?? ''),
                            'home_logo' => $formatLogo($m['homeLogo'] ?? ''),
                            'away_logo' => $formatLogo($m['awayLogo'] ?? ''),
                            'match' => $m['home'] . ' - ' . $m['away'],
                            'tip' => $m['tip'],
                            'score' => $m['score'] ?? '-',
                            'result' => $m['result'],
                            'odds' => $m['odd'],
                            'app_ids' => []
                        ];
                    }
                    if (!in_array($app['id'], $aggregated[$key]['app_ids'])) {
                        $aggregated[$key]['app_ids'][] = $app['id'];
                    }
                }
            } else {
                $log_messages[] = "Uyarı: '{$app['name']}' için '{$coupon_name}' kuponu $formatted_date verisinde bulunamadı.";
            }
        }
        
        foreach ($aggregated as $key => $m) {
            $league_id = getLeagueId($db, $m['league'], $m['league_logo']);
            $match_title = $m['match'];
            $match_date = date('Y-m-d', strtotime($input_date)) . ' ' . $m['time'] . ':00';
            $prediction = $m['tip'];
            $odds = $m['odds'];
            $status = mapStatusToAcms($m['result']);
            $score = $m['score'] === '-' ? '' : $m['score'];
            $home_logo = $m['home_logo'];
            $away_logo = $m['away_logo'];
            
            $matchParts = explode(' - ', $m['match']);
            $homeName = isset($matchParts[0]) ? trim($matchParts[0]) : '';
            $awayName = isset($matchParts[1]) ? trim($matchParts[1]) : '';
            
            $home_team_id = null;
            $away_team_id = null;
            if (!empty($homeName)) $home_team_id = getTeamId($db, $homeName, $home_logo, $league_id);
            if (!empty($awayName)) $away_team_id = getTeamId($db, $awayName, $away_logo, $league_id);
            
            // Check uniqueness
            $date_only = date('Y-m-d', strtotime($match_date));
            $stmt_check = $db->prepare("SELECT id FROM matches WHERE ((home_team_id <=> ? AND away_team_id <=> ?) OR match_title = ?) AND DATE(match_date) = ? AND prediction = ? AND is_deleted = 0 LIMIT 1");
            $stmt_check->execute([$home_team_id, $away_team_id, $match_title, $date_only, $prediction]);
            $existing_match_id = $stmt_check->fetchColumn();
            
            $match_id_to_link = null;
            
            if ($existing_match_id) {
                // Update
                $stmt_up = $db->prepare("UPDATE matches SET status = ?, score = ?, odds = ?, league_id = ?, home_team_id = ?, away_team_id = ? WHERE id = ?");
                $stmt_up->execute([$status, $score, $odds, $league_id, $home_team_id, $away_team_id, $existing_match_id]);
                $match_id_to_link = $existing_match_id;
                $total_updated++;
            } else {
                // Insert
                $stmt_ins = $db->prepare("INSERT INTO matches (league_id, match_title, match_date, prediction, odds, score, status, home_team_id, away_team_id, is_bot_added) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt_ins->execute([$league_id, $match_title, $match_date, $prediction, $odds, $score, $status, $home_team_id, $away_team_id]);
                $match_id_to_link = $db->lastInsertId();
                $total_added++;
            }
            
            // Assign apps
            if ($match_id_to_link) {
                foreach ($m['app_ids'] as $app_id) {
                    $stmt_lnk = $db->prepare("SELECT COUNT(*) FROM app_matches WHERE match_id = ? AND app_id = ?");
                    $stmt_lnk->execute([$match_id_to_link, $app_id]);
                    if ($stmt_lnk->fetchColumn() == 0) {
                        $stmt_ins_lnk = $db->prepare("INSERT INTO app_matches (match_id, app_id) VALUES (?, ?)");
                        $stmt_ins_lnk->execute([$match_id_to_link, $app_id]);
                        $total_assigned++;
                    }
                }
            }
        }
        
        $db->commit();
        log_action($db, 'bot_sync', "Bot Synced for $input_date. Added: $total_added, Updated: $total_updated.");
        
        $log_messages[] = "Senkronizasyon Tamamlandı. Benzersiz Eklenen Maç: $total_added, Güncellenen Maç: $total_updated, Uygulamalara Toplam Atama: $total_assigned.";
        
        echo json_encode(['status' => 'success', 'message' => 'Senkronizasyon işlemi bitti.', 'logs' => $log_messages]);
    } catch (PDOException $e) {
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'DB Hatası: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Geçersiz aksiyon.']);
?>
