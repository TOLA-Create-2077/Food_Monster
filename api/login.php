<?php
/**
 * login.php
 * ផ្ទៀងផ្ទាត់គណនីប្រើប្រាស់សម្រាប់ Mobile App និង Web
 *
 * FIXED:
 *  - The Android app sends the password as "user_password" (see LoginRequest
 *    in ApiService.kt), but this endpoint was reading "password" — every
 *    login from the app would fail with an empty password. Now reads
 *    "user_password" (still accepts legacy "password" for other callers).
 *  - Issues a real, DB-backed session token instead of an unstored mock string.
 *  - Rejects login for non-ACTIVE accounts.
 *  - Login accepts phone only, matching what the app actually sends and what
 *    UserDataResponse expects back (id, name, phone — no email).
 *  - Generic error message on failure (avoids revealing whether the phone
 *    exists vs the password was wrong).
 */

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_helper.php';

if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) {
    $data = [];
}

$phone    = isset($data['phone']) ? trim((string)$data['phone']) : '';
$password = $data['user_password'] ?? $data['password'] ?? '';
$password = (string)$password;

if ($phone === '' || $password === '') {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "សូមបញ្ចូលលេខទូរស័ព្ទ និងលេខកូដសម្ងាត់"]);
    exit();
}

$stmt = $conn->prepare("SELECT id, name, phone, password, status FROM users WHERE phone = ? LIMIT 1");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Internal server error"]);
    exit();
}

$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

$genericError = "លេខទូរស័ព្ទ ឬ លេខកូដសម្ងាត់មិនត្រឹមត្រូវ";

if (!$row || !password_verify($password, $row['password'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => $genericError]);
    exit();
}

if ($row['status'] !== 'ACTIVE') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "គណនីនេះត្រូវបានផ្អាក សូមទាក់ទងផ្នែកគាំទ្រ"]);
    exit();
}

$token = issue_auth_token($conn, (int)$row['id']);

echo json_encode([
    "success" => true,
    "message" => "Success",
    "token" => $token,
    "user" => [
        "id" => (int)$row['id'],
        "name" => $row['name'],
        "phone" => $row['phone'],
    ]
]);

$conn->close();