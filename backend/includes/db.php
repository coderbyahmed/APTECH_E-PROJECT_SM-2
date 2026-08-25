<?php
/**
 * SOUND Group — Database Connection (PDO)
 */

require_once __DIR__ . '/../config/database.php';

function getDb() {
    static $pdo = null;
    if ($pdo === null) {
        $cfg = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['database']};charset={$cfg['charset']}";
        try {
            $pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(503);
            include __DIR__ . '/../error-handling/503.php';
            exit;
        }
    }
    return $pdo;
}
