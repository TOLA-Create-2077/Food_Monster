<?php
/**
 * config.php
 * Central database connection for Aiven Cloud (Supports both PDO and mysqli with SSL)
 */

ini_set('display_errors', '0'); 
error_reporting(E_ALL);

if (!function_exists('load_env_direct')) {
    function load_env_direct($path) {
        if (!file_exists($path)) return;
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') continue;
            
            if (strpos($line, '=') !== false) {
                $parts = explode('=', $line, 2);
                $key = trim($parts[0]);
                $value = trim($parts[1], " \t\n\r\0\x0B\"'");
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

// Load environment variables
load_env_direct(__DIR__ . '/.env');
load_env_direct(__DIR__ . '/../.env');

// 1. Set Aiven Cloud as the default database connection parameters
$DB_HOST = getenv('DB_HOST')     ?: ($_ENV['DB_HOST']     ?? 'foodmonster-foodmonster2077.l.aivencloud.com');
$DB_PORT = (int)(getenv('DB_PORT') ?: ($_ENV['DB_PORT']    ?? 27243));
$DB_NAME = getenv('DB_DATABASE') ?: ($_ENV['DB_DATABASE'] ?? 'little_duckling_db');
$DB_USER = getenv('DB_USERNAME') ?: ($_ENV['DB_USERNAME'] ?? 'avnadmin');
$DB_PASS = getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD'] ?? 'AVNS_zm11DvJhdhSKo24pyuy');

$pdo = null;
$conn = null;

try {
    // 2. PDO Connection Setup with SSL verification disabled (required for quick cloud setup)
    $pdo_options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_SSL_CA       => true, // Tells PDO to initialize SSL connection
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false // Keeps local development connection smooth
    ];
    
    $pdo = new PDO("mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, $pdo_options);
    
    // 3. MySQLi Connection Setup with SSL
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = mysqli_init();
    
    // Initialize SSL flag before real connect execution
    $conn->ssl_set(NULL, NULL, NULL, NULL, NULL); 
    
    if (!$conn->real_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT)) {
        throw new Exception($conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database Connection Failed",
        "error" => $e->getMessage()
    ]);
    exit;
}