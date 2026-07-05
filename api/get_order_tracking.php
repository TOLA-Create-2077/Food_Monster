<?php
error_reporting(0); // Prevents PHP warnings from corrupting JSON output stream
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

// Credentials now come from config.php (env vars), not hardcoded here.
require_once __DIR__ . '/config.php';

if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Missing mandatory tracking parameter: 'order_id' is required."
    ]);
    exit;
}

$order_id = intval($_GET['order_id']);

$query = "SELECT 
            id, 
            customer_id, 
            code, 
            status, 
            order_date, 
            COALESCE(branch_name, 'Little Duckling Main Branch') AS branch_name, 
            address, 
            payment_type, 
            delivery_fee, 
            grand_total 
          FROM orders 
          WHERE id = ? 
          LIMIT 1";

$stmt = $conn->prepare($query);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Failed to compile SQL structure."
    ]);
    exit;
}

$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "No record match found for Order ID: " . $order_id
    ]);
    $stmt->close();
    $conn->close();
    exit;
}

$orderData = $result->fetch_assoc();

$finalResponse = [
    "id" => intval($orderData['id']),
    "customer_id" => intval($orderData['customer_id']),
    "code" => $orderData['code'],
    "status" => strtoupper($orderData['status']),
    "order_date" => $orderData['order_date'],
    "branch_name" => $orderData['branch_name'],
    "address" => $orderData['address'],
    "payment_type" => $orderData['payment_type'],
    "delivery_fee" => floatval($orderData['delivery_fee']),
    "grand_total" => floatval($orderData['grand_total'])
];

$stmt->close();
$conn->close();

http_response_code(200);
echo json_encode($finalResponse);
exit;
