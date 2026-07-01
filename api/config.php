<?php
/**
 * config.php
 *
 * Central database connection (Supports both PDO and mysqli)
 */

// បើកការបង្ហាញ Error សម្រាប់ការដោះស្រាយលើ Server
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (!function_exists('load_env_direct')) {
    function load_env_direct($path) {
        if (!file_exists($path)) {
            return;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                $parts = explode('=', $line, 2);
                $key = isset($parts[0]) ? trim($parts[0]) : '';
                $value = isset($parts[1]) ? trim($parts[1]) : '';
                $value = trim($value, " \t\n\r\0\x0B\"'");
                if ($key !== '') {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                }
            }
        }
    }
}

// អាន .env នៅក្នុង folder api ផ្ទាល់
load_env_direct(__DIR__ . '/.env');
// អាន .env របស់ Laravel ទុកជាប្រភពបម្រុង
load_env_direct(__DIR__ . '/../.env');

// ទាញយកតម្លៃអថេរ Database 
$DB_HOST = getenv('DB_HOST')     ?: ($_ENV['DB_HOST']     ?? 'foodmonster-foodmonster2077.l.aivencloud.com');
$DB_PORT = (int)(getenv('DB_PORT') ?: ($_ENV['DB_PORT']     ?? 27243));
$DB_NAME = getenv('DB_DATABASE') ?: ($_ENV['DB_DATABASE'] ?? 'little_duckling_db');
$DB_USER = getenv('DB_USERNAME') ?: ($_ENV['DB_USERNAME'] ?? 'avnadmin');
$DB_PASS = getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD'] ?? 'AVNS_zm11DvJhdhSKo24pyuy');

// បង្ខំប្រើតម្លៃ Aiven Cloud ភ្លាម បើនៅលើ Server Railway
if (isset($_SERVER['HTTP_HOST']) && (str_contains($_SERVER['HTTP_HOST'], 'railway.app') || str_contains($_SERVER['HTTP_HOST'], 'up.railway.app'))) {
    $DB_HOST = 'foodmonster-foodmonster2077.l.aivencloud.com';
    $DB_PORT = 27243;
    $DB_NAME = 'little_duckling_db';
    $DB_USER = 'avnadmin';
    $DB_PASS = 'AVNS_zm11DvJhdhSKo24pyuy';
}

$pdo = null;
$conn = null;

try {
    // បង្កើត PDO Connection
    $pdo = new PDO("mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
    
    // បង្កើត mysqli Connection
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        "success" => false,
        "message" => "Database Connection Error: " . $e->getMessage()
    ]);
    exit;
}