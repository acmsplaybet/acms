<?php
require_once '../config/config.php';
require_once '../config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Sütun var mı kontrol et, yoksa ekle
    $stmt = $db->query("SHOW COLUMNS FROM apps LIKE 'bot_coupon_name'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE apps ADD COLUMN bot_coupon_name VARCHAR(150) DEFAULT NULL AFTER name");
        echo "Başarılı: bot_coupon_name kolonu eklendi.";
    } else {
        echo "Bilgi: bot_coupon_name kolonu zaten mevcut.";
    }
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>
