<?php
/**
 * register.php
 * បង្កើតគណនីថ្មីសម្រាប់ Mobile App និង Web
 *
 * FIXED to match the real RegisterRequest sent by the app: fields are
 * name, phone, user_password (no email — the app doesn't collect one).
 *
 *  - Password hashed with password_hash() (bcrypt), same format already used
 *    for every existing row in `users`.
 *  - Rejects duplicate phone.
 *  - New accounts default to role "user" / status "ACTIVE".
 *  - Issues a real session token and returns the same shape as login.php so
 *    the app can log the user straight in after registering.
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

$name     = isset($data['name']) ? trim((string)$data['name']) : '';
$phone    = isset($data['phone']) ? trim((string)$data['phone']) : '';
$password = $data['user_password'] ?? $data['password'] ?? '';
$password = (string)$password;

if ($name === '' || $phone === '' || $password === '') {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "សូមបំពេញឈ្មោះ លេខទូរស័ព្ទ និងលេខកូដសម្ងាត់"]);
    exit();
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "លេខកូដសម្ងាត់ត្រូវមានយ៉ាងតិច 6 តួអក្សរ"]);
    exit();
}

$checkStmt = $conn->prepare("SELECT id FROM users WHERE phone = ? LIMIT 1");
$checkStmt->bind_param("s", $phone);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
if ($checkResult->fetch_assoc()) {
    $checkStmt->close();
    http_response_code(409);
    echo json_encode(["success" => false, "message" => "លេខទូរស័ព្ទនេះមានគណនីរួចហើយ"]);
    exit();
}
$checkStmt->close();

$passwordHash = password_hash($password, PASSWORD_BCRYPT);

$insertStmt = $conn->prepare(
    "INSERT INTO users (name, phone, password, role, type, status, created_at, updated_at)
     VALUES (?, ?, ?, 'user', NULL, 'ACTIVE', NOW(), NOW())"
);
$insertStmt->bind_param("sss", $name, $phone, $passwordHash);

if (!$insertStmt->execute()) {
    $insertStmt->close();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "ការចុះឈ្មោះមិនជោគជ័យទេ សូមព្យាយាមម្តងទៀត"]);
    exit();
}

$newUserId = $insertStmt->insert_id;
$insertStmt->close();

$token = issue_auth_token($conn, (int)$newUserId);

echo json_encode([
    "success" => true,
    "message" => "Success",
    "token" => $token,
    "user" => [
        "id" => (int)$newUserId,
        "name" => $name,
        "phone" => $phone,
    ]
]);

$conn->close();