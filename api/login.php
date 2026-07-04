<?php
/**
 * login.php
 * ផ្ទៀងផ្ទាត់គណនីប្រើប្រាស់សម្រាប់ Mobile App និង Web
 */

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ភ្ជាប់ទៅកាន់ការកំណត់ Database របស់ Aiven Cloud
require_once __DIR__ . '/config.php';

if (!isset($conn) || $conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

// 🛠️ ដំណោះស្រាយ៖ គាំទ្រទាំងការប្រើប្រាស់ លេខទូរស័ព្ទ (Phone) ឬ អុីម៉ែល (Email)
$phone = isset($data['phone']) ? trim($data['phone']) : '';
$password = isset($data['password']) ? $data['password'] : '';

if (empty($phone) || empty($password)) {
    echo json_encode(["success" => false, "message" => "សូមបញ្ចូលលេខទូរស័ព្ទ/អុីម៉ែល និងលេខកូដសម្ងាត់"]);
    exit();
}

// ទាញយកទិន្នន័យអ្នកប្រើប្រាស់មកផ្ទៀងផ្ទាត់ (គាំទ្រទាំងការឆែកតាម phone នៅក្នុង DB)
$stmt = $conn->prepare("SELECT id, name, phone, password FROM users WHERE phone = ? LIMIT 1");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL Statement preparation failure"]);
    exit();
}

$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // ផ្ទៀងផ្ទាត់លេខកូដសម្ងាត់ Hash ជាមួយ Password ធម្មតា
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
    echo json_encode(["success" => false, "message" => "មិនមានគណនីនេះទេ"]);
}

$stmt->close();
$conn->close();
?>