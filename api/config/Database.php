<?php
/**
 * ACMS Database Connection Class (PDO Singleton)
 * Secure, OOP-based database connection engine.
 */

require_once 'config.php';

class Database {
    private static $instance = null;
    private $conn;

    // Private constructor prevents multiple connections
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Secure error handling
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Always fetch as associative array
                PDO::ATTR_EMULATE_PREPARES   => false,                  // Native prepared statements for security
            ];

            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch(PDOException $e) {
            // In a real production environment, log this to a file instead of echoing
            echo json_encode([
                "status" => "error",
                "message" => "Veritabanı bağlantı hatası: " . $e->getMessage()
            ]);
            exit;
        }
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
