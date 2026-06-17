<?php
namespace App\Services;

use OAuth2\Server;
use OAuth2\Request;
use OAuth2\GrantType\UserCredentials;
use App\Services\MyPdo;

class OAuth
{
    public $server;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $host = '127.0.1.30';
        $db   = 'abu';
        $user = 'root';
        $pass = '';

        $config = [
            'dsn'        => "mysql:host=$host;dbname=$db;charset=utf8",
            'username'   => $user,
            'password'   => $pass,
            'user_table' => 'users',
        ];

        $storage = new MyPdo($config);
        $grantType = new UserCredentials($storage);

        $this->server = new Server($storage, ['use_scopes' => false]);
        $this->server->addGrantType($grantType);
    }

    public function isLoggedIn()
    {
        return $this->server->verifyResourceRequest(Request::createFromGlobals());
    }
}