<?php
/**
 * login.php
 * Authenticates user credentials securely.
 */

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Dynamically reference global Aiven Cloud configurations from config.php
require_once __DIR__ . '/config.php';

if (!isset($conn) || $conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$phone = isset($data['phone']) ? trim($data['phone']) : '';

// 🛠️ FIXED: Reads 'password' to exactly match Android's property naming convention
$password = isset($data['password']) ? $data['password'] : '';

if (empty($phone) || empty($password)) {
    echo json_encode(["success" => false, "message" => "សូមបញ្ចូលលេខទូរស័ព្ទ និងលេខកូដសម្ងាត់"]);
    exit();
}

// Fetch user data securely
$stmt = $conn->prepare("SELECT id, name, phone, password FROM users WHERE phone = ? LIMIT 1");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL Statement preparation failure"]);
    exit();
}

$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Validate password hashes
    if (password_verify($password, $row['password'])) {
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
    echo json_encode(["success" => false, "message" => "មិនមានគណនីលេខទូរស័ព្ទនេះទេ"]);
}

$stmt->close();
$conn->close();
?>