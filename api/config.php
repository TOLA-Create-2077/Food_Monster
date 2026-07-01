<?php
/**
 * config.php
 *
 * Central database connection (Supports both PDO and mysqli with silent fallback)
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

// ចាប់យកតម្លៃពី Environment (បើគ្មានទេ ឱ្យយកតម្លៃ Aiven Cloud ភ្លាម)
$DB_HOST = getenv('DB_HOST')     ?: ($_ENV['DB_HOST']     ?? 'foodmonster-foodmonster2077.l.aivencloud.com');
$DB_PORT = (int)(getenv('DB_PORT') ?: ($_ENV['DB_PORT']     ?? 27243));
$DB_NAME = getenv('DB_DATABASE') ?: ($_ENV['DB_DATABASE'] ?? 'little_duckling_db');
$DB_USER = getenv('DB_USERNAME') ?: ($_ENV['DB_USERNAME'] ?? 'avnadmin');
$DB_PASS = getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD'] ?? 'AVNS_zm11DvJhdhSKo24pyuy');

// បង្ខំប្រើតម្លៃ Aiven បើនៅលើ Railway
if (isset($_SERVER['HTTP_HOST']) && (str_contains($_SERVER['HTTP_HOST'], 'railway.app') || str_contains($_SERVER['HTTP_HOST'], 'up.railway.app'))) {
    $DB_HOST = 'foodmonster-foodmonster2077.l.aivencloud.com';
    $DB_PORT = 27243;
    $DB_NAME = 'little_duckling_db';
    $DB_USER = 'avnadmin';
    $DB_PASS = 'AVNS_zm11DvJhdhSKo24pyuy';
}

// --- 🛠️ ដំណោះស្រាយការពារ៖ បង្កើតការតភ្ជាប់ដោយមានលក្ខខណ្ឌច្បាស់លាស់ដើម្បីការពារកុំឱ្យចេញ Error 500 ---
$pdo = null;
$conn = null;

try {
    // ១) បង្កើត PDO Connection (ប្រើសម្រាប់ register.php)
    if (class_exists('PDO')) {
        $pdo = new PDO("mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    // មិនបោះ Error 500 ទេ ទុកឱ្យ mysqli សាកល្បងម្តង
}

try {
    // ២) បង្កើត mysqli Connection (ប្រើសម្រាប់ get_foods.php និងកូដចាស់ៗរបស់បង)
    if (class_exists('mysqli')) {
        @mysqli_report(MYSQLI_REPORT_OFF); // បិទការ Report ធ្ងន់ធ្ងរដែលនាំឱ្យគាំង Server 500
        $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
        if (!$conn->connect_error) {
            $conn->set_charset("utf8mb4");
        }
    }
} catch (Exception $e) {
    // សម្ងំស្ងាត់
}

// 🛠️ ប្រសិនបើការតភ្ជាប់ទាំងពីរប្រភេទបរាជ័យទាំងព្រម ទើបបង្ហាញសារ JSON ប្រាប់ច្បាស់ៗ
if (!$pdo && (!$conn || $conn->connect_error)) {
    http_response_code(200); // 🚀 ប្តូរទៅជា Code 200 ដើម្បីកុំឱ្យស្ទះទូរស័ព្ទដៃ Android
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        "success" => false,
        "message" => "Database Connection Failed. Please check Server Extensions or Credentials."
    ]);
    exit;
}