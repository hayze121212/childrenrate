<?php
$host = '127.0.1.30';
$db   = 'abu';
$user = 'root';
$pass = '';

try {
    $pdo = new \PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    // Проверяем, существует ли клиент
    $stmt = $pdo->query("SELECT COUNT(*) FROM oauth_clients WHERE client_id = 'TestClient'");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $sql = "INSERT INTO oauth_clients (client_id, client_secret) VALUES ('TestClient', 'test_secret')";
        $pdo->exec($sql);
        echo "✅ Клиент TestClient создан.\n";
    } else {
        echo "ℹ️ Клиент TestClient уже существует.\n";
    }
} catch (\PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
}