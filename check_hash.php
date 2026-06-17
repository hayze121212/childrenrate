<?php
$host = '127.0.1.30';
$db   = 'abu';
$user = 'root';
$pass = '';

try {
    $pdo = new \PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT password FROM users WHERE email = 'admin@admin.com'");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $hash = $row['password'];
        $password = 'password';
        $verified = password_verify($password, $hash);
        echo "Хеш в базе: $hash\n";
        echo "Пароль 'password' верен: " . ($verified ? '✅ ДА' : '❌ НЕТ') . "\n";
    } else {
        echo "❌ Пользователь admin@admin.com не найден.\n";
    }
} catch (\PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
}