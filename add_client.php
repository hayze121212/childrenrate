<?php
$host = '127.0.1.30';
$db   = 'abu';
$user = 'root';
$pass = '';

try {
    $pdo = new \PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO oauth_clients (client_id, client_secret) VALUES ('TestClient', 'test_secret')";
    $pdo->exec($sql);
    echo "Клиент TestClient добавлен!\n";
} catch (\PDOException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}
