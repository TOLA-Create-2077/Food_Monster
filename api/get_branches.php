<?php
header("Content-Type: application/json");

require_once __DIR__ . '/config.php';

// Selects standard plain properties native to your branches table schema mapping definition
$sql = "SELECT id, title, description, image, cover, phone, status FROM branches WHERE status = 'ACTIVE' ORDER BY id ASC";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Execution context failure."]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "id" => (int)$row['id'],
        "title" => $row['title'] ?? "",
        "description" => $row['description'] ?? "",
        "image" => $row['image'] ?? "",
        "cover" => $row['cover'] ?? "",
        "phone" => $row['phone'] ?? ""
    ];
}

echo json_encode($data);