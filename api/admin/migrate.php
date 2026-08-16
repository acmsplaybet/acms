<?php
require_once __DIR__ . '/../config/Database.php';
$db = Database::getInstance()->getConnection();
try {
    $db->exec('ALTER TABLE users ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;');
    echo 'Users updated. ';
} catch(Exception $e) { echo $e->getMessage() . ' '; }

$tables = ['apps', 'matches', 'leagues', 'teams'];
foreach($tables as $table) {
    try {
        $db->exec("ALTER TABLE $table ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;");
        echo ucfirst($table) . ' updated. ';
    } catch(Exception $e) {
        echo $e->getMessage() . ' ';
    }
}
?>
