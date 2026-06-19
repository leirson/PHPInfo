<?php
$host = '127.0.0.1';
$db   = 'NOMEDOBANCO';
$user = 'USUARIODOBANCO';
$pass = 'SENHADOBANCO';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

if (!is_dir(__DIR__ . '/backups')) {
    @mkdir(__DIR__ . '/backups', 0755, true);
}
if (!is_dir(__DIR__ . '/uploads')) {
    @mkdir(__DIR__ . '/uploads', 0755, true);
}
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
