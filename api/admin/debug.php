<?php
require 'c:/xampp/htdocs/acms/api/config/Database.php';
$db = Database::getInstance()->getConnection();
print_r($db->query('SHOW TABLES LIKE "audit_logs"')->fetchAll());
