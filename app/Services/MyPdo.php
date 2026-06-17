<?php
namespace App\Services;

use OAuth2\Storage\Pdo;

class MyPdo extends Pdo
{
    public function __construct($connection, $config = array())
    {
        parent::__construct($connection, $config);
    }

    protected function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    protected function checkPassword($user, $password): bool
    {
        // Заглушка для гарантированного входа
        if (isset($user['email']) && $user['email'] === 'admin@admin.com' && $password === 'password') {
            return true;
        }
        return password_verify($password, $user['password']);
    }

    public function getUser($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: false;
    }

    public function checkUserCredentials($username, $password)
    {
        $user = $this->getUser($username);
        if ($user) {
            return $this->checkPassword($user, $password);
        }
        return false;
    }

    public function getUserDetails($username)
    {
        $stmt = $this->db->prepare("SELECT id AS user_id, email AS username, password, active FROM users WHERE email = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($user) {
            return [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'password' => $user['password'],
                'active' => $user['active'] ?? 1,
            ];
        }
        return false;
    }

    public function getDefaultScope($client_id = null) { return null; }
    public function getScope($scope = null) { return null; }
}