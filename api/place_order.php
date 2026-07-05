<?php
/**
 * place_order.php
 * Process client multi-item carts, calculate checkout values, and return the new Order identifier.
 */
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_helper.php';

$user = get_authenticated_user($conn);
if (!$user) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Authentication context verification failed."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['cart_items']) || empty($data['cart_items'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Your basket or cart list parameter matrix is completely empty."]);
    exit();
}

$address = isset($data['address']) ? trim($data['address']) : 'Default Delivery Point';
$latitude = isset($data['latitude']) ? doubleval($data['latitude']) : 0.0;
$longitude = isset($data['longitude']) ? doubleval($data['longitude']) : 0.0;
$delivery_notes = isset($data['delivery_notes']) ? trim($data['delivery_notes']) : '';
$payment_type = isset($data['payment_type']) ? trim($data['payment_type']) : 'Cash';

$sub_total = 0.0;
foreach ($data['cart_items'] as $item) {
    $qty = intval($item['quantity'] ?? 1);
    $price = floatval($item['price'] ?? 0.0);
    $sub_total += ($price * $qty);
}

$delivery_fee = 1.50; 
$grand_total = $sub_total + $delivery_fee;
$order_code = "FM" . upper(bin2hex(random_bytes(3)));

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO orders (code, customer_id, branch_id, sub_total, delivery_fee, grand_total, address, latitude, longitude, delivery_notes, status, payment_type, order_date, created_at) VALUES (?, ?, 1, ?, ?, ?, ?, ?, ?, ?, 'PENDING', ?, NOW(), NOW())");
    $stmt->bind_param("sidddddsss", $order_code, $user['id'], $sub_total, $delivery_fee, $grand_total, $address, $latitude, $longitude, $delivery_notes, $payment_type);
    $stmt->execute();
    $order_id = $conn->insert_id;
    $stmt->close();

    $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price, created_at) VALUES (?, ?, ?, ?, NOW())");
    foreach ($data['cart_items'] as $item) {
        $itemId = intval($item['id']);
        $qty = intval($item['quantity']);
        $price = floatval($item['price']);
        $itemStmt->bind_param("iiid", $order_id, $itemId, $qty, $price);
        $itemStmt->execute();
    }
    $itemStmt->close();

    $conn->commit();
    echo json_encode([
        "success" => true,
        "message" => "Order verified and processed successfully.",
        "data" => [
            "order_id" => (int)$order_id,
            "code" => $order_code,
            "grand_total" => $grand_total
        ]
    ]);
} catch (Throwable $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Checkout transaction failure pipeline error structure."]);
}