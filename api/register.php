<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// ប្រសិនបើជា OPTIONS Request (CORS Preflight) ឱ្យត្រឡប់ទៅវិញភ្លាម
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ទាញយកអថេរ $pdo ចេញពី config.php
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
        echo json_encode([
            "success" => false,
            "message" => "សូមបំពេញព័ត៌មានឱ្យបានគ្រប់គ្រាន់"
        ]);
        exit();
    }

    // 🛠️ ៣. ពិនិត្យមើលលេខទូរស័ព្ទជាន់គ្នា (ប្តូរមកប្រើទម្រង់ PDO)
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE phone = :phone LIMIT 1");
    $checkStmt->execute([':phone' => $phone]);
    
    if ($checkStmt->fetch()) {
        echo json_encode([
            "success" => false, 
            "message" => "លេខទូរស័ព្ទនេះមានគណនីរួចហើយ"
        ]);
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 🛠️ ៤. បញ្ចូលទិន្នន័យទៅក្នុង Database table 'users' (ប្តូរមកប្រើទម្រង់ PDO)
    $stmt = $pdo->prepare("INSERT INTO users (name, phone, password, status, role, type) VALUES (:name, :phone, :password, 'ACTIVE', 'user', 'user')");
    
    $success = $stmt->execute([
        ':name'     => $name,
        ':phone'    => $phone,
        ':password' => $hashedPassword
    ]);

    if ($success) {
        $newUserId = $pdo->lastInsertId();

        echo json_encode([
            "success" => true,
            "message" => "ចុះឈ្មោះជោគជ័យ",
            "token" => "auto_generated_token_example", 
            "user" => [
                "id" => (int)$newUserId,
                "name" => $name,
                "phone" => $phone
            ]
        ]);
    } else {
        echo json_encode([
            "success" => false, 
            "message" => "មានបញ្ហាបច្ចេកទេសក្នុងការរក្សាទុកទិន្នន័យ"
        ]);
    }

} catch (Exception $e) {
    // 💡 ជំនួយការលម្អិត៖ ប្រសិនបើមាន Error វានឹងប្រាប់ចំៗតែម្តងដើម្បីងាយស្រួលកែសម្រួល
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}