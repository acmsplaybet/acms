<?php
/**
 * ACMS Database Connection Class (PDO Singleton)
 * Secure, OOP-based database connection engine with environment awareness & fallback.
 */

require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $conn;

    // Private constructor prevents multiple connections
    private function __construct() {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Secure error handling
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Always fetch as associative array
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Native prepared statements for security
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ];

        // Primary Host Attempt
        $host = DB_HOST;
        $db   = DB_NAME;
        $user = DB_USER;
        $pass = DB_PASS;

        try {
            $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
            $this->conn = new PDO($dsn, $user, $pass, $options);
        } catch(PDOException $e) {
            // If primary host fails and a fallback host is defined (e.g. 185.179.27.23)
            if (defined('DB_HOST_FALLBACK') && DB_HOST_FALLBACK !== $host) {
                try {
                    $fallbackHost = DB_HOST_FALLBACK;
                    $dsnFallback = "mysql:host={$fallbackHost};dbname={$db};charset=utf8mb4";
                    $this->conn = new PDO($dsnFallback, $user, $pass, $options);
                    return;
                } catch (PDOException $e2) {
                    $this->handleError($e2);
                    return;
                }
            }
            $this->handleError($e);
        }
    }

    private function handleError(PDOException $e) {
        if (defined('ENV_TYPE') && ENV_TYPE === 'development') {
            $errorMsg = "Veritabanı bağlantı hatası: " . $e->getMessage();
        } else {
            $errorMsg = "Veritabanı bağlantısı sağlanamadı. Lütfen daha sonra tekrar deneyiniz.";
        }

        if (php_sapi_name() === 'cli') {
            fwrite(STDERR, "Database Connection Error: " . $e->getMessage() . PHP_EOL);
            exit(1);
        }

        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => $errorMsg
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Singleton instance getter
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Connection getter
    public function getConnection() {
        return $this->conn;
    }
}
