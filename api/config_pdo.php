<?php
/**
 * config_pdo.php
 *
 * PDO variant of config.php, optimized for both local Laragon development 
 * and production live environments like Railway.
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
            
            // Only load from file if the variable doesn't already exist in system env
            if ($key !== '' && getenv($key) === false && $_ENV[$key] === null && $_SERVER[$key] === null) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

// Safely attempt local fallback loading
load_env(__DIR__ . '/.env');

// Railway injects these automatically; we fall back to getenv or $_ENV array map structures
$DB_HOST = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?: ($_SERVER['DB_HOST'] ?: ''));
$DB_PORT = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?: ($_SERVER['DB_PORT'] ?: '3306'));
$DB_USER = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?: ($_SERVER['DB_USER'] ?: ''));
$DB_PASS = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?: ($_SERVER['DB_PASS'] ?: ''));
$DB_NAME = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?: ($_SERVER['DB_NAME'] ?: ''));

if ($DB_HOST === '' || $DB_USER === '' || $DB_NAME === '') {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        "success" => false,
        "message" => "Server misconfigured: Environment database variables missing. Host: '$DB_HOST', User: '$DB_USER', DB: '$DB_NAME'"
    ]);
    exit;
}

try {
    $conn = new PDO(
        "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS
    );
    // Enable exceptions so we catch exact column mismatches immediately
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        "success" => false, 
        "message" => "Database connection failed: " . $exception->getMessage()
    ]);
    exit;
}