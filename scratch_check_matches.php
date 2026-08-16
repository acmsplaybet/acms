<?php
require_once 'api/config/Database.php';
$db = Database::getInstance()->getConnection();
$apps = $db->query('SELECT id, name FROM apps')->fetchAll(PDO::FETCH_ASSOC);
print_r($apps);
$m = $db->query('SELECT am.app_id, count(*) as c FROM app_matches am GROUP BY am.app_id')->fetchAll(PDO::FETCH_ASSOC);
print_r($m);
$recentWins = $db->query("SELECT m.id, m.match_date, m.status, am.app_id FROM matches m JOIN app_matches am ON m.id = am.match_id WHERE m.status = 'win' ORDER BY m.match_date DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
print_r($recentWins);
