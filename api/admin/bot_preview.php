<?php
require_once '../config/config.php';
require_once '../config/Database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$input_date = $input['date'] ?? date('Y-m-d'); // 2026-08-09
$formatted_date = date('d-m-Y', strtotime($input_date)); // JSON format is 09-08-2026

$url = "https://realmobilebet.com/bpiv2/bpav2/api/bpa_history.php";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$result = curl_exec($ch);
curl_close($ch);

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => 'Dış API ulaşılamadı.']);
    exit;
}

$data = json_decode($result, true);
if (!isset($data[$formatted_date])) {
    echo json_encode(['status' => 'success', 'data' => [], 'message' => "$formatted_date tarihi için dış API'de veri bulunamadı."]);
    exit;
}

$dateData = $data[$formatted_date];

$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT id, name, bot_coupon_name FROM apps WHERE is_deleted = 0 AND bot_coupon_name IS NOT NULL AND bot_coupon_name != ''");
$apps = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($apps)) {
    echo json_encode(['status' => 'error', 'message' => 'Eşleştirilmiş uygulama bulunamadı.']);
    exit;
}

$aggregated = [];
foreach ($apps as $app) {
    $coupon_name = $app['bot_coupon_name'];
    if (isset($dateData[$coupon_name])) {
        foreach ($dateData[$coupon_name]['matches'] as $m) {
            $formatLogo = function($url) {
                if (empty($url)) return '';
                if (strpos($url, 'http') === 0) return $url;
                return 'https://realmobilebet.com/bpiv2/bpav2/' . $url;
            };

            $key = $m['home'] . ' - ' . $m['away'] . '|' . $m['tip'] . '|' . $m['time']; // unique identifier
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
                    'apps' => []
                ];
            }
            if (!in_array($app['name'], $aggregated[$key]['apps'])) {
                $aggregated[$key]['apps'][] = $app['name'];
            }
        }
    }
}

$output = array_values($aggregated);

echo json_encode(['status' => 'success', 'data' => $output]);
?>
