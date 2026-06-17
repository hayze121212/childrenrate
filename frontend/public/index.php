<?php
// Добавляем CORS-заголовки (вставьте это в самое начало!)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

use CodeIgniter\Boot;
use Config\Paths;

$minPhpVersion = '8.2';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;
    exit(1);
}

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

require FCPATH . '../app/Config/Paths.php';
$paths = new Paths();

require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));