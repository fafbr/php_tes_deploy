<?php

// Load environment variables dari .env
$envPath = __DIR__ . '/../.env';

if (file_exists($envPath)) {
    $lines = file(
        $envPath,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }

        if (strpos($line, '=') === false) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);

        $name = trim($name);
        $value = trim($value);

        if (
            !array_key_exists($name, $_SERVER) &&
            !array_key_exists($name, $_ENV)
        ) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Konfigurasi database
$host = $_ENV['POSTGRES_HOST']
    ?? $_SERVER['POSTGRES_HOST']
    ?? getenv('POSTGRES_HOST')
    ?: 'localhost';

$port = $_ENV['POSTGRES_PORT']
    ?? $_SERVER['POSTGRES_PORT']
    ?? getenv('POSTGRES_PORT')
    ?: '5432';

$db = $_ENV['POSTGRES_DATABASE']
    ?? $_SERVER['POSTGRES_DATABASE']
    ?? getenv('POSTGRES_DATABASE')
    ?: 'polimedic_db';

$user = $_ENV['POSTGRES_USER']
    ?? $_SERVER['POSTGRES_USER']
    ?? getenv('POSTGRES_USER')
    ?: 'postgres';

$pass = $_ENV['POSTGRES_PASSWORD']
    ?? $_SERVER['POSTGRES_PASSWORD']
    ?? getenv('POSTGRES_PASSWORD')
    ?: 'satuduatiga';

// Konfigurasi koneksi
$isLocal = in_array($host, ['127.0.0.1', 'localhost'], true);

$dsn = "pgsql:host={$host};port={$port};dbname={$db}";

if (!$isLocal) {
    $dsn .= ';sslmode=require';
}

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_PERSISTENT => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    error_log('Koneksi database gagal: ' . $e->getMessage());

    http_response_code(500);
    exit('Koneksi database gagal. Silakan coba lagi nanti.');
}