<?php
require_once __DIR__ . '/../config/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    $createTableSql = "
    CREATE TABLE IF NOT EXISTS notification_templates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        app_id INT NOT NULL,
        event_key VARCHAR(50) NOT NULL,
        name VARCHAR(100) NOT NULL,
        is_active TINYINT(1) DEFAULT 1,
        title_tr VARCHAR(255) NOT NULL,
        message_tr TEXT NOT NULL,
        title_en VARCHAR(255) NULL,
        message_en TEXT NULL,
        title_de VARCHAR(255) NULL,
        message_de TEXT NULL,
        title_es VARCHAR(255) NULL,
        message_es TEXT NULL,
        title_pt VARCHAR(255) NULL,
        message_pt TEXT NULL,
        title_fr VARCHAR(255) NULL,
        message_fr TEXT NULL,
        deep_link VARCHAR(255) DEFAULT '/app/tips',
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unq_app_event (app_id, event_key),
        INDEX idx_app_id (app_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $db->exec($createTableSql);

    // Seed default VIP template for all existing apps if missing
    $appsStmt = $db->query("SELECT id, name FROM apps WHERE is_deleted = 0");
    $apps = $appsStmt->fetchAll(PDO::FETCH_ASSOC);

    $insStmt = $db->prepare("
        INSERT INTO notification_templates 
        (app_id, event_key, name, is_active, title_tr, message_tr, title_en, message_en, title_de, message_de, title_es, message_es, title_pt, message_pt, title_fr, message_fr, deep_link)
        VALUES 
        (:app_id, 'vip_approval', 'VIP Üyelik Onay Bildirimi', 1,
         '🎉 VIP Üyeliğiniz Onaylandı!', 'Tebrikler {name}! Sipariş kodunuz onaylandı. Artık tüm VIP tahmin ve analizlere sınırsız erişebilirsiniz.',
         '🎉 VIP Membership Approved!', 'Congratulations {name}! Your order code has been approved. You now have full unlimited access to all VIP tips.',
         '🎉 VIP-Mitgliedschaft Genehmigt!', 'Herzlichen Glückwunsch {name}! Ihr Bestellcode wurde bestätigt. Sie haben jetzt unbegrenzten Zugriff auf alle VIP-Tipps.',
         '🎉 ¡Membresía VIP Aprobada!', '¡Felicitaciones {name}! Tu código de pedido ha sido aprobado. Ahora tienes acceso ilimitado a todas las predicciones VIP.',
         '🎉 Associação VIP Aprovada!', 'Parabéns {name}! Seu código de pedido foi aprovado. Agora você tem acesso ilimitado a todas as dicas VIP.',
         '🎉 Adhésion VIP Approuvée!', 'Félicitations {name}! Votre code de commande a été validé. Vous avez maintenant un accès illimité à tous les pronostics VIP.',
         '/app/tips')
        ON DUPLICATE KEY UPDATE name = VALUES(name)
    ");

    foreach ($apps as $app) {
        $insStmt->execute([':app_id' => $app['id']]);
    }

    echo "[Migration] notification_templates table is ready with multi-language defaults.<br>\n";
} catch (Exception $e) {
    echo "[Migration Error] " . $e->getMessage();
}
