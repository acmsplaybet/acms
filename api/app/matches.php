<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once '../config/config.php';
require_once '../config/Database.php';

function getAuthUser($db) {
    $headers = apache_request_headers();
    $token = '';
    if (isset($headers['Authorization'])) {
        $token = str_replace('Bearer ', '', $headers['Authorization']);
    } else {
        $token = $_GET['token'] ?? '';
    }
    
    if (empty($token)) return null;
    
    $stmt = $db->prepare("SELECT * FROM users WHERE session_token = :token AND is_deleted = 0");
    $stmt->execute([':token' => $token]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $app_id = isset($_GET['app_id']) ? intval($_GET['app_id']) : null;
    $date = isset($_GET['date']) ? trim($_GET['date']) : date('Y-m-d');
    $action = isset($_GET['action']) ? trim($_GET['action']) : 'daily';
    
    if (!$app_id) {
        echo json_encode(['status' => 'error', 'message' => 'App ID required']);
        exit;
    }
    
    $appStmt = $db->prepare("SELECT * FROM apps WHERE id = :id AND is_deleted = 0");
    $appStmt->execute([':id' => $app_id]);
    $app = $appStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$app) {
        echo json_encode(['status' => 'error', 'message' => 'App not found']);
        exit;
    }
    
    $user = getAuthUser($db);
    
    if ($user && (int)$user['is_banned'] === 1) {
        echo json_encode(['status' => 'error', 'code' => 'BANNED', 'message' => 'User is banned']);
        exit;
    }
    
    // User status logic: (Check user_apps table and approval_date for 'approved' status)
    $user_status = 'guest';
    if ($user) {
        $stmtStatus = $db->prepare("SELECT status FROM user_apps WHERE user_id = :uid AND app_id = :aid ORDER BY id DESC LIMIT 1");
        $stmtStatus->execute([':uid' => $user['id'], ':aid' => $app_id]);
        $userAppRow = $stmtStatus->fetch(PDO::FETCH_ASSOC);
        
        if ($userAppRow && $userAppRow['status'] === 'approved') {
            $user_status = 'approved';
        } elseif (!empty($user['approval_date'])) {
            $user_status = 'approved';
        } else {
            $user_status = 'pending';
        }
    }
    
    // Guest Limit Check
    if ($user_status === 'guest') {
        $limit_days = (int)($app['guest_tips_limit'] ?? 3);
        
        $req_date = new DateTime($date);
        $today = new DateTime(date('Y-m-d'));
        
        // Eğer istenen tarih bugünden küçükse (geçmiş maçlar) farkını gün olarak alalım
        // Örn: İstenen 10'u, Bugün 15'i -> difference = -5
        $interval = $today->diff($req_date);
        $diff_days = (int)$interval->format('%r%a'); 
        
        // Eğer diff_days < -$limit_days ise boş dön
        if ($diff_days < -$limit_days) {
            echo json_encode([
                'status' => 'success',
                'date' => $date,
                'data' => []
            ]);
            exit;
        }
    }
    
    if ($action === 'recent_winners') {
        $sql = "
            SELECT m.id, m.home_team_id, m.away_team_id, m.league_id, m.match_date, 
                   m.prediction, m.odds, m.confidence_rate, m.status, m.score,
                   t1.name as home_team, t1.logo_url as home_logo,
                   t2.name as away_team, t2.logo_url as away_logo,
                   l.name as league, l.logo_url as league_logo
            FROM matches m
            INNER JOIN app_matches am ON m.id = am.match_id
            LEFT JOIN teams t1 ON m.home_team_id = t1.id
            LEFT JOIN teams t2 ON m.away_team_id = t2.id
            LEFT JOIN leagues l ON m.league_id = l.id
            WHERE am.app_id = :app_id 
              AND m.status = 'win'
              AND m.is_deleted = 0
            ORDER BY m.match_date DESC
            LIMIT 5
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([':app_id' => $app_id]);
    } else {
        $sql = "
            SELECT m.id, m.home_team_id, m.away_team_id, m.league_id, m.match_date, 
                   m.prediction, m.odds, m.confidence_rate, m.status, m.score,
                   t1.name as home_team, t1.logo_url as home_logo,
                   t2.name as away_team, t2.logo_url as away_logo,
                   l.name as league, l.logo_url as league_logo
            FROM matches m
            INNER JOIN app_matches am ON m.id = am.match_id
            LEFT JOIN teams t1 ON m.home_team_id = t1.id
            LEFT JOIN teams t2 ON m.away_team_id = t2.id
            LEFT JOIN leagues l ON m.league_id = l.id
            WHERE am.app_id = :app_id 
              AND DATE(m.match_date) = :mdate 
              AND m.is_deleted = 0
            ORDER BY m.match_date ASC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':app_id' => $app_id,
            ':mdate' => $date
        ]);
    }
    
    $data = [];
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/acms/";
    
    $formatLogo = function($url) use ($baseUrl) {
        if (empty($url)) return '';
        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) return $url;
        return $baseUrl . 'admin/' . ltrim($url, '/');
    };
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        
        // Güvenlik: Prediction ve Odds alanlarını Blur'la
        $show_secret = ($user_status === 'approved');
        
        $prediction = $show_secret ? $row['prediction'] : null;
        $odds = $show_secret ? $row['odds'] : null;
        
        $data[] = [
            'id' => (int)$row['id'],
            'home_team' => $row['home_team'] ?? 'Unknown',
            'away_team' => $row['away_team'] ?? 'Unknown',
            'home_logo' => $formatLogo($row['home_logo']),
            'away_logo' => $formatLogo($row['away_logo']),
            'league' => $row['league'] ?? 'Unknown',
            'league_logo' => $formatLogo($row['league_logo']),
            'match_time' => $row['match_date'],
            'prediction' => $prediction,
            'odds' => $odds,
            'confidence_rate' => $row['confidence_rate'],
            'status' => $row['status'],
            'score' => $row['score']
        ];
    }
    
    echo json_encode([
        'status' => 'success',
        'date' => $date,
        'data' => $data
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
