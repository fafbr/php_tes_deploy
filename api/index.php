<?php

chdir(dirname(__DIR__));

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routes = [
    '/' => 'index.php',
    '/index.php' => 'index.php',
    '/login.php' => 'login.php',
    '/register.php' => 'register.php',
    '/logout.php' => 'logout.php',

    '/dokter/list.php' => 'dokter/list.php',
    '/dokter/tambah.php' => 'dokter/tambah.php',
    '/dokter/proses.php' => 'dokter/proses.php',
    '/dokter/hapus.php' => 'dokter/hapus.php',

    '/poli/list.php' => 'poli/list.php',
    '/poli/tambah.php' => 'poli/tambah.php',
    '/poli/proses.php' => 'poli/proses.php',
    '/poli/hapus.php' => 'poli/hapus.php',
];

if (isset($routes[$path])) {
    require __DIR__ . '/../' . $routes[$path];
    exit;
}

http_response_code(404);
echo '404 Not Found';