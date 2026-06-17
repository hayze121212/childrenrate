<?php
$host = '127.0.1.30';
$db   = 'abu';
$user = 'root';
$pass = '';

try {
    $pdo = new \PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    $hash = password_hash('password', PASSWORD_DEFAULT);
    $sql = "INSERT INTO `users` (`email`, `username`, `password`, `active`) VALUES
        ('admin@admin.com', 'admin', '$hash', 1)";
    $pdo->exec($sql);
    echo "Пользователь admin@admin.com добавлен!\n";
} catch (\PDOException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}