<?php
$host = '127.0.1.30';
$db   = 'abu';
$user = 'root';
$pass = '';

try {
    $pdo = new \PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    // Проверяем, есть ли уже данные
    $stmt = $pdo->query("SELECT COUNT(*) FROM ratings");
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "⚠️ В таблице ratings уже есть $count записей.\n";
        echo "Если хотите добавить ещё, выполните SQL вручную.\n";
    } else {
        $sql = "INSERT INTO `ratings` (`user_id`, `name`, `rating`, `comment`) VALUES
            (1, 'Иван Иванов', 5, 'Отличный сотрудник!'),
            (2, 'Петр Петров', 4, 'Хорошо работает'),
            (3, 'Сидор Сидоров', 3, 'Можно лучше')";
        $pdo->exec($sql);
        echo "✅ Данные добавлены в таблицу ratings!\n";
    }
} catch (\PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
}