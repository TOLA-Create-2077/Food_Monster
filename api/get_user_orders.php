<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_helper.php';

$user = get_authenticated_user($conn);
if (!$user) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Access denied. Credentials invalid."]);
    exit();
}

// Maps directly to the schema's 'customer_id' foreign relation key safely
$stmt = $conn->prepare("SELECT * FROM `orders` WHERE customer_id = ? ORDER BY id DESC");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Internal processing fault."]);
    exit();
}

$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = [
        "id" => (int)$row['id'],
        "code" => $row['code'] ?? "0",
        "customer_id" => (int)$row['customer_id'],
        "delivery_fee" => (float)($row['delivery_fee'] ?? 0.0),
        "sub_total" => (float)($row['sub_total'] ?? 0.0),
        "grand_total" => (float)($row['grand_total'] ?? 0.0),
        "order_date" => $row['order_date'] ?? "",
        "status" => $row['status'] ?? "PENDING",
        "payment_type" => $row['payment_type'] ?? "Cash"
    ];
}

echo json_encode(["success" => true, "data" => $orders]);
$stmt->close();