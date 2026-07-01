<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Added OPTIONS for preflight requests
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/config.php';

try {
    // 1. Read data from JSON Raw Body
    $raw_input = file_get_contents("php://input");
    $data = json_decode($raw_input, true);

    // 2. Extract values (Supports both JSON and Form $_POST)
    $name = isset($data['name']) ? trim($data['name']) : (isset($_POST['name']) ? trim($_POST['name']) : '');
    $phone = isset($data['phone']) ? trim($data['phone']) : (isset($_POST['phone']) ? trim($_POST['phone']) : '');

    $password = '';
    if (isset($data['user_password'])) { $password = $data['user_password']; }
    elseif (isset($data['password'])) { $password = $data['password']; }
    elseif (isset($_POST['user_password'])) { $password = $_POST['user_password']; }
    elseif (isset($_POST['password'])) { $password = $_POST['password']; }

    if (empty($name) || empty($phone) || empty($password)) {
        echo json_encode([
            "success" => false,
            "message" => "សូមបំពេញព័ត៌មានឱ្យបានគ្រប់គ្រាន់"
        ]);
        exit();
    }

    // Ensure database connection is up
    if (!$conn) {
        throw new Exception("Database connection is not available.");
    }

    // 3. Check for duplicate phone numbers
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE phone = ? LIMIT 1");
    $checkStmt->bind_param("s", $phone);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "លេខទូរស័ព្ទនេះមានគណនីរួចហើយ"]);
        $checkStmt->close();
        exit();
    }
    $checkStmt->close();

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 4. Insert data into 'users' table matching your exact schema columns
    $stmt = $conn->prepare("INSERT INTO users (name, phone, password) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    
    $stmt->bind_param("sss", $name, $phone, $hashedPassword);

    if ($stmt->execute()) {
        $newUserId = $conn->insert_id;

        echo json_encode([
            "success" => true,
            "message" => "ចុះឈ្មោះជោគជ័យ",
            "token" => "auto_generated_token_example", 
            "user" => [
                "id" => (int)$newUserId,
                "name" => $name,
                "phone" => $phone
            ]
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        throw new Exception("Execution failed: " . $stmt->error);
    }

    $stmt->close();

} catch (Throwable $e) {
    // Return explicit debugging message to find out exactly what failed
    echo json_encode([
        "success" => false,
        "message" => "Database error.",
        "debug_error" => $e->getMessage() 
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}