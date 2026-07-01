<?php
/**
 * config.php
 *
 * Central database connection (mysqli), credentials loaded from environment
 * variables — NEVER hardcode credentials in source files.
 *
 * Local dev: create a `.env` file next to this one (see .env.example) and
 * make sure something loads it (see load_env() below, or use
 * vlucas/phpdotenv if you prefer a Composer dependency).
 *
 * Production (recommended): set these as real environment variables on the
 * server / hosting panel instead of shipping a .env file at all.
 *
 * This file must NEVER be committed with real values inside it, and the
 * .env file must be listed in .gitignore.
 */

// --- Minimal .env loader (skip if you already load env vars another way) ---
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

// --- Read credentials from environment (never from a literal string here) ---
$DB_HOST = getenv('DB_HOST') ?: '';
$DB_PORT = (int)(getenv('DB_PORT') ?: 3306);
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

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);
    exit;
}
