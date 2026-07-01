<?php
/**
 * config_pdo.php
 *
 * PDO variant of config.php, for endpoints written against PDO
 * (create_order.php, place_order.php). Same env-var approach — see
 * config.php for details and .env.example for the variable names.
 */

if (!function_exists('load_env')) {
    function load_env(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if ($key !== '' && getenv($key) === false) {
                putenv("$key=$value");
            }
        }
    }
}
load_env(__DIR__ . '/.env');

$DB_HOST = getenv('DB_HOST') ?: '';
$DB_PORT = getenv('DB_PORT') ?: '3306';
$DB_USER = getenv('DB_USER') ?: '';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: '';

if ($DB_HOST === '' || $DB_USER === '' || $DB_NAME === '') {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        "success" => false,
        "message" => "Server misconfigured: database environment variables are not set."
    ]);
    exit;
}

try {
    $conn = new PDO(
        "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}
