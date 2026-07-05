<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Credentials now come from config.php (env vars), not hardcoded here.
require_once __DIR__ . '/config.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    $name = isset($data['name']) ? trim($data['name']) : '';
    $phone = isset($data['phone']) ? trim($data['phone']) : '';
    $password = isset($data['user_password']) ? $data['user_password'] : ''; // Reads from Android app input

    if (empty($name) || empty($phone) || empty($password)) {
        echo json_encode(["success" => false, "message" => "សូមបំពេញព័ត៌មានឱ្យបានគ្រប់គ្រាន់"]);
        exit();
    }

    // Check if duplicate user account exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE phone = ? LIMIT 1");
    $checkStmt->bind_param("s", $phone);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "លេខទូរស័ព្ទនេះមានគណនីរួចហើយ"]);
        $checkStmt->close();
        $conn->close();
        exit();
    }
    $checkStmt->close();

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (name, phone, password, status, role, type) VALUES (?, ?, ?, 'ACTIVE', 'user', 'user')");
    $stmt->bind_param("sss", $name, $phone, $hashedPassword);

    if ($stmt->execute()) {
        $newUserId = $conn->insert_id;
        echo json_encode([
            "success" => true,
            "message" => "ចុះឈ្មោះជោគជ័យ",
            "user" => [
                "id" => $newUserId,
                "name" => $name,
                "phone" => $phone
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "មានបញ្ហាបច្ចេកទេសក្នុងការរក្សាទុកទិន្នន័យ"]);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database processing error."
    ]);
}
