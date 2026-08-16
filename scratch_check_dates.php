<?php
require_once 'api/config/Database.php';
$db = Database::getInstance()->getConnection();
$matches = $db->query("SELECT id, match_date, status, home_team_id, away_team_id FROM matches ORDER BY match_date DESC LIMIT 15")->fetchAll(PDO::FETCH_ASSOC);
print_r($matches);
