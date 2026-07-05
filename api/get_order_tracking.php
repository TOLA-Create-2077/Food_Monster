<?php
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_helper.php';

$user = get_authenticated_user($conn);
if (!$user) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Authentication failed."]);
    exit();
}

if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Required request parameter 'order_id' missing."]);
    exit();
}

$order_id = intval($_GET['order_id']);

$stmt = $conn->prepare("SELECT id, customer_id, code, status, order_date, address, grand_total FROM orders WHERE id = ? LIMIT 1");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Compilation fault."]);
    exit();
}

$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Target order tracking reference record missing."]);
    $stmt->close();
    exit();
}

$orderData = $result->fetch_assoc();

// Explicit contextual permission validation: Prevents unauthorized IDOR exposure 
if ((int)$orderData['customer_id'] !== $user['id']) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Access forbidden."]);
    $stmt->close();
    exit();
}

echo json_encode([
    "id" => intval($orderData['id']),
    "code" => $orderData['code'],
    "status" => strtoupper($orderData['status']),
    "order_date" => $orderData['order_date'],
    "address" => $orderData['address'],
    "grand_total" => floatval($orderData['grand_total'])
]);
$stmt->close();