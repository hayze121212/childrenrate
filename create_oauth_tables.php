<?php
$host = '127.0.1.30';
$db   = 'abu';
$user = 'root';
$pass = '';

try {
    $pdo = new \PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    // oauth_clients (если ещё нет)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `oauth_clients` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `client_id` varchar(255) NOT NULL,
        `client_secret` varchar(255) NOT NULL,
        `redirect_uri` varchar(255) DEFAULT NULL,
        `grant_types` varchar(255) DEFAULT NULL,
        `scope` varchar(255) DEFAULT NULL,
        `user_id` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    // oauth_access_tokens
    $pdo->exec("CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `access_token` varchar(255) NOT NULL,
        `client_id` varchar(255) NOT NULL,
        `user_id` int(11) DEFAULT NULL,
        `expires` timestamp NULL DEFAULT NULL,
        `scope` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    // oauth_refresh_tokens
    $pdo->exec("CREATE TABLE IF NOT EXISTS `oauth_refresh_tokens` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `refresh_token` varchar(255) NOT NULL,
        `client_id` varchar(255) NOT NULL,
        `user_id` int(11) DEFAULT NULL,
        `expires` timestamp NULL DEFAULT NULL,
        `scope` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    // oauth_scopes (если понадобится)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `oauth_scopes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `scope` varchar(255) NOT NULL,
        `is_default` tinyint(1) DEFAULT '0',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    echo "Все таблицы OAuth созданы успешно!\n";
} catch (\PDOException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}