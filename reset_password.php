<?php
$host = '127.0.1.30';
$db   = 'abu';
$user = 'root';
$pass = '';

try {
    $pdo = new \PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    // Генерируем хеш для пароля 'password'
    $hash = password_hash('password', PASSWORD_DEFAULT);
    
    // Обновляем пароль для admin@admin.com
    $sql = "UPDATE users SET password = '$hash' WHERE email = 'admin@admin.com'";
    $pdo->exec($sql);
    
    echo "✅ Пароль для admin@admin.com сброшен на 'password'.\n";
} catch (\PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
}