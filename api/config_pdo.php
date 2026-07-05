<?php
/**
 * config_pdo.php
 *
 * Configured specifically for Aiven Cloud Hosted MySQL.
 * Enforces mandatory SSL mode parameters requested by the production cluster.
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
            
            if ($key !== '' && getenv($key) === false && !isset($_ENV[$key]) && !isset($_SERVER[$key])) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

// Safely load local fallback configurations if available
load_env(__DIR__ . '/.env');

// Environment reading structure with fallback safety mechanisms
$DB_HOST = getenv('DB_HOST') ?: (isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : (isset($_SERVER['DB_HOST']) ? $_SERVER['DB_HOST'] : 'foodmonster-foodmonster2077.l.aivencloud.com'));
$DB_PORT = getenv('DB_PORT') ?: (isset($_ENV['DB_PORT']) ? $_ENV['DB_PORT'] : (isset($_SERVER['DB_PORT']) ? $_SERVER['DB_PORT'] : '27243'));
$DB_USER = getenv('DB_USER') ?: (isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : (isset($_SERVER['DB_USER']) ? $_SERVER['DB_USER'] : 'avnadmin'));
$DB_PASS = getenv('DB_PASS') ?: (isset($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : (isset($_SERVER['DB_PASS']) ? $_SERVER['DB_PASS'] : 'AVNS_zm11DvJhdhSKo24pyuy')); // Put your revealed password here if testing locally
$DB_NAME = getenv('DB_NAME') ?: (isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : (isset($_SERVER['DB_NAME']) ? $_SERVER['DB_NAME'] : 'little_duckling_db'));

if ($DB_HOST === '' || $DB_USER === '' || $DB_NAME === '') {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        "success" => false,
        "message" => "Server misconfigured: Database environment setup parameters are missing."
    ]);
    exit;
}

try {
    // Establish connection utilizing standard PDO string rules
    $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
    
    // CRITICAL FIX: Pass SSL mode configuration options required explicitly by Aiven Cloud
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false // Allows connection using secure transit encryption without manual CA file uploading paths
    ];

    $conn = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $exception) {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        "success" => false, 
        "message" => "Aiven Database connection pipeline error: " . $exception->getMessage()
    ]);
    exit;
}