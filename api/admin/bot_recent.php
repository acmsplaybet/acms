<?php
require_once '../config/Database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $db = Database::getInstance()->getConnection();
    
    // Get last 10 matches added by bot
    $stmt = $db->query("
        SELECT m.id, m.match_date, m.match_title, m.prediction, m.status, l.name AS league_name, ht.logo_url AS home_logo, at.logo_url AS away_logo, m.updated_at
        FROM matches m
        LEFT JOIN leagues l ON m.league_id = l.id
        LEFT JOIN teams ht ON m.home_team_id = ht.id
        LEFT JOIN teams at ON m.away_team_id = at.id
        WHERE m.is_deleted = 0 AND m.is_bot_added = 1
        ORDER BY m.updated_at DESC, m.id DESC
        LIMIT 10
    ");
    
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['status' => 'success', 'data' => $matches]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Hata: ' . $e->getMessage()]);
}
