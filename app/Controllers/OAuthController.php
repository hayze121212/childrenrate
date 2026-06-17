<?php
namespace App\Controllers;

use App\Services\OAuth;
use OAuth2\Request;

class OAuthController extends BaseController
{
    protected $OAuth;

    public function __construct()
    {
        $this->OAuth = new OAuth();
    }

    public function token()
    {
        // Разрешаем все кросс-доменные запросы
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        // Если это предварительный запрос OPTIONS – сразу отвечаем
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        // Включаем отображение ошибок (поможет увидеть реальную причину)
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $request = Request::createFromGlobals();
        $response = $this->OAuth->server->handleTokenRequest($request);
        $response->send();
    }
}