<?php
// Set root directory sebagai base path
chdir(dirname(__DIR__));

$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

switch ($path) {
    case '/':
    case '/index.php':
        require __DIR__ . '/../app/index.php';
        break;
    case '/login.php':
        require __DIR__ . '/../app/login.php';
        break;
    case '/logout.php':
        require __DIR__ . '/../app/logout.php';
        break;
    case '/dokter/list.php':
        require __DIR__ . '/../app/dokter/list.php';
        break;
    case '/dokter/tambah.php':
        require __DIR__ . '/../app/dokter/tambah.php';
        break;
    case '/poli/list.php':
        require __DIR__ . '/../app/poli/list.php';
        break;
    case '/poli/tambah.php':
        require __DIR__ . '/../app/poli/tambah.php';
        break;
    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}