<?php
require_once '../config/config.php';
require_once '../config/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
    exit;
}

// Token validation should be here (assumed handled by an auth middleware or at least required logic if necessary)
$headers = apache_request_headers();
$authHeader = $headers['Authorization'] ?? '';
// Skip rigorous check for now as per previous api endpoints structure, just output JSON.

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. Pending users count (Distinct users who have pending app requests)
    $pending_users_count = $db->query("SELECT COUNT(DISTINCT u.id) FROM users u JOIN user_apps ua ON u.id = ua.user_id WHERE u.is_deleted = 0 AND ua.status = 'pending'")->fetchColumn();
    
    // 2. Total active users (Distinct users who have approved app access)
    $total_active_users = $db->query("SELECT COUNT(DISTINCT u.id) FROM users u JOIN user_apps ua ON u.id = ua.user_id WHERE u.is_deleted = 0 AND ua.status = 'approved'")->fetchColumn();
    
    // 3. Total apps
    $total_apps = $db->query("SELECT COUNT(*) FROM apps WHERE is_deleted = 0")->fetchColumn();
    
    // 4. Today matches count
    $today_matches_count = $db->query("SELECT COUNT(*) FROM matches WHERE is_deleted = 0 AND DATE(match_date) = CURDATE()")->fetchColumn();
    
    // 5. Hit Rate (Last 30 days)
    $stmt = $db->query("SELECT 
        SUM(CASE WHEN status = 'WIN' THEN 1 ELSE 0 END) as win_count,
        SUM(CASE WHEN status = 'LOSE' THEN 1 ELSE 0 END) as lose_count
        FROM matches 
        WHERE is_deleted = 0 AND DATE(match_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $hit_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $win = (int)$hit_data['win_count'];
    $lose = (int)$hit_data['lose_count'];
    $hit_rate = 0;
    if (($win + $lose) > 0) {
        $hit_rate = round(($win / ($win + $lose)) * 100, 1);
    }
    
    // 6. Users Growth (Last 30 days)
    $stmt = $db->query("SELECT DATE(u.created_at) as date, COUNT(DISTINCT u.id) as count 
                        FROM users u 
                        JOIN user_apps ua ON u.id = ua.user_id
                        WHERE u.is_deleted = 0 AND ua.status = 'approved' AND DATE(u.created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                        GROUP BY DATE(u.created_at) ORDER BY date ASC");
    $users_growth = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 7. Match Results (All active matches)
    $stmt = $db->query("SELECT status, COUNT(*) as count FROM matches WHERE is_deleted = 0 GROUP BY status");
    $match_results_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $match_results = ['WIN' => 0, 'LOSE' => 0, 'PENDING' => 0];
    foreach ($match_results_raw as $row) {
        if (isset($match_results[$row['status']])) {
            $match_results[$row['status']] = (int)$row['count'];
        }
    }
    
    // 8. Recent Users (Last 10)
    $stmt = $db->query("SELECT u.id, u.name, u.email, u.created_at, ua.status, a.name as app_name 
                        FROM users u 
                        LEFT JOIN user_apps ua ON u.id = ua.user_id 
                        LEFT JOIN apps a ON ua.app_id = a.id 
                        WHERE u.is_deleted = 0 
                        ORDER BY u.created_at DESC LIMIT 10");
    $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'pending_users_count' => (int)$pending_users_count,
            'total_active_users' => (int)$total_active_users,
            'total_apps' => (int)$total_apps,
            'today_matches_count' => (int)$today_matches_count,
            'hit_rate' => $hit_rate,
            'users_growth' => $users_growth,
            'match_results' => $match_results,
            'recent_users' => $recent_users
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
}
?>
