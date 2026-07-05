<?php
/**
 * login.php
 * Verifies active credentials using native password hashing and returns token profiles.
 */
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_helper.php';

$data = json_decode(file_get_contents("php://input"), true);

$phone = isset($data['phone']) ? trim($data['phone']) : '';
$password = isset($data['user_password']) ? $data['user_password'] : '';

if (empty($phone) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "សូមបញ្ចូលលេខទូរស័ព្ទ និងលេខកូដសម្ងាត់"
    ]);
    exit();
}

$stmt = $conn->prepare("SELECT id, name, email, phone, password FROM users WHERE phone = ? AND type = 'user' AND status = 'ACTIVE' LIMIT 1");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "SQL Execution Engine processing failure."]);
    exit();
}

$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (!empty($row['password']) && password_verify($password, $row['password'])) {
        
        $sessionToken = issue_auth_token($conn, $row['id']);
        
        echo json_encode([
            "success" => true,
            "message" => "Success",
            "data" => [
                "token" => $sessionToken,
                "user" => [
                    "id" => (int)$row['id'],
                    "name" => $row['name'],
                    "phone" => $row['phone'],
                    "email" => $row['email'] ?? ""
                ]
            ]
        ]);
        $stmt->close();
        exit();
    }
}

http_response_code(401);
echo json_encode([
    "success" => false,
    "message" => "លេខទូរស័ព្ទ ឬ លេខកូដសម្ងាត់មិនត្រឹមត្រូវទេ"
]);
$stmt->close();