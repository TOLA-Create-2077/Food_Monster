<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

require_once __DIR__ . '/config.php';

if (!isset($conn) || $conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$name = isset($data['name']) ? trim($data['name']) : '';
$phone = isset($data['phone']) ? trim($data['phone']) : '';
$password = isset($data['user_password']) ? $data['user_password'] : '';

if (empty($name) || empty($phone) || empty($password)) {
    echo json_encode(["success" => false, "message" => "សូមបញ្ចូលព័ត៌មានទាំងអស់ឱ្យបានគ្រប់គ្រាន់"]);
    exit();
}

if (strlen($password) < 6) {
    echo json_encode(["success" => false, "message" => "លេខកូដសម្ងាត់ត្រូវមានយ៉ាងតិច ៦ តួអក្សរ"]);
    exit();
}

$checkStmt = $conn->prepare(
    "SELECT id FROM users WHERE phone = ? AND deleted_at IS NULL LIMIT 1"
);
if (!$checkStmt) {
    echo json_encode(["success" => false, "message" => "SQL Statement preparation failure"]);
    exit();
}
$checkStmt->bind_param("s", $phone);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->fetch_assoc()) {
    echo json_encode(["success" => false, "message" => "លេខទូរស័ព្ទនេះមានគណនីរួចហើយ"]);
    $checkStmt->close();
    $conn->close();
    exit();
}
$checkStmt->close();

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$insertStmt = $conn->prepare(
    "INSERT INTO users (name, phone, password, type, status, default_language, created_at, updated_at)
     VALUES (?, ?, ?, 'user', 'ACTIVE', 'en', NOW(), NOW())"
);
if (!$insertStmt) {
    echo json_encode(["success" => false, "message" => "SQL Statement preparation failure"]);
    exit();
}
$insertStmt->bind_param("sss", $name, $phone, $hashedPassword);

if ($insertStmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "ចុះឈ្មោះជោគជ័យ",
        "user" => [
            "id" => $insertStmt->insert_id,
            "name" => $name,
            "phone" => $phone
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "ការចុះឈ្មោះមិនជោគជ័យទេ"]);
}

$insertStmt->close();
$conn->close();