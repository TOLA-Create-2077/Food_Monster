<?php
/**
 * config.php
 * Central database connection for Aiven Cloud (guarantees both $pdo and $conn exist)
 *
 * FIXED:
 *  - .env used DB_HOST / DB_PORT / DB_NAME / DB_USER / DB_PASS,
 *    but this file was reading DB_HOST / DB_PORT / DB_DATABASE / DB_USERNAME / DB_PASSWORD.
 *    Because those names never matched, the .env values were NEVER actually used —
 *    the connection silently fell back to a password hardcoded in this file.
 *    That hardcoded fallback is why the password ended up in a public repo.
 *  - Removed all hardcoded credential fallbacks. If required env vars are missing,
 *    the app now fails loudly instead of silently using a baked-in secret.
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
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

// Load environment variables (.env is expected to sit outside web root in production)
load_env_direct(__DIR__ . '/.env');
load_env_direct(__DIR__ . '/../.env');

function required_env(string $key): string {
    $value = getenv($key);
    if ($value === false || $value === '') {
        $value = $_ENV[$key] ?? null;
    }
    if ($value === null || $value === '') {
        http_response_code(500);
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode([
            "success" => false,
            "message" => "Server misconfigured: missing required environment variable {$key}"
        ]);
        exit;
    }
    return (string)$value;
}

// Names now match the .env file exactly: DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
$DB_HOST = required_env('DB_HOST');
$DB_PORT = (int) required_env('DB_PORT');
$DB_NAME = required_env('DB_NAME');
$DB_USER = required_env('DB_USER');
$DB_PASS = required_env('DB_PASS');

// Initialize both as null first so they're never "undefined"
$pdo = null;
$conn = null;

try {
    // 1. PDO connection
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

    // 2. MySQLi connection
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
    // Don't leak internal error detail in production; log it server-side instead.
    error_log("DB connection failure: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed",
    ]);
    exit;
}
