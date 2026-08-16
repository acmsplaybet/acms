-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: acms
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','editor') DEFAULT 'super_admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'Alper Yılmaz','admin@playbettingtips.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','super_admin','2026-08-08 19:32:41');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_matches`
--

DROP TABLE IF EXISTS `app_matches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_matches` (
  `app_id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  PRIMARY KEY (`app_id`,`match_id`),
  KEY `match_id` (`match_id`),
  CONSTRAINT `app_matches_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `apps` (`id`) ON DELETE CASCADE,
  CONSTRAINT `app_matches_ibfk_2` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_matches`
--

LOCK TABLES `app_matches` WRITE;
/*!40000 ALTER TABLE `app_matches` DISABLE KEYS */;
INSERT INTO `app_matches` VALUES (1,75),(1,76),(1,77),(1,78),(1,79),(1,80),(1,81),(1,82),(1,83),(1,84),(1,85),(1,86),(1,87),(1,88),(1,89),(1,90),(1,91),(1,92),(1,93),(1,94),(1,95),(1,96),(1,97),(1,98),(1,99),(1,100),(1,101),(1,102),(1,103),(1,104),(1,105),(1,106),(1,107),(1,108),(1,109),(1,110),(2,75),(2,76),(2,77),(2,78),(2,79),(2,80),(2,81),(2,82),(2,83),(2,84),(2,85),(2,86),(2,87),(2,88),(2,89),(2,90),(2,91),(2,92),(2,93),(2,94),(2,95),(2,96),(2,97),(2,98),(2,99),(2,100),(2,101),(2,102),(2,103),(2,104),(2,105),(2,106),(2,107),(2,108),(2,109),(2,110);
/*!40000 ALTER TABLE `app_matches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_promotions`
--

DROP TABLE IF EXISTS `app_promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_promotions` (
  `promo_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `discount_rate` varchar(20) NOT NULL COMMENT 'Orn: %70',
  PRIMARY KEY (`promo_id`,`app_id`),
  KEY `app_id` (`app_id`),
  CONSTRAINT `app_promotions_ibfk_1` FOREIGN KEY (`promo_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `app_promotions_ibfk_2` FOREIGN KEY (`app_id`) REFERENCES `apps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_promotions`
--

LOCK TABLES `app_promotions` WRITE;
/*!40000 ALTER TABLE `app_promotions` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_promotions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `apps`
--

DROP TABLE IF EXISTS `apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `bot_coupon_name` varchar(150) DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `app_type` enum('paid','free') DEFAULT 'paid',
  `price` decimal(10,2) DEFAULT 0.00,
  `frontend_url` varchar(255) NOT NULL COMMENT 'CORS ve WebView iÃ§in ozel domain (Orn: app.realmobilebet.com/vip)',
  `primary_color` varchar(20) DEFAULT '#000000',
  `secondary_color` varchar(20) DEFAULT '#333333',
  `accent_color` varchar(20) DEFAULT '#FF0000',
  `gradient_bg` varchar(255) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `favicon_url` varchar(255) DEFAULT NULL,
  `icon_type` varchar(50) DEFAULT 'default',
  `nav_names_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '{"home":"Home","predictions":"VIP Tips","hub":"VIP Hub","profile":"Profile"}' CHECK (json_valid(`nav_names_json`)),
  `user_agent` varchar(255) NOT NULL,
  `is_ios_allowed` tinyint(1) DEFAULT 0,
  `min_version` varchar(20) DEFAULT '1.0.0',
  `history_limit_days` int(11) DEFAULT 10,
  `onesignal_app_id` varchar(255) DEFAULT NULL,
  `onesignal_api_key` varchar(255) DEFAULT NULL,
  `custom_scripts` text DEFAULT NULL COMMENT 'Yandex Metrica vs.',
  `legal_texts_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '{"privacy":"", "terms":"", "about":""}' CHECK (json_valid(`legal_texts_json`)),
  `announcement_popup` text DEFAULT NULL,
  `is_maintenance` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bg_color` varchar(20) DEFAULT '#060d1a',
  `contact_telegram` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `maintenance_mode` tinyint(1) DEFAULT 0,
  `app_version` varchar(20) DEFAULT '1.0.0',
  `play_store_link` varchar(255) DEFAULT NULL,
  `vip_hub_description` varchar(255) DEFAULT NULL,
  `contact_whatsapp` varchar(255) DEFAULT NULL,
  `contact_instagram` varchar(255) DEFAULT NULL,
  `tawk_to_id` varchar(100) DEFAULT NULL,
  `forced_login` tinyint(1) DEFAULT 0,
  `min_required_version` varchar(20) DEFAULT NULL,
  `welcome_modal_active` tinyint(1) DEFAULT 0,
  `welcome_modal_text` text DEFAULT NULL,
  `guest_tips_limit` int(11) DEFAULT 3,
  `guide_step_1` varchar(255) DEFAULT NULL,
  `guide_step_2` varchar(255) DEFAULT NULL,
  `guide_step_3` varchar(255) DEFAULT NULL,
  `post_register_text` varchar(255) DEFAULT NULL,
  `empty_state_text` varchar(255) DEFAULT NULL,
  `rate_us_text` varchar(255) DEFAULT NULL,
  `rate_us_reward` varchar(255) DEFAULT NULL,
  `font_family` varchar(50) DEFAULT 'Inter',
  `contact_telegram_response` varchar(50) DEFAULT '~1–2 hours',
  `contact_whatsapp_response` varchar(50) DEFAULT '~1–2 hours',
  `contact_instagram_response` varchar(50) DEFAULT '~24 hours',
  `contact_email_response` varchar(50) DEFAULT '~24 hours',
  `rate_us_active` tinyint(1) DEFAULT 0,
  `rate_us_title` varchar(255) DEFAULT 'Enjoying the App? ⭐',
  `rate_us_snooze_days` int(11) DEFAULT 3,
  `rate_us_rate_btn_text` varchar(100) DEFAULT '⭐ Rate on Google Play',
  `rate_us_later_btn_text` varchar(100) DEFAULT 'Remind me later',
  `rate_us_step2_title` varchar(255) DEFAULT 'Thanks for your support! ?',
  `rate_us_step2_text` text DEFAULT NULL,
  `rate_us_step2_email_btn` varchar(100) DEFAULT '? Send via Email',
  `rate_us_step2_telegram_btn` varchar(100) DEFAULT '? Send via Telegram',
  `rate_us_step2_done_btn` varchar(100) DEFAULT 'Done ✓',
  `category` varchar(50) DEFAULT 'free',
  `theme` varchar(50) DEFAULT 'real',
  `onboarding_step1_title` varchar(255) DEFAULT NULL,
  `onboarding_step1_desc` text DEFAULT NULL,
  `onboarding_step2_title` varchar(255) DEFAULT NULL,
  `onboarding_step2_desc` text DEFAULT NULL,
  `onboarding_step3_title` varchar(255) DEFAULT NULL,
  `onboarding_step3_desc` text DEFAULT NULL,
  `home_announcement_text` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `brand_id` (`brand_id`),
  CONSTRAINT `apps_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apps`
--

LOCK TABLES `apps` WRITE;
/*!40000 ALTER TABLE `apps` DISABLE KEYS */;
INSERT INTO `apps` VALUES (1,1,'Real Bet FIXED MATCH Tips','GENEL VIP KUPONU - Global','com.real.fixed','paid',299.00,'app.realmobilebet.com/fixed','#454bba','#333333','#ffffff',NULL,'uploads/apps/logo_6a7ddd391d856.png','','default',NULL,'',0,'1.0.0',10,'','',NULL,'{\"privacy\":\"gizlilik\",\"terms\":\"kullanım koşul terms\",\"about\":\"hakkımızda about\"}',NULL,0,1,0,'2026-08-08 20:15:12','#060d1a','https:/t.me/realmobilebet','alperenozt1@gmail.com',0,'1.0.0','https://google.com','Super premium high stakes VIP picks.','https:/wa.me/905073622629','https:/t.me/realmobilebet','678cef89825083258e077b5f/1ihv8fca8',0,'',1,'alperen1',3,'alperen2','alperen3','alperen4','alperen5','alperen6','rate us başlık','ödül','Oswald','','','','',0,'',3,'','','','','','','','free','real_v2','','','','','','','Son duyuru\r\nson duyuru'),(2,1,'real deluxe','GENEL VIP KUPONU - Global','real deluxe','paid',199.00,'deluxe','#5156be','#333333','#ff0000',NULL,'uploads/apps/logo_6a7b0e888c318.jpg',NULL,'default',NULL,'',0,'1.0.0',10,'','','','{\"privacy\":\"\",\"terms\":\"\",\"about\":\"\"}','',0,1,0,'2026-08-09 16:43:38','#060d1a','','',0,'1.0.0','','Super premium high stakes VIP picks.','','','',0,'',0,'',3,'','','','','','','','Inter','~1–2 hours','~1–2 hours','~24 hours','~24 hours',0,'Enjoying the App? ⭐',3,'⭐ Rate on Google Play','Remind me later','Thanks for your support! 🙌',NULL,'📩 Send via Email','💬 Send via Telegram','Done ✓','free','real',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `apps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'ban_user','User ID: 1 banned. Reason: asda','::1','2026-08-09 17:59:18'),(2,1,'update_app','App ID: 2 updated (real deluxe)','::1','2026-08-09 18:02:10'),(3,1,'update_bot_mapping','Bot coupon mappings updated.','::1','2026-08-09 18:26:46'),(4,1,'bot_sync','Bot Synced for 2026-08-09. Added: 9, Updated: 0.','::1','2026-08-09 18:27:21'),(5,1,'bot_sync','Bot Synced for 2026-08-09. Added: 0, Updated: 9.','::1','2026-08-09 18:32:05'),(6,1,'bot_sync','Bot Synced for 2026-08-09. Added: 0, Updated: 9.','::1','2026-08-09 18:33:09'),(7,1,'bot_sync','Bot Synced for 2026-08-09. Added: 0, Updated: 9.','::1','2026-08-09 18:35:24'),(8,1,'bulk_delete_matches','10 matches deleted.','::1','2026-08-09 18:48:14'),(9,1,'bot_sync','Bot Synced for 2026-08-09. Added: 9, Updated: 0.','::1','2026-08-09 18:48:41'),(10,1,'bulk_delete_matches','9 matches deleted.','::1','2026-08-09 18:56:20'),(11,1,'bot_sync','Bot Synced for 2026-08-09. Added: 9, Updated: 0.','::1','2026-08-09 18:59:33'),(12,1,'bulk_delete_matches','9 matches deleted.','::1','2026-08-09 19:00:01'),(13,1,'bot_sync','Bot Synced for 2026-08-09. Added: 9, Updated: 0.','::1','2026-08-09 19:02:37'),(14,1,'bulk_delete_matches','9 matches deleted.','::1','2026-08-09 19:04:11'),(15,1,'bot_sync','Bot Synced for 2026-08-09. Added: 9, Updated: 0.','::1','2026-08-09 19:04:20'),(16,1,'bulk_delete_matches','9 matches deleted.','::1','2026-08-09 19:27:15'),(17,1,'bulk_hard_delete','10 League(s) permanently deleted.','::1','2026-08-09 19:28:40'),(18,1,'bulk_hard_delete','46 Match(s) permanently deleted.','::1','2026-08-09 19:28:47'),(19,1,'bot_sync','Bot Synced for 2026-08-09. Added: 9, Updated: 0.','::1','2026-08-09 19:28:56'),(20,1,'add_match','Match ID: 56 (alperen ev sahibi - alperen deplasman) added.','::1','2026-08-09 19:32:26'),(21,1,'bulk_delete_matches','10 matches deleted.','::1','2026-08-10 10:29:09'),(22,1,'bot_sync','Bot Synced for 2026-08-09. Added: 9, Updated: 0.','::1','2026-08-10 10:29:30'),(23,1,'bulk_hard_delete','10 Match(s) permanently deleted.','::1','2026-08-10 10:54:10'),(24,1,'hard_delete','League ID: 23 (Alperen ligi) permanently deleted.','::1','2026-08-10 10:54:14'),(25,1,'bulk_delete_matches','9 matches deleted.','::1','2026-08-10 12:44:12'),(26,1,'bulk_hard_delete','9 Match(s) permanently deleted.','::1','2026-08-10 12:44:32'),(27,1,'bulk_hard_delete','9 League(s) permanently deleted.','::1','2026-08-10 12:44:36'),(28,1,'bot_sync','Bot Synced for 2026-08-09. Added: 9, Updated: 0.','::1','2026-08-10 12:44:57'),(29,1,'bulk_delete_matches','9 matches deleted.','::1','2026-08-10 12:57:43'),(30,1,'bot_sync','Bot Synced for 2026-08-09. Added: 9, Updated: 0.','::1','2026-08-10 12:57:53'),(31,1,'bulk_hard_delete','9 Match(s) permanently deleted.','::1','2026-08-10 13:17:42'),(32,1,'update_app','App ID: 1 (Real Fixed) updated.','::1','2026-08-10 17:59:53'),(33,1,'update_app','App ID: 1 (Real Fixed) updated.','::1','2026-08-10 18:00:36'),(34,1,'update_match','Match ID: 83 (Gungahlin Juventus - Canberra White Eagles) updated.','::1','2026-08-10 18:06:18'),(35,1,'add_faq','FAQ eklendi ID: 1, App ID: 1','::1','2026-08-11 08:34:10'),(36,1,'add_promotion','Promosyon eklendi ID: 2, App ID: 2','::1','2026-08-11 09:07:02'),(37,1,'add_promotion','Promosyon eklendi ID: 3, App ID: 2','::1','2026-08-11 09:07:27'),(38,1,'add_promotion','Promosyon eklendi ID: 5, App ID: 2','::1','2026-08-11 09:10:24'),(39,1,'update_app','App ID: 1 (Real Fixed) updated.','::1','2026-08-11 09:21:31'),(40,1,'update_smtp','SMTP ayarları güncellendi.','::1','2026-08-11 09:23:50'),(41,1,'unban_user','User ID: 1 (alper) unbanned.','::1','2026-08-11 09:31:08'),(42,1,'update_user_status','User ID: 1 (alper), App ID: 1 status updated to approved','::1','2026-08-11 09:31:12'),(43,1,'bot_sync','Bot Synced for 2026-08-11. Added: 9, Updated: 0.','::1','2026-08-11 11:22:50'),(44,1,'bot_sync','Bot Synced for 2026-08-10. Added: 9, Updated: 0.','::1','2026-08-11 11:24:22'),(45,1,'update_match','Match ID: 84 (Hwacheon KSPO W - Incheon Red Angels W) updated.','::1','2026-08-11 11:28:24'),(46,1,'update_user_status','User ID: 9 (Test User), App ID: 2 status updated to approved','::1','2026-08-11 11:37:07'),(47,1,'update_user_status','User ID: 11 (alper1), App ID: 1 status updated to approved','::1','2026-08-11 11:37:49'),(48,1,'update_user_status','User ID: 11 (alper1), App ID: 1 status updated to pending','::1','2026-08-11 11:45:47'),(49,1,'update_user_status','User ID: 11 (alper1), App ID: 1 status updated to approved','::1','2026-08-11 11:48:19'),(50,1,'reply_ticket','Ticket yanıtlandı ve kapatıldı. ID: 2','::1','2026-08-11 11:53:44'),(51,1,'update_ticket_status','Ticket statüsü güncellendi. ID: 2, Yeni Statü: open','::1','2026-08-11 11:54:00'),(52,1,'update_ticket_status','Ticket statüsü güncellendi. ID: 2, Yeni Statü: cancelled','::1','2026-08-11 11:54:15'),(53,1,'update_app','App ID: 2 (real deluxe) updated.','::1','2026-08-11 11:58:31'),(54,1,'update_app','App ID: 2 (real deluxe) updated.','::1','2026-08-11 11:59:04'),(55,1,'update_app','App ID: 1 (Real Fixed) updated.','::1','2026-08-11 12:07:26'),(56,1,'update_app','App ID: 2 (real deluxe) updated.','::1','2026-08-11 12:10:24'),(57,1,'set_match_result','Match ID: 92 (Lyon - Sparta Praha) result set to win (Score: 3-0)','::1','2026-08-11 12:19:51'),(58,1,'update_user_status','User ID: 11 (alper1), App ID: 1 status updated to pending','::1','2026-08-11 12:42:10'),(59,1,'update_user_status','User ID: 11 (alper1), App ID: 1 status updated to approved','::1','2026-08-11 12:42:13'),(60,1,'update_user_status','User ID: 11 (alper1), App ID: 1 status updated to pending','::1','2026-08-11 12:42:16'),(61,1,'update_user_status','User ID: 11 (alper1), App ID: 1 status updated to approved','::1','2026-08-11 12:42:20'),(62,1,'update_promotion','Promosyon güncellendi ID: 4, App ID: 1','::1','2026-08-11 13:07:11'),(63,1,'update_user_status','User ID: 12 (John Doe), App ID: 1 status updated to approved','::1','2026-08-11 13:16:34'),(64,1,'ban_user','User ID: 12 (John Doe) banned. Reason: Test ban reason','::1','2026-08-11 13:24:21'),(65,1,'ban_user','User ID: 13 (John) banned. Reason: Testing','::1','2026-08-12 18:03:09'),(66,1,'update_user_status','User ID: 14 (John), App ID: 1 status updated to approved','::1','2026-08-12 18:06:34'),(67,1,'ban_user','User ID: 1 (alper) banned. Reason: test','::1','2026-08-12 18:07:47'),(68,1,'unban_user','User ID: 1 (alper) unbanned.','::1','2026-08-12 18:08:10'),(69,1,'ban_user','User ID: 1 (alper) banned. Reason: test','::1','2026-08-12 18:08:24'),(70,1,'unban_user','User ID: 1 (alper) unbanned.','::1','2026-08-12 18:11:43'),(71,1,'update_user_status','User ID: 1 (alper), App ID: 1 status updated to approved','::1','2026-08-12 18:12:27'),(72,1,'ban_user','User ID: 1 (alper) banned. Reason: test','::1','2026-08-12 18:18:22'),(73,1,'unban_user','User ID: 1 (alper) unbanned.','::1','2026-08-12 18:18:52'),(74,1,'update_user_status','User ID: 1 (alper), App ID: 1 status updated to approved','::1','2026-08-12 18:19:49'),(75,1,'ban_user','User ID: 1 (alper) banned. Reason: test','::1','2026-08-12 18:21:00'),(76,1,'unban_user','User ID: 1 (alper) unbanned.','::1','2026-08-12 18:21:18'),(77,1,'update_user_status','User ID: 1 (alper), App ID: 1 status updated to approved','::1','2026-08-12 18:21:24'),(78,1,'ban_user','User ID: 1 (alper) banned. Reason: res','::1','2026-08-12 18:21:34'),(79,1,'unban_user','User ID: 1 (alper) unbanned.','::1','2026-08-12 18:21:59'),(80,1,'update_user_status','User ID: 1 (alper), App ID: 1 status updated to approved','::1','2026-08-12 18:22:04'),(81,1,'ban_user','User ID: 1 (alper) banned. Reason: test','::1','2026-08-12 18:22:40'),(82,1,'update_user_status','User ID: 1 (alper), App ID: 1 status updated to approved','::1','2026-08-12 18:23:37'),(83,1,'unban_user','User ID: 1 (alper) unbanned.','::1','2026-08-12 18:23:42'),(84,1,'update_user_status','User ID: 1 (alper), App ID: 1 status updated to approved','::1','2026-08-12 18:24:14'),(85,1,'bulk_update_users','Bulk action: delete on 1 users.','::1','2026-08-12 18:27:34'),(86,1,'bulk_update_users','Bulk action: delete on 1 users.','::1','2026-08-12 18:27:37'),(87,1,'bulk_update_users','Bulk action: delete on 1 users.','::1','2026-08-12 18:27:39'),(88,1,'bulk_update_users','Bulk action: delete on 1 users.','::1','2026-08-12 18:27:41'),(89,1,'bulk_update_users','Bulk action: delete on 1 users.','::1','2026-08-12 18:27:44'),(90,1,'ban_user','User ID: 1 (alper) banned. Reason: ts','::1','2026-08-12 18:30:05'),(91,1,'update_user_status','User ID: 6 (Lockout User), App ID: 1 status updated to rejected','::1','2026-08-12 18:34:19'),(92,1,'unban_user','User ID: 1 (alper) unbanned.','::1','2026-08-12 18:40:02'),(93,1,'update_user_status','User ID: 1 (alper), App ID: 1 status updated to approved','::1','2026-08-12 18:40:06'),(94,1,'ban_user','User ID: 1 (alper) banned. Reason: test','::1','2026-08-12 18:40:20'),(95,1,'unban_user','User ID: 1 (alper) unbanned.','::1','2026-08-12 18:45:51'),(96,1,'update_user_status','User ID: 1 (alper), App ID: 1 status updated to approved','::1','2026-08-12 18:45:53'),(97,1,'ban_user','User ID: 1 (alper) banned. Reason: a','::1','2026-08-12 18:46:08'),(98,1,'unban_user','User ID: 1 (alper) unbanned.','::1','2026-08-12 18:46:41'),(99,1,'update_user_status','User ID: 1 (alper), App ID: 1 status updated to approved','::1','2026-08-12 18:46:42'),(100,1,'update_user_status','User ID: 1 (alper), App ID: 1 status updated to pending','::1','2026-08-12 18:47:04'),(101,1,'update_user_status','User ID: 1 (alper), App ID: 1 status updated to approved','::1','2026-08-12 18:47:15'),(102,1,'ban_user','User ID: 1 (alper) banned. Reason: Yönetici tarafından yasaklandı.','::1','2026-08-12 18:48:23'),(103,1,'unban_user','User ID: 1 (alper) unbanned.','::1','2026-08-12 18:51:06'),(104,1,'ban_user','User ID: 1 (alper) banned. Reason: testtt','::1','2026-08-12 18:51:10'),(105,1,'restore_from_trash','User ID: 2 (Real Fixed) restored.','::1','2026-08-12 19:16:51'),(106,1,'restore_from_trash','User ID: 2 (Real Fixed) restored.','::1','2026-08-12 19:21:16'),(107,1,'unban_user','User ID: 1 (alper) unbanned.','::1','2026-08-12 19:21:53'),(108,1,'update_user_status','User ID: 1 (alper), App ID: 1 status updated to approved','::1','2026-08-12 19:21:55'),(109,1,'update_app','App ID: 1 (Real Fixed) updated.','::1','2026-08-12 19:40:43'),(110,1,'update_app','App ID: 1 (Real Fixed) updated.','::1','2026-08-12 19:41:29'),(111,1,'update_app','App ID: 1 (Real Fixed) updated.','::1','2026-08-12 19:43:01'),(112,1,'update_app','App ID: 1 (Real Fixed) updated.','::1','2026-08-12 20:08:07'),(113,1,'update_user_status','User ID: 11 (alper1), App ID: 1 status updated to approved','::1','2026-08-12 20:22:35'),(114,1,'update_app','App ID: 1 (Real Fixed) updated.','::1','2026-08-12 20:24:32'),(115,1,'update_user_status','User ID: 9 (Test User), App ID: 1 status updated to approved','::1','2026-08-12 20:28:22'),(116,1,'update_app','App ID: 1 (Real Fixed) updated.','::1','2026-08-12 20:30:18'),(117,1,'update_app','App ID: 1 (Real Fixed) updated.','::1','2026-08-13 13:14:21'),(118,1,'update_app','App ID: 1 (Real Fixed) updated.','::1','2026-08-13 13:15:19'),(119,1,'bot_sync','Bot Synced for 2026-08-13. Added: 9, Updated: 0.','::1','2026-08-13 14:49:37'),(120,1,'update_match','Match ID: 109 (HK Kopavogur - Leiknir Reykjavik) updated.','::1','2026-08-13 15:02:21'),(121,1,'update_app','App ID: 1 (Real Fixed) updated.','::1','2026-08-13 15:05:07'),(122,1,'update_app','App ID: 1 (Real Fixed) updated.','::1','2026-08-13 15:05:29'),(123,1,'update_app','App ID: 1 (Real Bet FIXED MATCH Tips) updated.','::1','2026-08-13 15:05:54'),(124,1,'update_app','App ID: 1 (Real Bet FIXED MATCH Tips) updated.','::1','2026-08-13 16:44:53'),(125,1,'update_app','App ID: 1 (Real Bet FIXED MATCH Tips) updated.','::1','2026-08-13 19:30:55'),(126,1,'update_app','App ID: 1 (Real Bet FIXED MATCH Tips) updated.','::1','2026-08-13 19:50:23');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `default_theme` varchar(50) DEFAULT 'real',
  `description` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'Real','realtech',NULL,1,'2026-08-08 19:53:46','real',''),(3,'Alex','alex',NULL,1,'2026-08-13 10:56:48','alex','');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) NOT NULL,
  `type` enum('welcome','approved','rejected','banned') NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brand_type_unique` (`brand_id`,`type`),
  CONSTRAINT `email_templates_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_templates`
--

LOCK TABLES `email_templates` WRITE;
/*!40000 ALTER TABLE `email_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faqs`
--

DROP TABLE IF EXISTS `faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faqs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `status` enum('active','passive') DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faqs`
--

LOCK TABLES `faqs` WRITE;
/*!40000 ALTER TABLE `faqs` DISABLE KEYS */;
INSERT INTO `faqs` VALUES (1,1,'asda','asdasda','active',0,'2026-08-11 08:34:10');
/*!40000 ALTER TABLE `faqs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leagues`
--

DROP TABLE IF EXISTS `leagues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leagues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `slug` varchar(180) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leagues`
--

LOCK TABLES `leagues` WRITE;
/*!40000 ALTER TABLE `leagues` DISABLE KEYS */;
INSERT INTO `leagues` VALUES (24,'Sweden Allsvenskan','sweden-allsvenskan','https://realmobilebet.com/bpiv2/bpav2/images/leagues/se.png',0,'2026-08-10 12:44:57'),(25,'Denmark 1. Division','denmark-1-division','https://realmobilebet.com/bpiv2/bpav2/images/leagues/dk.png',0,'2026-08-10 12:44:57'),(26,'England FA Cup','england-fa-cup','https://realmobilebet.com/bpiv2/bpav2/images/leagues/gb-en.png',0,'2026-08-10 12:44:57'),(27,'Sweden Div 1 Norra','sweden-div-1-norra','https://realmobilebet.com/bpiv2/bpav2/images/leagues/se.png',0,'2026-08-10 12:44:57'),(28,'Czech Republic 3. CFL','czech-republic-3-cfl','https://realmobilebet.com/bpiv2/bpav2/images/leagues/cz.png',0,'2026-08-10 12:44:57'),(29,'Portugal Liga Portugal','portugal-liga-portugal','https://realmobilebet.com/bpiv2/bpav2/images/leagues/pt.png',0,'2026-08-10 12:44:57'),(30,'Serbia Superliga','serbia-superliga','https://realmobilebet.com/bpiv2/bpav2/images/leagues/rs.png',0,'2026-08-10 12:44:57'),(31,'Bolivia División Profesional','bolivia-divisi-n-profesional','https://realmobilebet.com/bpiv2/bpav2/images/leagues/bo.png',0,'2026-08-10 12:44:57'),(32,'Australia NPL ACT','australia-npl-act','https://realmobilebet.com/bpiv2/bpav2/images/leagues/au.png',0,'2026-08-10 12:44:57'),(33,'South Korea WK League Women','south-korea-wk-league-women','https://realmobilebet.com/bpiv2/bpav2/images/leagues/kr.png',0,'2026-08-11 11:22:50'),(34,'Kuwait 1st Division','kuwait-1st-division','https://realmobilebet.com/bpiv2/bpav2/images/leagues/kw.png',0,'2026-08-11 11:22:50'),(35,'Northern Ireland NIFL Championship','northern-ireland-nifl-championship','https://realmobilebet.com/bpiv2/bpav2/images/leagues/gb-ni.png',0,'2026-08-11 11:22:50'),(36,'Scotland Challenge Cup','scotland-challenge-cup','https://realmobilebet.com/bpiv2/bpav2/images/leagues/gb-sct.png',0,'2026-08-11 11:22:50'),(37,'England NPL Premier Division','england-npl-premier-division','https://realmobilebet.com/bpiv2/bpav2/images/leagues/gb-en.png',0,'2026-08-11 11:22:50'),(38,'England SPL Premier Division','england-spl-premier-division','https://realmobilebet.com/bpiv2/bpav2/images/leagues/gb-en.png',0,'2026-08-11 11:22:50'),(39,'Champions League','champions-league','https://realmobilebet.com/bpiv2/bpav2/images/leagues/13.png',0,'2026-08-11 11:22:50'),(40,'Bulgaria Vtora Liga','bulgaria-vtora-liga','https://realmobilebet.com/bpiv2/bpav2/images/leagues/bg.png',0,'2026-08-11 11:24:22'),(41,'Lithuania 1 Lyga','lithuania-1-lyga','https://realmobilebet.com/bpiv2/bpav2/images/leagues/lt.png',0,'2026-08-11 11:24:22'),(42,'Lithuania A Lyga','lithuania-a-lyga','https://realmobilebet.com/bpiv2/bpav2/images/leagues/lt.png',0,'2026-08-11 11:24:22'),(43,'Poland 1. Liga','poland-1-liga','https://realmobilebet.com/bpiv2/bpav2/images/leagues/pl.png',0,'2026-08-11 11:24:22'),(44,'Faroe Islands Premier League','faroe-islands-premier-league','https://realmobilebet.com/bpiv2/bpav2/images/leagues/fo.png',0,'2026-08-11 11:24:22'),(45,'England EFL Cup','england-efl-cup','https://realmobilebet.com/bpiv2/bpav2/images/leagues/gb-en.png',0,'2026-08-11 11:24:22'),(46,'Uzbekistan Professional Football League','uzbekistan-professional-football-league','https://realmobilebet.com/bpiv2/bpav2/images/leagues/uz.png',0,'2026-08-13 14:49:37'),(47,'Armenia Premier League','armenia-premier-league','https://realmobilebet.com/bpiv2/bpav2/images/leagues/am.png',0,'2026-08-13 14:49:37'),(48,'Denmark Danish Cup Women','denmark-danish-cup-women','https://realmobilebet.com/bpiv2/bpav2/images/leagues/dk.png',0,'2026-08-13 14:49:37'),(49,'UEFA Europa Conference League','uefa-europa-conference-league','https://realmobilebet.com/bpiv2/bpav2/images/leagues/571.png',0,'2026-08-13 14:49:37'),(50,'Sweden Division 2 - Södra Svealand','sweden-division-2-s-dra-svealand','https://realmobilebet.com/bpiv2/bpav2/images/leagues/se.png',0,'2026-08-13 14:49:37'),(51,'Europa League','europa-league','https://realmobilebet.com/bpiv2/bpav2/images/leagues/14.png',0,'2026-08-13 14:49:37'),(52,'Iceland 1. Division','iceland-1-division','https://realmobilebet.com/bpiv2/bpav2/images/leagues/is.png',0,'2026-08-13 14:49:37'),(53,'Argentina Reserve League','argentina-reserve-league','https://realmobilebet.com/bpiv2/bpav2/images/leagues/ar.png',0,'2026-08-13 14:49:37');
/*!40000 ALTER TABLE `leagues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matches`
--

DROP TABLE IF EXISTS `matches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `match_title` varchar(255) NOT NULL COMMENT 'Ev Sahibi - Deplasman (veya vs)',
  `match_date` datetime NOT NULL,
  `league_id` int(11) DEFAULT NULL,
  `prediction` varchar(150) NOT NULL,
  `odds` varchar(20) NOT NULL,
  `score` varchar(20) DEFAULT NULL,
  `status` enum('pending','win','lose','postponed') DEFAULT 'pending',
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `home_logo` varchar(255) DEFAULT NULL,
  `away_logo` varchar(255) DEFAULT NULL,
  `home_team_id` int(11) DEFAULT NULL,
  `away_team_id` int(11) DEFAULT NULL,
  `is_bot_added` tinyint(4) DEFAULT 0,
  `confidence_rate` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `league_id` (`league_id`),
  CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`league_id`) REFERENCES `leagues` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matches`
--

LOCK TABLES `matches` WRITE;
/*!40000 ALTER TABLE `matches` DISABLE KEYS */;
INSERT INTO `matches` VALUES (75,'Malmö - Degerfors IF','2026-08-09 15:00:00',24,'Over 2.5 Goals','1.65','','pending',0,'2026-08-10 12:57:53',NULL,NULL,1,2,1,NULL),(76,'Vejle BK - Hillerod','2026-08-09 15:00:00',25,'Over 2.5 Goals','1.66','','pending',0,'2026-08-10 12:57:53',NULL,NULL,3,4,1,NULL),(77,'Worsbrough Bridge - Trafford FC','2026-08-09 16:00:00',26,'Trafford FC Wins','2.62','','pending',0,'2026-08-10 12:57:53',NULL,NULL,5,6,1,NULL),(78,'FC Arlanda - Piteå IF','2026-08-09 18:00:00',27,'HT/FT: 1/1','2.05','','pending',0,'2026-08-10 12:57:53',NULL,NULL,7,8,1,NULL),(79,'Sokol Brozany - FK Caslav','2026-08-09 18:00:00',28,'Sokol Brozany Wins','1.90','','pending',0,'2026-08-10 12:57:53',NULL,NULL,9,10,1,NULL),(80,'Porto - FC Alverca','2026-08-09 20:00:00',29,'Correct Score: 2-0','7.00','','pending',0,'2026-08-10 12:57:53',NULL,NULL,11,12,1,NULL),(81,'Macva Sabac - IMT Novi Beograd','2026-08-09 21:00:00',30,'Over 2.5 Goals','2.15','','pending',0,'2026-08-10 12:57:53',NULL,NULL,13,14,1,NULL),(82,'GV San José - Always Ready','2026-08-09 22:00:00',31,'Correct Score: 0-2','7.00','','pending',0,'2026-08-10 12:57:53',NULL,NULL,15,16,1,NULL),(83,'Gungahlin Juventus - Canberra White Eagles','2026-08-09 08:00:00',32,'HT/FT: 1/1','1.83',NULL,'pending',0,'2026-08-10 12:57:53',NULL,NULL,17,18,1,'86'),(84,'Hwacheon KSPO W - Incheon Red Angels W','2026-08-11 13:00:00',33,'Over 2.5 Goals','1.89',NULL,'pending',0,'2026-08-11 11:22:50',NULL,NULL,21,22,1,'84'),(85,'Sulaibikhat - Sporty','2026-08-11 20:25:00',34,'Correct Score: 2-0','7.00','','pending',0,'2026-08-11 11:22:50',NULL,NULL,23,24,1,NULL),(86,'Newry City - Glenavon','2026-08-11 21:45:00',35,'Correct Score: 2-0','7.00','','pending',0,'2026-08-11 11:22:50',NULL,NULL,25,26,1,NULL),(87,'Forfar Athletic - Aberdeen Youth','2026-08-11 21:45:00',36,'HT/FT: 1/1','2.35','','pending',0,'2026-08-11 11:22:50',NULL,NULL,27,28,1,NULL),(88,'Quorn - Cleethorpes Town','2026-08-11 21:45:00',37,'HT/FT: 2/2','3.75','','pending',0,'2026-08-11 11:22:50',NULL,NULL,29,30,1,NULL),(89,'St Neots Town - Stowmarket Town','2026-08-11 21:45:00',26,'St Neots Town Wins','2.15','','pending',0,'2026-08-11 11:22:50',NULL,NULL,31,32,1,NULL),(90,'Institute FC - Ballinamallard Utd','2026-08-11 21:45:00',35,'Institute FC Wins','1.74','','pending',0,'2026-08-11 11:22:50',NULL,NULL,33,34,1,NULL),(91,'Bromsgrove Sporting - Rushall Olympic','2026-08-11 21:45:00',38,'Over 2.5 Goals','1.70','','pending',0,'2026-08-11 11:22:50',NULL,NULL,35,36,1,NULL),(92,'Lyon - Sparta Praha','2026-08-11 22:00:00',39,'Over 2.5 Goals','1.60','3-0','win',0,'2026-08-11 11:22:50',NULL,NULL,37,38,1,NULL),(93,'PFK Nesebar - Dobrudzha','2026-08-10 18:00:00',40,'Correct Score: 0-1','7.00','2-0','lose',0,'2026-08-11 11:24:22',NULL,NULL,39,40,1,NULL),(94,'Zalgiris-2 - Kauno Zalgiris-2','2026-08-10 19:00:00',41,'HT/FT: 1/1','2.50','0-1/2-1','lose',0,'2026-08-11 11:24:22',NULL,NULL,41,42,1,NULL),(95,'FK Riteriai - Zalgiris','2026-08-10 19:30:00',42,'Correct Score: 0-2','7.00','','pending',0,'2026-08-11 11:24:22',NULL,NULL,43,44,1,NULL),(96,'Vasteras SK FK - Djurgårdens','2026-08-10 20:00:00',24,'Djurgårdens Wins','1.80','1-0','lose',0,'2026-08-11 11:24:22',NULL,NULL,45,46,1,NULL),(97,'Sirius IK - Brommapojkarna','2026-08-10 20:00:00',24,'Sirius IK Wins','1.30','2-2','lose',0,'2026-08-11 11:24:22',NULL,NULL,47,48,1,NULL),(98,'Unia Skierniewice - Arka Gdynia','2026-08-10 20:00:00',43,'Arka Gdynia Wins','2.00','2-0','lose',0,'2026-08-11 11:24:22',NULL,NULL,49,50,1,NULL),(99,'Sahel (KUW) - Al Shamiya','2026-08-10 20:25:00',34,'Over 2.5 Goals','1.75','3-0','win',0,'2026-08-11 11:24:22',NULL,NULL,51,52,1,NULL),(100,'NSÍ Runavík - Skala IF','2026-08-10 21:00:00',44,'NSÍ Runavík Wins','1.33','4-0','win',0,'2026-08-11 11:24:22',NULL,NULL,53,54,1,NULL),(101,'Plymouth Argyle - Exeter City','2026-08-10 22:00:00',45,'HT/FT: 1/1','2.10','0-0/2-0','lose',0,'2026-08-11 11:24:22',NULL,NULL,55,56,1,NULL),(102,'FK Andijan - Neftchi Fergana','2026-08-13 17:00:00',46,'Neftchi Fergana Wins','1.66','','pending',0,'2026-08-13 14:49:37',NULL,NULL,57,58,1,NULL),(103,'FC Urartu - Syunik','2026-08-13 18:00:00',47,'FC Urartu Wins','1.40','','pending',0,'2026-08-13 14:49:37',NULL,NULL,59,60,1,NULL),(104,'BSF W - Nørrebro United W','2026-08-13 19:30:00',48,'BSF W Wins','1.73','','pending',0,'2026-08-13 14:49:37',NULL,NULL,61,62,1,NULL),(105,'Tromso - Cluj','2026-08-13 20:00:00',49,'HT/FT: 1/1','2.10','','pending',0,'2026-08-13 14:49:37',NULL,NULL,63,64,1,NULL),(106,'NSÍ Runavík - Lugano','2026-08-13 20:30:00',49,'Lugano Wins','1.33','','pending',0,'2026-08-13 14:49:37',NULL,NULL,53,65,1,NULL),(107,'IK Sleipner - Lindö FF','2026-08-13 21:00:00',50,'IK Sleipner Wins','1.72','','pending',0,'2026-08-13 14:49:37',NULL,NULL,66,67,1,NULL),(108,'Anderlecht - PAOK','2026-08-13 21:30:00',51,'Correct Score: 1-0','7.00','','pending',0,'2026-08-13 14:49:37',NULL,NULL,68,69,1,NULL),(109,'HK Kopavogur - Leiknir Reykjavik','2026-08-13 22:15:00',52,'HK Kopavogur Wins','1.50',NULL,'pending',0,'2026-08-13 14:49:37',NULL,NULL,70,71,1,'99'),(110,'Central Córdoba SdE Res. - Independiente Res.','2026-08-13 23:00:00',53,'Correct Score: 0-1','7.00','','pending',0,'2026-08-13 14:49:37',NULL,NULL,72,73,1,NULL);
/*!40000 ALTER TABLE `matches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `attempts` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotions`
--

DROP TABLE IF EXISTS `promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promotions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL,
  `end_date` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `badge_text` varchar(100) DEFAULT NULL,
  `status` enum('active','passive') DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_promotions_app_id` (`app_id`),
  CONSTRAINT `fk_promotions_app_id` FOREIGN KEY (`app_id`) REFERENCES `apps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotions`
--

LOCK TABLES `promotions` WRITE;
/*!40000 ALTER TABLE `promotions` DISABLE KEYS */;
INSERT INTO `promotions` VALUES (2,2,'2026-08-11 09:00:00','asda','asdasda','asda','active',0,'2026-08-11 09:09:48'),(3,2,'2026-08-15 00:00:00','asdada','asdadsa','asdasda','active',0,'2026-08-11 09:09:48'),(4,1,'2026-08-14 00:00:00','Test Promo','Test Desc','50%1231231231','active',0,'2026-08-11 09:09:48'),(5,2,'2026-11-11 00:00:00','asdasda','adsadda','11','active',0,'2026-08-11 09:10:24');
/*!40000 ALTER TABLE `promotions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'bot_api_url','https://realmobilebet.com/bpiv2/bpav2/api/bpa_history.php'),(2,'bot_cron_fetch','09:00, 14:00'),(3,'bot_cron_result','30'),(4,'bot_status','1'),(9,'smtp_host','smtp.gmail.com'),(10,'smtp_port','465'),(11,'smtp_username','noreply'),(12,'smtp_password','password'),(13,'smtp_from_name','acms'),(14,'smtp_from_email','support@example.com'),(15,'smtp_encryption','ssl');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `league_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(180) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teams`
--

LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
INSERT INTO `teams` VALUES (1,24,'Malmö','malm','https://realmobilebet.com/bpiv2/bpav2/images/teams/718.png',0,'2026-08-09 18:35:24'),(2,24,'Degerfors IF','degerfors-if','https://realmobilebet.com/bpiv2/bpav2/images/teams/741.png',0,'2026-08-09 18:35:24'),(3,25,'Vejle BK','vejle-bk','https://realmobilebet.com/bpiv2/bpav2/images/teams/1245.png',0,'2026-08-09 18:35:24'),(4,25,'Hillerod','hillerod','https://realmobilebet.com/bpiv2/bpav2/images/teams/6344.png',0,'2026-08-09 18:35:24'),(5,26,'Worsbrough Bridge','worsbrough-bridge','https://www.forebet.com/images/icons/blank-logo.png',0,'2026-08-09 18:35:24'),(6,26,'Trafford FC','trafford-fc','https://realmobilebet.com/bpiv2/bpav2/images/teams/8205.png',0,'2026-08-09 18:35:24'),(7,27,'FC Arlanda','fc-arlanda','https://realmobilebet.com/bpiv2/bpav2/images/teams/14907.png',0,'2026-08-09 18:35:24'),(8,27,'Piteå IF','pite-if','https://realmobilebet.com/bpiv2/bpav2/images/teams/5417.png',0,'2026-08-09 18:35:24'),(9,28,'Sokol Brozany','sokol-brozany','https://realmobilebet.com/bpiv2/bpav2/images/teams/8692.png',0,'2026-08-09 18:35:24'),(10,28,'FK Caslav','fk-caslav','https://realmobilebet.com/bpiv2/bpav2/images/teams/2574.png',0,'2026-08-09 18:35:24'),(11,29,'Porto','porto','https://realmobilebet.com/bpiv2/bpav2/images/teams/1009.png',0,'2026-08-09 18:35:24'),(12,29,'FC Alverca','fc-alverca','https://realmobilebet.com/bpiv2/bpav2/images/teams/1156.png',0,'2026-08-09 18:35:24'),(13,30,'Macva Sabac','macva-sabac','https://realmobilebet.com/bpiv2/bpav2/images/teams/5761.png',0,'2026-08-09 18:35:24'),(14,30,'IMT Novi Beograd','imt-novi-beograd','https://realmobilebet.com/bpiv2/bpav2/images/teams/15601.png',0,'2026-08-09 18:35:24'),(15,31,'GV San José','gv-san-jos','https://realmobilebet.com/bpiv2/bpav2/images/teams/23524.png',0,'2026-08-09 18:35:24'),(16,31,'Always Ready','always-ready','https://realmobilebet.com/bpiv2/bpav2/images/teams/9357.png',0,'2026-08-09 18:35:24'),(17,32,'Gungahlin Juventus','gungahlin-juventus','https://realmobilebet.com/bpiv2/bpav2/images/teams/28598.png',0,'2026-08-09 18:35:24'),(18,32,'Canberra White Eagles','canberra-white-eagles','https://realmobilebet.com/bpiv2/bpav2/images/teams/28599.png',0,'2026-08-09 18:35:24'),(19,NULL,'alperen ev sahibi','alperen-ev-sahibi',NULL,1,'2026-08-09 19:32:26'),(20,NULL,'alperen deplasman','alperen-deplasman',NULL,1,'2026-08-09 19:32:26'),(21,33,'Hwacheon KSPO W','hwacheon-kspo-w','https://realmobilebet.com/bpiv2/bpav2/images/teams/27527.png',0,'2026-08-11 11:22:50'),(22,33,'Incheon Red Angels W','incheon-red-angels-w','https://realmobilebet.com/bpiv2/bpav2/images/teams/27523.png',0,'2026-08-11 11:22:50'),(23,34,'Sulaibikhat','sulaibikhat','https://realmobilebet.com/bpiv2/bpav2/images/teams/6083.png',0,'2026-08-11 11:22:50'),(24,34,'Sporty','sporty','https://realmobilebet.com/bpiv2/bpav2/images/teams/28046.png',0,'2026-08-11 11:22:50'),(25,35,'Newry City','newry-city','https://realmobilebet.com/bpiv2/bpav2/images/teams/4847.png',0,'2026-08-11 11:22:50'),(26,35,'Glenavon','glenavon','https://realmobilebet.com/bpiv2/bpav2/images/teams/4839.png',0,'2026-08-11 11:22:50'),(27,36,'Forfar Athletic','forfar-athletic','https://realmobilebet.com/bpiv2/bpav2/images/teams/2149.png',0,'2026-08-11 11:22:50'),(28,36,'Aberdeen Youth','aberdeen-youth','https://realmobilebet.com/bpiv2/bpav2/images/teams/18406.png',0,'2026-08-11 11:22:50'),(29,37,'Quorn','quorn','https://realmobilebet.com/bpiv2/bpav2/images/teams/16109.png',0,'2026-08-11 11:22:50'),(30,37,'Cleethorpes Town','cleethorpes-town','https://realmobilebet.com/bpiv2/bpav2/images/teams/16247.png',0,'2026-08-11 11:22:50'),(31,26,'St Neots Town','st-neots-town','https://realmobilebet.com/bpiv2/bpav2/images/teams/8157.png',0,'2026-08-11 11:22:50'),(32,26,'Stowmarket Town','stowmarket-town','https://www.forebet.com/images/icons/blank-logo.png',0,'2026-08-11 11:22:50'),(33,35,'Institute FC','institute-fc','https://realmobilebet.com/bpiv2/bpav2/images/teams/4844.png',0,'2026-08-11 11:22:50'),(34,35,'Ballinamallard Utd','ballinamallard-utd','https://realmobilebet.com/bpiv2/bpav2/images/teams/4841.png',0,'2026-08-11 11:22:50'),(35,38,'Bromsgrove Sporting','bromsgrove-sporting','https://realmobilebet.com/bpiv2/bpav2/images/teams/8165.png',0,'2026-08-11 11:22:50'),(36,38,'Rushall Olympic','rushall-olympic','https://realmobilebet.com/bpiv2/bpav2/images/teams/1803.png',0,'2026-08-11 11:22:50'),(37,39,'Lyon','lyon','https://realmobilebet.com/bpiv2/bpav2/images/teams/544.png',0,'2026-08-11 11:22:50'),(38,39,'Sparta Praha','sparta-praha','https://realmobilebet.com/bpiv2/bpav2/images/teams/1535.png',0,'2026-08-11 11:22:50'),(39,40,'PFK Nesebar','pfk-nesebar','https://realmobilebet.com/bpiv2/bpav2/images/teams/1272.png',0,'2026-08-11 11:24:22'),(40,40,'Dobrudzha','dobrudzha','https://realmobilebet.com/bpiv2/bpav2/images/teams/1273.png',0,'2026-08-11 11:24:22'),(41,41,'Zalgiris-2','zalgiris-2','https://realmobilebet.com/bpiv2/bpav2/images/teams/9838.png',0,'2026-08-11 11:24:22'),(42,41,'Kauno Zalgiris-2','kauno-zalgiris-2','https://realmobilebet.com/bpiv2/bpav2/images/teams/14370.png',0,'2026-08-11 11:24:22'),(43,42,'FK Riteriai','fk-riteriai','https://realmobilebet.com/bpiv2/bpav2/images/teams/3218.png',0,'2026-08-11 11:24:22'),(44,42,'Zalgiris','zalgiris','https://realmobilebet.com/bpiv2/bpav2/images/teams/3210.png',0,'2026-08-11 11:24:22'),(45,24,'Vasteras SK FK','vasteras-sk-fk','https://realmobilebet.com/bpiv2/bpav2/images/teams/743.png',0,'2026-08-11 11:24:22'),(46,24,'Djurgårdens','djurg-rdens','https://realmobilebet.com/bpiv2/bpav2/images/teams/716.png',0,'2026-08-11 11:24:22'),(47,24,'Sirius IK','sirius-ik','https://realmobilebet.com/bpiv2/bpav2/images/teams/751.png',0,'2026-08-11 11:24:22'),(48,24,'Brommapojkarna','brommapojkarna','https://realmobilebet.com/bpiv2/bpav2/images/teams/739.png',0,'2026-08-11 11:24:22'),(49,43,'Unia Skierniewice','unia-skierniewice','https://realmobilebet.com/bpiv2/bpav2/images/teams/7479.png',0,'2026-08-11 11:24:22'),(50,43,'Arka Gdynia','arka-gdynia','https://realmobilebet.com/bpiv2/bpav2/images/teams/1182.png',0,'2026-08-11 11:24:22'),(51,34,'Sahel (KUW)','sahel-kuw','https://realmobilebet.com/bpiv2/bpav2/images/teams/6084.png',0,'2026-08-11 11:24:22'),(52,34,'Al Shamiya','al-shamiya','https://realmobilebet.com/bpiv2/bpav2/images/teams/28045.png',0,'2026-08-11 11:24:22'),(53,49,'NSÍ Runavík','ns-runav-k','https://realmobilebet.com/bpiv2/bpav2/images/teams/6012.png',0,'2026-08-11 11:24:22'),(54,44,'Skala IF','skala-if','https://realmobilebet.com/bpiv2/bpav2/images/teams/3809.png',0,'2026-08-11 11:24:22'),(55,45,'Plymouth Argyle','plymouth-argyle','https://realmobilebet.com/bpiv2/bpav2/images/teams/81.png',0,'2026-08-11 11:24:22'),(56,45,'Exeter City','exeter-city','https://realmobilebet.com/bpiv2/bpav2/images/teams/43.png',0,'2026-08-11 11:24:22'),(57,46,'FK Andijan','fk-andijan','https://realmobilebet.com/bpiv2/bpav2/images/teams/4630.png',0,'2026-08-13 14:49:37'),(58,46,'Neftchi Fergana','neftchi-fergana','https://realmobilebet.com/bpiv2/bpav2/images/teams/4624.png',0,'2026-08-13 14:49:37'),(59,47,'FC Urartu','fc-urartu','https://realmobilebet.com/bpiv2/bpav2/images/teams/2804.png',0,'2026-08-13 14:49:37'),(60,47,'Syunik','syunik','https://realmobilebet.com/bpiv2/bpav2/images/teams/2801.png',0,'2026-08-13 14:49:37'),(61,48,'BSF W','bsf-w','https://www.forebet.com/images/icons/blank-logo.png',0,'2026-08-13 14:49:37'),(62,48,'Nørrebro United W','n-rrebro-united-w','https://www.forebet.com/images/icons/blank-logo.png',0,'2026-08-13 14:49:37'),(63,49,'Tromso','tromso','https://realmobilebet.com/bpiv2/bpav2/images/teams/786.png',0,'2026-08-13 14:49:37'),(64,49,'Cluj','cluj','https://realmobilebet.com/bpiv2/bpav2/images/teams/1529.png',0,'2026-08-13 14:49:37'),(65,49,'Lugano','lugano','https://realmobilebet.com/bpiv2/bpav2/images/teams/1827.png',0,'2026-08-13 14:49:37'),(66,50,'IK Sleipner','ik-sleipner','https://realmobilebet.com/bpiv2/bpav2/images/teams/772.png',0,'2026-08-13 14:49:37'),(67,50,'Lindö FF','lind-ff','https://realmobilebet.com/bpiv2/bpav2/images/teams/12858.png',0,'2026-08-13 14:49:37'),(68,51,'Anderlecht','anderlecht','https://realmobilebet.com/bpiv2/bpav2/images/teams/1113.png',0,'2026-08-13 14:49:37'),(69,51,'PAOK','paok','https://realmobilebet.com/bpiv2/bpav2/images/teams/1014.png',0,'2026-08-13 14:49:37'),(70,52,'HK Kopavogur','hk-kopavogur','https://realmobilebet.com/bpiv2/bpav2/images/teams/2041.png',0,'2026-08-13 14:49:37'),(71,52,'Leiknir Reykjavik','leiknir-reykjavik','https://realmobilebet.com/bpiv2/bpav2/images/teams/3420.png',0,'2026-08-13 14:49:37'),(72,53,'Central Córdoba SdE Res.','central-c-rdoba-sde-res','https://realmobilebet.com/bpiv2/bpav2/images/teams/22635.png',0,'2026-08-13 14:49:37'),(73,53,'Independiente Res.','independiente-res','https://realmobilebet.com/bpiv2/bpav2/images/teams/22627.png',0,'2026-08-13 14:49:37');
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `admin_reply` text DEFAULT NULL,
  `status` enum('open','pending','closed','cancelled') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `category` varchar(100) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `admin_reply_at` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `app_id` (`app_id`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`app_id`) REFERENCES `apps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (1,11,1,'test1',NULL,'open','2026-08-11 11:52:56','2026-08-11 14:52:56','Payment & GPA Code','Payment & GPA Code',NULL,0),(2,11,1,'test2','detaylı cevap veriyorum','cancelled','2026-08-11 11:53:18','2026-08-11 14:54:15','Technical Issue','Technical Issue','2026-08-11 14:53:44',0),(3,11,1,'test4',NULL,'open','2026-08-11 11:54:20','2026-08-11 14:54:20','Payment & GPA Code','Payment & GPA Code',NULL,0);
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_apps`
--

DROP TABLE IF EXISTS `user_apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_app_unique` (`user_id`,`app_id`),
  KEY `app_id` (`app_id`),
  CONSTRAINT `user_apps_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_apps_ibfk_2` FOREIGN KEY (`app_id`) REFERENCES `apps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_apps`
--

LOCK TABLES `user_apps` WRITE;
/*!40000 ALTER TABLE `user_apps` DISABLE KEYS */;
INSERT INTO `user_apps` VALUES (1,1,1,'approved','2026-08-09 16:24:58'),(2,2,1,'approved','2026-08-09 16:36:41'),(6,5,1,'pending','2026-08-11 09:32:24'),(7,6,1,'rejected','2026-08-11 09:32:24'),(8,7,1,'pending','2026-08-11 09:32:25'),(9,8,1,'pending','2026-08-11 09:36:18'),(13,10,1,'pending','2026-08-11 11:15:21'),(15,11,1,'approved','2026-08-11 11:37:27'),(17,9,1,'approved','2026-08-11 12:50:51'),(18,12,1,'rejected','2026-08-11 13:12:19'),(19,13,1,'rejected','2026-08-12 18:01:23'),(20,14,1,'approved','2026-08-12 18:06:08');
/*!40000 ALTER TABLE `user_apps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gpa_code` varchar(255) DEFAULT NULL,
  `is_banned` tinyint(1) DEFAULT 0,
  `ban_reason` text DEFAULT NULL,
  `exempt_force_update` tinyint(1) DEFAULT 0,
  `exempt_security` tinyint(1) DEFAULT 0 COMMENT 'Muafiyet (User-Agent/Mobile)',
  `last_login_ip` varchar(45) DEFAULT NULL,
  `last_login_date` datetime DEFAULT NULL,
  `device_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  `gamification_badge` varchar(255) DEFAULT NULL,
  `session_token` varchar(255) DEFAULT NULL,
  `failed_login_attempts` int(11) DEFAULT 0,
  `lockout_time` datetime DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `reset_token` varchar(10) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `deleted_by_user` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'alper','alper@gmail.com','$2y$10$ftK4qho6QzLBQ2ToxcIs3.eZxLg/mo6SQw/xutJJCgGUD.OCzfH/G','',0,NULL,0,0,'::1','2026-08-13 23:45:56',NULL,'2026-08-09 16:24:58',0,'VIP Member','5d46e4c8a1c15b2da66ea679107f70cd46afc3b52bb3df037a0748043397d819',0,NULL,'2026-08-12 22:21:55',NULL,NULL,0),(2,'Real Fixed','alpe2r@gmail.com','$2y$10$O0tsfLx75ycOWD0Da4.LhOgZcWUMkrIJ2SGLP6nbwAXDM5ISy6cIi','',0,NULL,0,0,'::1','2026-08-12 22:17:19',NULL,'2026-08-09 16:36:41',0,'VIP Member,Verified Account,Elite Status',NULL,0,NULL,NULL,NULL,NULL,1),(5,'API Tester','test_1786440744@test.com','$2y$10$LFMcy6bluOtFnf.uZUMLie9sfFLxd1OU8eeU36UE59gnBB3/m0hvC',NULL,0,NULL,0,0,'::1','2026-08-11 12:32:25',NULL,'2026-08-11 09:32:24',0,NULL,NULL,0,NULL,NULL,NULL,NULL,0),(6,'Lockout User','lockout_1786440744@test.com','$2y$10$WFjTHZEqkWqx7z4WnGkz2e6KVeJNMmnwNtXmbsBsDvQFzlj575/B6',NULL,0,NULL,0,0,NULL,NULL,NULL,'2026-08-11 09:32:24',0,NULL,'4040a6db50813a85859a4c3e8126e7500abce9f9ffe1f0eede927f24698c4507',5,'2026-08-11 13:02:24',NULL,NULL,NULL,0),(7,'Lockout User','lockout_1786440745@test.com','$2y$10$FCiYUstcT0b78XW.V/jdJOI1MwbgE/m94rAlhLsQhOjNfo5GmzoI6',NULL,0,NULL,0,0,NULL,NULL,NULL,'2026-08-11 09:32:25',0,NULL,'54a42349e3f40a924c7c54fb84b1a7c0ea39266cb5463ec30fb011cc5610e1b2',5,'2026-08-11 13:02:25',NULL,NULL,NULL,0),(8,'Match Tester','match_tester_1786440978@test.com','$2y$10$EbZYWw9hW9G8eyBQT7hZZ.4GAV7N9vAPtAkqC478zCgeADxBBpoX.',NULL,0,NULL,0,0,NULL,NULL,NULL,'2026-08-11 09:36:18',1,NULL,'2fac76beeafe98a0f8820a2307780cb8f70e632090ca2419d34405a10342be57',0,NULL,'2026-08-11 12:36:18',NULL,NULL,0),(9,'Test User','testuser123@acms.com','$2y$10$yDC4FDFsDyKm64YJsobK9.sKsdyyTZ0gDjOo2KqEkgkv7ep77Bi8q','',0,NULL,0,0,'::1','2026-08-11 16:10:00',NULL,'2026-08-11 10:46:12',0,'','c9d5d872e0e4079c158f6cafde2eaba9f1dc3060341bf931016b48e3caaba04b',0,NULL,'2026-08-12 23:28:22',NULL,NULL,0),(10,'asdadasda','testuser12344@acms.com','$2y$10$zoSSQQg6mhYX30J3HbLP8eL6uCsl70dh/h70dML.wLxc4lH79sB5u','',0,NULL,0,0,NULL,'2026-08-11 14:25:11',NULL,'2026-08-11 11:15:21',1,'','fa23e2916392269ab1a86b7881904bce4753f56ea598861eb6a8218184818427',0,NULL,NULL,NULL,NULL,0),(11,'alper1','alper1@gmail.com','$2y$10$K/blWTAz6PsZw286iCReKOjc4ZSajxCnxz3ZJJa3iR8tFsFAItmQG','gpa5',0,NULL,0,0,'::1','2026-08-12 22:55:25',NULL,'2026-08-11 11:37:27',0,'VIP Member,Verified Account,Early Supporter,Elite Status,Top Winner,Veteran','8be7def34f4d43aafff864fa9b99a301623ab1427bdb5b589b1f2b1154eaf4ca',0,NULL,'2026-08-12 23:22:35',NULL,NULL,0),(12,'John Doe','john@example.com','$2y$10$tKYSCE/j.m5OWFvxoVhvleY2a9QrW66YeGrEDLiWfDEYzcvsfasmq','GPA.1111-2222-3333-44444',1,'Test ban reason',0,0,'::1','2026-08-11 16:19:32',NULL,'2026-08-11 13:12:19',1,NULL,'51c0b35f3bf1f137d89c6bffed4269062ea7aa979c5a48df467042f57065c276',0,NULL,'2026-08-11 16:16:34',NULL,NULL,0),(13,'John','john2@example.com','$2y$10$7UyHiVKvhh2lTzb7u1ACs.RT/cJTzR9ian4iIWbXEEVncQFsbE3Mm','GPA.1234-1234-1234-12345',1,'Testing',0,0,'::1','2026-08-12 21:02:09',NULL,'2026-08-12 18:01:23',1,NULL,'785cdfe63b45f36f14d4e3595ac3deb0af1c02e9cc6336ece3d0f557fdc0e26f',0,NULL,NULL,NULL,NULL,0),(14,'John','john3@example.com','$2y$10$qxDPv5iHg37u6PycIJj5yOacHhLtqLBQA2liwlooLubAvcoqjKD0y','GPA.1234-1234-1234-12346',0,NULL,0,0,'::1','2026-08-12 21:07:13',NULL,'2026-08-12 18:06:08',1,NULL,'5378e43dd4893e42331882d7cf192539143b1ea1a6825f1e17f4c161edd0b814',0,NULL,'2026-08-12 21:06:34',NULL,NULL,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'acms'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-13 23:49:33
