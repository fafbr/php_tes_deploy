
<?php

$debugStart = microtime(true);
$debugLast = $debugStart;

function debugTiming(string $label): void
{
    global $debugStart, $debugLast;

    $now = microtime(true);

    error_log(sprintf(
        '[TIMING] %s: +%.2f ms | total %.2f ms',
        $label,
        ($now - $debugLast) * 1000,
        ($now - $debugStart) * 1000
    ));

    $debugLast = $now;
}

require_once __DIR__ . '/koneksi.php';
debugTiming('Koneksi database selesai');

require_once __DIR__ . '/session_handler.php';

$handler = new PdoSessionHandler($pdo);
session_set_save_handler($handler, true);
debugTiming('Session handler dipasang');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
debugTiming('session_start selesai');

$base_url = '/';

function base_url($path = '') {
    global $base_url;
    return rtrim($base_url, '/') . '/' . ltrim($path, '/');
}

function auth_protect() {
    $request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $guest_pages = ['/login.php', '/register.php'];

    if (!in_array($request_uri, $guest_pages) && !isset($_SESSION['user_logged_in'])) {
        header('Location: ' . base_url('login.php'));
        exit;
    }
}

auth_protect();
debugTiming('auth_protect selesai');