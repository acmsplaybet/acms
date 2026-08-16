<?php
require_once '../config/config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz metod.']);
    exit;
}

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
if (!$data || !is_array($data)) {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz JSON formatı.']);
    exit;
}

$coupons = [];
foreach ($data as $date => $dateCoupons) {
    if (is_array($dateCoupons)) {
        foreach (array_keys($dateCoupons) as $couponName) {
            if ($couponName === 'coupons') continue; // edge case
            $coupons[] = $couponName;
        }
    }
}

$unique_coupons = array_values(array_unique($coupons));
sort($unique_coupons);

echo json_encode(['status' => 'success', 'data' => $unique_coupons]);
?>
