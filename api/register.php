<?php
/**
 * register.php
 * Onboards unique user phone registrations with optimized password security profiles.
 */
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/config.php';

$data = json_decode(file_get_contents("php://input"), true);

$name = isset($data['name']) ? trim($data['name']) : '';
$phone = isset($data['phone']) ? trim($data['phone']) : '';
$password = isset($data['user_password']) ? $data['user_password'] : '';

if (empty($name) || empty($phone) || empty($password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "សូមបំពេញព័ត៌មានឱ្យបានគ្រប់គ្រាន់"]);
    exit();
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "លេខកូដសម្ងាត់ត្រូវតែមានយ៉ាងហោចណាស់ ៦ ខ្ទង់"]);
    exit();
}

$checkStmt = $conn->prepare("SELECT id FROM users WHERE phone = ? LIMIT 1");
$checkStmt->bind_param("s", $phone);
$checkStmt->execute();
if ($checkStmt->get_result()->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["success" => false, "message" => "លេខទូរស័ព្ទនេះមានគណនីរួចហើយ"]);
    $checkStmt->close();
    exit();
}
$checkStmt->close();

$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

$stmt = $conn->prepare("INSERT INTO users (name, phone, password, status, type, created_at, updated_at) VALUES (?, ?, ?, 'ACTIVE', 'user', NOW(), NOW())");
$stmt->bind_param("sss", $name, $phone, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "ចុះឈ្មោះជោគជ័យ",
        "data" => [
            "id" => $conn->insert_id,
            "name" => $name,
            "phone" => $phone
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "គណនីមិនអាចបង្កើតបានទេនៅពេលនេះ"]);
}
$stmt->close();