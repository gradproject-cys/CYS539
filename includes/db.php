<?php
// includes/db.php — PDO connection (prepared statements throughout)

function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $host = getenv('DB_HOST') ?: 'db';
    $name = getenv('DB_NAME') ?: 'xsslab';
    $user = getenv('DB_USER') ?: 'labuser';
    $pass = getenv('DB_PASS') ?: 'labpass123';

    $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,   // enforce real prepared statements
    ]);

    return $pdo;
}
