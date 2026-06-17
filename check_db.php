<?php
$pdo = new PDO('mysql:host=127.0.1.30;dbname=abu;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "USERS:\n";
foreach ($pdo->query('SELECT id, email, username, LEFT(password, 30) AS pw, active FROM users') as $r) {
    echo '  ' . json_encode($r, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
echo "OAUTH CLIENTS:\n";
foreach ($pdo->query('SELECT * FROM oauth_clients') as $r) {
    echo '  ' . json_encode($r, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
echo "OAUTH ACCESS TOKENS (last 5):\n";
foreach ($pdo->query('SELECT id, access_token, client_id, user_id, expires FROM oauth_access_tokens ORDER BY id DESC LIMIT 5') as $r) {
    echo '  ' . json_encode($r, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
echo "RATINGS (count):\n";
$row = $pdo->query('SELECT COUNT(*) AS c FROM ratings')->fetch();
echo '  ' . $row['c'] . PHP_EOL;
echo "RATINGS SCHEMA:\n";
foreach ($pdo->query('DESCRIBE ratings') as $r) {
    echo '  ' . $r['Field'] . ' ' . $r['Type'] . PHP_EOL;
}
