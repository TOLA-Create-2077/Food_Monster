<?php
/**
 * get_order_tracking.php
 * Secure Route: Enforces logical object access validation controls over live tracking pipelines.
 */
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_helper.php';

$user = get_authenticated_user($conn);
if (!$user) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Session authentication verification missing."]);
    exit();
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

$stmt = $conn->prepare("SELECT id, customer_id, code, status, order_date, address, grand_total FROM orders WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$orderData = $stmt->get_get_result()->fetch_assoc();
$stmt->close();

if (!$orderData) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Target order tracking profile record missing."]);
    exit();
}

if ((int)$orderData['customer_id'] !== $user['id']) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Forbidden resource query context query block."]);
    exit();
}

echo json_encode([
    "success" => true,
    "message" => "Tracking metadata extracted.",
    "data" => [
        "id" => intval($orderData['id']),
        "code" => $orderData['code'],
        "status" => strtoupper($orderData['status']),
        "order_date" => $orderData['order_date'],
        "address" => $orderData['address'],
        "grand_total" => floatval($orderData['grand_total'])
    ]
]);