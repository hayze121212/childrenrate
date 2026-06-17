<?php
namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class ApiController extends BaseController
{
    use ResponseTrait;

    // Текущий пользователь по токену (GET)
    public function user()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        $auth = new \App\Services\OAuth();
        if (!$auth->isLoggedIn()) {
            return $this->failUnauthorized('Требуется авторизация');
        }

        $token  = $auth->server->getResourceController()->getToken();
        $userId = $token['user_id'] ?? null;

        $db  = \Config\Database::connect();
        $row = $db->table('users')->where('id', $userId)->get()->getRowArray();
        if (!$row) {
            return $this->failNotFound('Пользователь не найден');
        }

        return $this->respond([
            'id'       => (int) $row['id'],
            'username' => $row['username'],
            'email'    => $row['email'],
        ]);
    }
}
