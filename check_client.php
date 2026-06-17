<?php
$host = '127.0.1.30';
$db   = 'abu';
$user = 'root';
$pass = '';

try {
    $pdo = new \PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT client_id, client_secret FROM oauth_clients WHERE client_id = 'TestClient'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo "✅ Клиент найден: client_id = {$row['client_id']}, client_secret = {$row['client_secret']}\n";
    } else {
        echo "❌ Клиент TestClient НЕ НАЙДЕН. Добавьте его.\n";
        $pdo->exec("INSERT INTO oauth_clients (client_id, client_secret) VALUES ('TestClient', 'test_secret')");
        echo "✅ Клиент создан.\n";
    }
} catch (\PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
}
