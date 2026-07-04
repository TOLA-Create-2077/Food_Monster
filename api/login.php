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

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$phone = isset($data['phone']) ? trim($data['phone']) : '';
$password = isset($data['user_password']) ? $data['user_password'] : '';

if (empty($phone) || empty($password)) {
    // DEBUG: remove this "debug" key once things work — shows exactly what arrived
    echo json_encode([
        "success" => false,
        "message" => "សូមបញ្ចូលលេខទូរស័ព្ទ និងលេខកូដសម្ងាត់",
        "debug" => [
            "raw_body_received" => $raw,
            "json_decode_error" => json_last_error_msg(),
            "parsed_phone" => $phone,
            "parsed_password_present" => !empty($password)
        ]
    ]);
    exit();
}

$stmt = $conn->prepare(
    "SELECT id, name, email, phone, address, profile, password
     FROM users
     WHERE phone = ? AND type = 'user' AND deleted_at IS NULL
     LIMIT 1"
);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL Statement preparation failure"]);
    exit();
}

$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (!empty($row['password']) && password_verify($password, $row['password'])) {
        echo json_encode([
            "success" => true,
            "message" => "Success",
            "token" => "MOCK_SESSION_TOKEN_" . bin2hex(random_bytes(16)),
            "user" => [
                "id" => (int)$row['id'],
                "name" => $row['name'],
                "phone" => $row['phone']
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "លេខកូដសម្ងាត់មិនត្រឹមត្រូវទេ"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "មិនមានគណនីនេះទេ"]);
}

$stmt->close();
$conn->close();