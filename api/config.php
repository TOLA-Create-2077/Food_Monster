<?php
/**
 * config.php
 *
 * Central database connection (Supports both PDO and mysqli)
 */

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
                if ($key !== '' && getenv($key) === false) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                }
            }
        }
    }
}

// អាន .env សម្រាប់ម៉ាស៊ីន Local Host 
load_env_fallback(__DIR__ . '/../.env');

// ចាប់យកតម្លៃពី Environment
$DB_HOST = getenv('DB_HOST')     ?: ($_ENV['DB_HOST']     ?? '');
$DB_PORT = (int)(getenv('DB_PORT') ?: ($_ENV['DB_PORT']     ?? 3306));
$DB_NAME = getenv('DB_DATABASE') ?: ($_ENV['DB_DATABASE'] ?? '');
$DB_USER = getenv('DB_USERNAME') ?: ($_ENV['DB_USERNAME'] ?? '');
$DB_PASS = getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD'] ?? '');

// 🛠️ ដំណោះស្រាយពិសេស៖ បើដំឡើងនៅលើ Railway ហើយតម្លៃខាងលើនៅតែទទេ ឱ្យបង្ខំទាញតម្លៃ Aiven មកប្រើផ្ទាល់តែម្តង
if (empty($DB_HOST) || $DB_HOST === '127.0.0.1' || $DB_HOST === 'localhost') {
    // ឆែកមើលថាតើកំពុងរត់នៅលើ Server Railway មែនឬទេ
    if (isset($_SERVER['HTTP_HOST']) && (str_contains($_SERVER['HTTP_HOST'], 'railway.app') || str_contains($_SERVER['HTTP_HOST'], 'up.railway.app'))) {
        $DB_HOST = 'foodmonster-foodmonster2077.l.aivencloud.com';
        $DB_PORT = 27243;
        $DB_NAME = 'little_duckling_db';
        $DB_USER = 'avnadmin';
        $DB_PASS = 'AVNS_zm11DvJhdhSKo24pyuy';
    }
}

// ប្រសិនបើនៅតែខ្វះព័ត៌មានតភ្ជាប់ ទើបបោះ Error
if ($DB_HOST === '' || $DB_USER === '' || $DB_NAME === '') {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        "success" => false,
        "message" => "Server misconfigured: database environment variables are not set properly."
    ]);
    exit;
}

// --- បង្កើតការតភ្ជាប់ Database (PDO & mysqli) ---
try {
    // ១) សម្រាប់កូដទម្រង់ PDO ថ្មីៗ (যেমন: register.php)
    $pdo = new PDO("mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // ២) សម្រាប់កូដទម្រង់ mysqli ចាស់ៗរបស់បង
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