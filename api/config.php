<?php
/**
 * config.php
 * Central database connection for Aiven Cloud (guarantees both $pdo and $conn exist)
 *
 * Accepts EITHER naming convention for env vars, since this repo mixes a
 * Laravel app (config/database.php, which typically uses DB_DATABASE /
 * DB_USERNAME / DB_PASSWORD) with these standalone api/*.php scripts:
 *   DB_HOST powers both
 *   DB_PORT powers both
 *   DB_NAME       or DB_DATABASE
 *   DB_USER       or DB_USERNAME
 *   DB_PASS       or DB_PASSWORD
 *
 * No hardcoded credential fallback — if nothing is set under either name,
 * this fails loudly with a clear message instead of silently using a
 * baked-in secret (which is what caused the original credential leak).
 */

ini_set('display_errors', '0');
error_reporting(E_ALL);

if (!function_exists('load_env_direct')) {
    function load_env_direct(string $path): void {
        if (!file_exists($path)) return;
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') continue;
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                if (getenv($key) === false) {
                    putenv("$key=$value");
                }
                if (!isset($_ENV[$key])) {
                    $_ENV[$key] = $value;
                }
            }
        }
    }
}

// Load .env only as a local-dev convenience; Railway injects real env vars directly.
load_env_direct(__DIR__ . '/.env');
load_env_direct(__DIR__ . '/../.env');

function env_any(array $keys): ?string {
    foreach ($keys as $key) {
        $value = getenv($key);
        if ($value === false || $value === '') {
            $value = $_ENV[$key] ?? null;
        }
        if ($value !== null && $value !== '') {
            return (string)$value;
        }
    }
    return null;
}

function required_env_any(array $keys): string {
    $value = env_any($keys);
    if ($value === null) {
        http_response_code(500);
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode([
            "success" => false,
            "message" => "Server misconfigured: none of these environment variables are set: " . implode(', ', $keys)
        ]);
        exit;
    }
    return $value;
}

$DB_HOST = required_env_any(['DB_HOST']);
$DB_PORT = (int) required_env_any(['DB_PORT']);
$DB_NAME = required_env_any(['DB_NAME', 'DB_DATABASE']);
$DB_USER = required_env_any(['DB_USER', 'DB_USERNAME']);
$DB_PASS = required_env_any(['DB_PASS', 'DB_PASSWORD']);


$pdo = null;
$conn = null;

try {
    $pdo_options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_SSL_CA       => true,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];
    $pdo = new PDO(
        "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        $pdo_options
    );

    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = mysqli_init();
    $conn->ssl_set(NULL, NULL, NULL, NULL, NULL);

    if (!$conn->real_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT)) {
        throw new Exception("MySQLi Connect Error: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");

} catch (Throwable $e) {
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code(500);
    error_log("DB connection failure: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed",
    ]);
    exit;
}