<?php
/**
 * get_user_orders.php
 * Secure Route: Extracts identity from Bearer token to prevent cross-account history parameter scraping attacks.
 */
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_helper.php';

$user = get_authenticated_user($conn);
if (!$user) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Unauthorized connection request access block."]);
    exit();
}

$stmt = $conn->prepare("SELECT id, code, customer_id, delivery_fee, sub_total, grand_total, order_date, status, payment_type FROM `orders` WHERE customer_id = ? ORDER BY id DESC");
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

echo json_encode([
    "success" => true,
    "message" => "Orders fetched successfully",
    "data" => $orders
]);
$stmt->close();