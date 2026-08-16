<?php
// api/config/onesignal_helper.php
// Centralized OneSignal push notification dispatcher for ACMS with Multi-Language Support

if (!function_exists('send_onesignal_notification')) {
    /**
     * Send OneSignal Push Notification (Supports Single & Multi-Language)
     * 
     * @param PDO $db
     * @param int $app_id
     * @param string|array $title String or ['en' => '...', 'tr' => '...', 'de' => '...', 'es' => '...', 'pt' => '...', 'fr' => '...']
     * @param string|array $message String or ['en' => '...', 'tr' => '...', 'de' => '...', 'es' => '...', 'pt' => '...', 'fr' => '...']
     * @param string $target_segment 'all' | 'approved' | 'pending' | 'user'
     * @param int|null $target_user_id
     * @param string|null $image_url
     * @param string $deep_link
     * @param bool $is_test
     * @return array
     */
    function send_onesignal_notification($db, $app_id, $title, $message, $target_segment = 'all', $target_user_id = null, $image_url = null, $deep_link = '/app/tips', $is_test = false) {
        $app_id = intval($app_id);
        if (!$app_id || empty($title) || empty($message)) {
            return [
                'status' => 'error',
                'message' => 'Uygulama ID, başlık ve mesaj alanları zorunludur.'
            ];
        }

        // 1. Fetch App OneSignal credentials
        $stmt = $db->prepare("SELECT id, name, onesignal_app_id, onesignal_api_key FROM apps WHERE id = ?");
        $stmt->execute([$app_id]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$app) {
            return [
                'status' => 'error',
                'message' => 'Uygulama bulunamadı.'
            ];
        }

        $onesignal_app_id = trim($app['onesignal_app_id'] ?? '');
        $onesignal_api_key = trim($app['onesignal_api_key'] ?? '');

        if (empty($onesignal_app_id) || empty($onesignal_api_key)) {
            return [
                'status' => 'error',
                'message' => "{$app['name']} uygulaması için OneSignal App ID veya REST API Key yapılandırılmamış. Lütfen Uygulama Düzenle sayfasından OneSignal anahtarlarını girin."
            ];
        }

        // 2. Format Headings & Contents (Multi-Language Aware)
        $headingsMap = [];
        $contentsMap = [];

        if (is_array($title)) {
            foreach ($title as $lang => $text) {
                if (!empty($text)) $headingsMap[$lang] = $text;
            }
            if (empty($headingsMap['en']) && !empty($headingsMap['tr'])) {
                $headingsMap['en'] = $headingsMap['tr'];
            }
        } else {
            $headingsMap = ['en' => $title, 'tr' => $title];
        }

        if (is_array($message)) {
            foreach ($message as $lang => $text) {
                if (!empty($text)) $contentsMap[$lang] = $text;
            }
            if (empty($contentsMap['en']) && !empty($contentsMap['tr'])) {
                $contentsMap['en'] = $contentsMap['tr'];
            }
        } else {
            $contentsMap = ['en' => $message, 'tr' => $message];
        }

        $primaryTitle = is_array($title) ? ($title['tr'] ?? $title['en'] ?? reset($title)) : $title;
        $primaryMessage = is_array($message) ? ($message['tr'] ?? $message['en'] ?? reset($message)) : $message;

        // 3. Build OneSignal Payload
        $payload = [
            'app_id' => $onesignal_app_id,
            'headings' => $headingsMap,
            'contents' => $contentsMap,
            'data' => [
                'route' => $deep_link ?: '/app/tips',
                'app_id' => $app_id,
                'custom_title' => $primaryTitle
            ]
        ];

        if (!empty($image_url)) {
            $payload['big_picture'] = $image_url;
            $payload['large_icon'] = $image_url;
            $payload['ios_attachments'] = ['id' => $image_url];
        }

        // Target Segmentation / Filtering
        if ($target_segment === 'approved') {
            $payload['filters'] = [
                ['field' => 'tag', 'key' => 'status', 'relation' => '=', 'value' => 'approved']
            ];
        } elseif ($target_segment === 'pending') {
            $payload['filters'] = [
                ['field' => 'tag', 'key' => 'status', 'relation' => '=', 'value' => 'pending']
            ];
        } elseif ($target_segment === 'user' && !empty($target_user_id)) {
            // Target specific user ID via tag and alias
            $payload['filters'] = [
                ['field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => (string)$target_user_id]
            ];
            $payload['include_aliases'] = [
                'external_id' => [(string)$target_user_id]
            ];
            $payload['target_channel'] = 'push';
        } else {
            // All active subscribed devices
            $payload['included_segments'] = ['Total Subscriptions', 'Active Subscriptions', 'All'];
        }

        // Test / Dry Run Mode
        if ($is_test) {
            try {
                $logStmt = $db->prepare("
                    INSERT INTO notification_logs 
                    (app_id, title, message, target_segment, target_user_id, image_url, deep_link, onesignal_id, recipients, status, response_json, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'DRY_RUN', 0, 'dry_run', ?, NOW())
                ");
                $logStmt->execute([
                    $app_id,
                    $primaryTitle,
                    $primaryMessage,
                    $target_segment,
                    $target_user_id,
                    $image_url,
                    $deep_link,
                    json_encode($payload, JSON_UNESCAPED_UNICODE)
                ]);
            } catch (Exception $e) {}

            return [
                'status' => 'success',
                'dry_run' => true,
                'message' => 'Simülasyon Modu: Bildirim OneSignal sunucularına gönderilmedi (Başarıyla test edildi).',
                'payload' => $payload
            ];
        }

        // 4. Dispatch POST Request to OneSignal REST API v1
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . $onesignal_api_key
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $curlErr = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $resData = json_decode($response, true);
        $onesignal_id = $resData['id'] ?? null;
        $recipients = intval($resData['recipients'] ?? 0);
        $isSuccess = ($httpCode >= 200 && $httpCode < 300) && empty($resData['errors']);

        // 5. Log to notification_logs table
        try {
            $logStmt = $db->prepare("
                INSERT INTO notification_logs 
                (app_id, title, message, target_segment, target_user_id, image_url, deep_link, onesignal_id, recipients, status, response_json, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $logStmt->execute([
                $app_id,
                $primaryTitle,
                $primaryMessage,
                $target_segment,
                $target_user_id,
                $image_url,
                $deep_link,
                $onesignal_id,
                $recipients,
                $isSuccess ? 'sent' : 'failed',
                $response ?: $curlErr
            ]);
        } catch (Exception $e) {
            error_log("Failed to insert notification log: " . $e->getMessage());
        }

        if ($isSuccess) {
            return [
                'status' => 'success',
                'message' => 'Bildirim başarıyla gönderildi.',
                'onesignal_id' => $onesignal_id,
                'recipients' => $recipients
            ];
        } else {
            $errMsg = 'OneSignal API Hatası';
            if (!empty($resData['errors'])) {
                $errMsg = is_array($resData['errors']) ? implode(', ', $resData['errors']) : $resData['errors'];
            } elseif ($curlErr) {
                $errMsg = 'Bağlantı Hatası: ' . $curlErr;
            }
            return [
                'status' => 'error',
                'message' => $errMsg,
                'http_code' => $httpCode,
                'response' => $resData
            ];
        }
    }
}

if (!function_exists('send_vip_approval_notification')) {
    /**
     * Send automatic push notification to a user when their VIP membership is approved
     * Uses dynamic multi-language template from notification_templates table.
     * 
     * @param PDO $db
     * @param int $app_id
     * @param int $user_id
     * @return array
     */
    function send_vip_approval_notification($db, $app_id, $user_id) {
        try {
            // 1. Fetch User details
            $userStmt = $db->prepare("SELECT id, name, email FROM users WHERE id = ?");
            $userStmt->execute([$user_id]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);
            $userName = $user ? $user['name'] : 'Değerli Üyemiz';

            // 2. Fetch App details
            $appStmt = $db->prepare("SELECT id, name FROM apps WHERE id = ?");
            $appStmt->execute([$app_id]);
            $app = $appStmt->fetch(PDO::FETCH_ASSOC);
            $appName = $app ? $app['name'] : 'ACMS App';

            // 3. Fetch Template from notification_templates
            $tplStmt = $db->prepare("SELECT * FROM notification_templates WHERE app_id = ? AND event_key = 'vip_approval'");
            $tplStmt->execute([$app_id]);
            $tpl = $tplStmt->fetch(PDO::FETCH_ASSOC);

            // If template exists and is deactivated, skip
            if ($tpl && isset($tpl['is_active']) && (int)$tpl['is_active'] === 0) {
                return ['status' => 'skipped', 'message' => 'VIP approval notification is disabled in settings.'];
            }

            // Variable replacement helper
            $replaceVars = function($text) use ($userName, $appName) {
                if (empty($text)) return '';
                return str_replace(['{name}', '{app_name}'], [$userName, $appName], $text);
            };

            // 4. Build Multi-Language Titles and Messages
            $titles = [
                'tr' => $replaceVars($tpl['title_tr'] ?? '🎉 VIP Üyeliğiniz Onaylandı!'),
                'en' => $replaceVars($tpl['title_en'] ?? '🎉 VIP Membership Approved!'),
                'de' => $replaceVars($tpl['title_de'] ?? '🎉 VIP-Mitgliedschaft Genehmigt!'),
                'es' => $replaceVars($tpl['title_es'] ?? '🎉 ¡Membresía VIP Aprobada!'),
                'pt' => $replaceVars($tpl['title_pt'] ?? '🎉 Associação VIP Aprovada!'),
                'fr' => $replaceVars($tpl['title_fr'] ?? '🎉 Adhésion VIP Approuvée!')
            ];

            $messages = [
                'tr' => $replaceVars($tpl['message_tr'] ?? 'Tebrikler {name}! Sipariş kodunuz onaylandı. Artık tüm VIP tahmin ve analizlere sınırsız erişebilirsiniz.'),
                'en' => $replaceVars($tpl['message_en'] ?? 'Congratulations {name}! Your order code has been approved. You now have full unlimited access to all VIP tips.'),
                'de' => $replaceVars($tpl['message_de'] ?? 'Herzlichen Glückwunsch {name}! Ihr Bestellcode wurde bestätigt. Sie haben jetzt unbegrenzten Zugriff auf alle VIP-Tipps.'),
                'es' => $replaceVars($tpl['message_es'] ?? '¡Felicitaciones {name}! Tu código de pedido ha sido aprobado. Ahora tienes acceso ilimitado a todas las predicciones VIP.'),
                'pt' => $replaceVars($tpl['message_pt'] ?? 'Parabéns {name}! Seu código de pedido foi aprovado. Agora você tem acesso ilimitado a todas as dicas VIP.'),
                'fr' => $replaceVars($tpl['message_fr'] ?? 'Félicitations {name}! Votre code de commande a été validé. Vous avez maintenant un accès illimité à tous les pronostics VIP.')
            ];

            $deepLink = $tpl['deep_link'] ?? '/app/tips';

            return send_onesignal_notification(
                $db,
                $app_id,
                $titles,
                $messages,
                'user',
                $user_id,
                null,
                $deepLink,
                false
            );
        } catch (Exception $e) {
            error_log("send_vip_approval_notification error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
