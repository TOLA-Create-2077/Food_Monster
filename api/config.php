<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

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
                $value = trim($value, " \t\\n\\r\\0\\x0B\"'");
                if (getenv($key) === false) putenv("$key=$value");
                if (!isset($_ENV[$key])) $_ENV[$key] = $value;
            }
        }
    }
}

load_env_direct(__DIR__ . '/.env');
load_env_direct(__DIR__ . '/../.env');

function env_any(array $keys): ?string {
    foreach ($keys as $key) {
        $value = getenv($key);
        if ($value === false || $value === '') $value = $_ENV[$key] ?? null;
        if ($value !== null && $value !== '') return (string)$value;
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
            "message" => "Server configuration error: Missing connection keys."
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

mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_init();

// For secure Aiven installations, ensure TLS validation flags match host configuration
if (!$conn->real_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT, NULL, MYSQLI_CLIENT_SSL)) {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        "success" => false,
        "message" => "Critical error: Database connectivity could not be established."
    ]);
    exit;
}
$conn->set_charset("utf8mb4");