<?php
/**
 * register.php
 * Production Registration API Service endpoint mapped directly to little_duckling_db.
 */
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/config.php';

// Parse raw JSON inputs directly from the mobile client stream
$data = json_decode(file_get_contents("php://input"), true);

$name = isset($data['name']) ? trim($data['name']) : '';
$phone = isset($data['phone']) ? trim($data['phone']) : '';
$password = isset($data['user_password']) ? $data['user_password'] : '';

// 1. Mandatory Input Fields Presence Sanitization
if (empty($name) || empty($phone) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "message" => "សូមបំពេញព័ត៌មានឱ្យបានគ្រប់គ្រាន់"
    ]);
    exit();
}

// 2. Password Complexity/Length Constraint Enforcement
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "message" => "លេខកូដសម្ងាត់ត្រូវតែមានយ៉ាងហោចណាស់ ៦ ខ្ទង់"
    ]);
    exit();
}

// 3. Duplicate Record Checking using Type-Safe Bound Parameters
$checkStmt = $conn->prepare("SELECT id FROM users WHERE phone = ? LIMIT 1");
if (!$checkStmt) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Internal processing initialization fault."]);
    exit();
}

$checkStmt->bind_param("s", $phone);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    http_response_code(409); // Conflict HTTP status code
    echo json_encode([
        "success" => false, 
        "message" => "លេខទូរស័ព្ទនេះមានគណនីរួចហើយ"
    ]);
    $checkStmt->close();
    exit();
}
$checkStmt->close();

// 4. Secure Non-Reversible Password Cryptographic Hashing
// Using PASSWORD_BCRYPT with an optimal processing cost of 12
$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// 5. Database Profile Persistence Execution Block
// Populates schema defaults matching your 'little_duckling_db' dump precisely
$stmt = $conn->prepare("INSERT INTO users (name, phone, password, status, type, created_at, updated_at) VALUES (?, ?, ?, 'ACTIVE', 'user', NOW(), NOW())");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to compile compilation transaction architecture."]);
    exit();
}

$stmt->bind_param("sss", $name, $phone, $hashedPassword);

if ($stmt->execute()) {
    $newUserId = $conn->insert_id;
    
    http_response_code(201); // 201 Created Status code standard
    echo json_encode([
        "success" => true,
        "message" => "ចុះឈ្មោះជោគជ័យ",
        "user" => [
            "id" => (int)$newUserId,
            "name" => $name,
            "phone" => $phone
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "គណនីមិនអាចបង្កើតបានទេនៅពេលនេះ"
    ]);
}

$stmt->close();