<?php
/**
 * config.php
 *
 * Central database connection (Supports both PDO and mysqli), credentials loaded from environment
 * variables — NEVER hardcode credentials in source files.
 */

// --- 🛠️ ១. មុខងារទាញយកតម្លៃពី .env របស់ Laravel (នៅក្រៅ folder api) សម្រាប់ Localhost ---
if (!function_exists('load_env_fallback')) {
    function load_env_fallback(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                if ($key !== '' && getenv($key) === false && (!isset($_ENV[$key]) || $_ENV[$key] === '')) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                }
            }
        }
    }
}

// ឱ្យវាទៅអានហ្វាយល៍ .env របស់ Laravel ដែលនៅខាងក្រៅបង្អស់
load_env_fallback(__DIR__ . '/../.env');

// --- 🛠️ ២. អានតម្លៃ Credentials (គាំទ្រទាំងស្តង់ដារ Laravel និង Railway Variables) ---
$DB_HOST = getenv('DB_HOST')     ?: ($_ENV['DB_HOST']     ?? 'foodmonster-foodmonster2077.l.aivencloud.com');
$DB_PORT = (int)(getenv('DB_PORT') ?: ($_ENV['DB_PORT']     ?? 27243));
$DB_NAME = getenv('DB_DATABASE') ?: ($_ENV['DB_DATABASE'] ?? 'little_duckling_db');
$DB_USER = getenv('DB_USERNAME') ?: ($_ENV['DB_USERNAME'] ?? 'avnadmin');
$DB_PASS = getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD'] ?? 'AVNS_zm11DvJhdhSKo24pyuy');

// ករណីការពារ៖ បើនៅតែទទេ ឱ្យយកតម្លៃ Backup របស់ Aiven Cloud តែម្តង
if (empty($DB_HOST) || $DB_HOST === '127.0.0.1' || $DB_HOST === 'localhost') {
    if (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'railway.app')) {
        $DB_HOST = 'foodmonster-foodmonster2077.l.aivencloud.com';
        $DB_PORT = 27243;
        $DB_NAME = 'little_duckling_db';
        $DB_USER = 'avnadmin';
        $DB_PASS = 'AVNS_zm11DvJhdhSKo24pyuy';
    }
}

if ($DB_HOST === '' || $DB_USER === '' || $DB_NAME === '') {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        "success" => false,
        "message" => "Server misconfigured: database environment variables are not set properly."
    ]);
    exit;
}

// --- 🛠️ ៣. បង្កើតការតភ្ជាប់ Database (គាំទ្រទាំង $pdo និង $conn របស់ mysqli) ---
try {
    // ក) បង្កើត PDO Connection (សម្រាប់ register.php និងកូដថ្មីៗ)
    $pdo = new PDO("mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // ខ) បង្កើត mysqli Connection (អថេរ $conn ចាស់របស់បង សម្រាប់ការពារហ្វាយល៍ដទៃកុំឱ្យគាំង)
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed: " . $e->getMessage()
    ]);
    exit;
}