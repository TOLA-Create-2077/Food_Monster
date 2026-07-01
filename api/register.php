<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Credentials now come from config.php (env vars), not hardcoded here.
require_once __DIR__ . '/config.php';

try {
    // ១. អានទិន្នន័យពី JSON Raw Body
    $raw_input = file_get_contents("php://input");
    $data = json_decode($raw_input, true);

    // ២. ទាញយកតម្លៃ (គាំទ្រទាំង JSON និង Form $_POST)
    $name = isset($data['name']) ? trim($data['name']) : (isset($_POST['name']) ? trim($_POST['name']) : '');
    $phone = isset($data['phone']) ? trim($data['phone']) : (isset($_POST['phone']) ? trim($_POST['phone']) : '');

    $password = '';
    if (isset($data['user_password'])) { $password = $data['user_password']; }
    elseif (isset($data['password'])) { $password = $data['password']; }
    elseif (isset($_POST['user_password'])) { $password = $_POST['user_password']; }
    elseif (isset($_POST['password'])) { $password = $_POST['password']; }

    if (empty($name) || empty($phone) || empty($password)) {
        // NOTE: previously this leaked the raw request body (debug_json / debug_post)
        // back to the client, which can expose whatever the caller sent (including
        // passwords) in the response. Removed — never echo raw input back.
        echo json_encode([
            "success" => false,
            "message" => "សូមបំពេញព័ត៌មានឱ្យបានគ្រប់គ្រាន់"
        ]);
        exit();
    }

    // ៣. ពិនិត្យមើលលេខទូរស័ព្ទជាន់គ្នា
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

    // ៤. បញ្ចូលទិន្នន័យទៅក្នុង Database table 'users'
    $stmt = $conn->prepare("INSERT INTO users (name, phone, password, status, role, type) VALUES (?, ?, ?, 'ACTIVE', 'user', 'user')");
    $stmt->bind_param("sss", $name, $phone, $hashedPassword);

    if ($stmt->execute()) {
        $newUserId = $conn->insert_id;

        echo json_encode([
            "success" => true,
            "message" => "ចុះឈ្មោះជោគជ័យ",
            "token" => "auto_generated_token_example", // TODO: replace with a real JWT/session token
            "user" => [
                "id" => (int)$newUserId,
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
        "message" => "Database error."
    ]);
}
