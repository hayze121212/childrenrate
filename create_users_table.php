<?php
$host = '127.0.1.30';
$db   = 'abu';
$user = 'root';
$pass = '';

try {
    $pdo = new \PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `email` varchar(255) NOT NULL,
        `username` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `active` tinyint(1) NOT NULL DEFAULT 1,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $pdo->exec($sql);
    echo "Таблица users создана!\n";
} catch (\PDOException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}