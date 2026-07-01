<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
header("Content-Type: application/json; charset=UTF-8");

// Credentials now come from config.php (env vars), not hardcoded here.
require_once __DIR__ . '/config.php';

$phone = isset($_GET['phone']) ? trim($_GET['phone']) : '';

if (empty($phone)) {
    echo json_encode(["success" => false, "message" => "Invalid or missing Phone Number context parameter."]);
    exit();
}

$query = "SELECT * FROM `orders` WHERE phone = ? ORDER BY id DESC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL Prepare Error."]);
    exit();
}

$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {

    $orders[] = [
        "id" => (int)$row['id'],
        "branch_id" => isset($row['branch_id']) ? (int)$row['branch_id'] : 1,
        "order_source_id" => isset($row['order_source_id']) ? (int)$row['order_source_id'] : null,
        "code" => isset($row['code']) ? (string)$row['code'] : "0",
        "customer_id" => isset($row['customer_id']) ? (int)$row['customer_id'] : null,
        "name" => isset($row['name']) ? (string)$row['name'] : "",
        "phone" => isset($row['phone']) ? (string)$row['phone'] : "",
        "address" => isset($row['address']) ? (string)$row['address'] : "",
        "location" => isset($row['location']) ? (string)$row['location'] : null,
        "delivery_id" => isset($row['delivery_id']) ? (int)$row['delivery_id'] : null,

        "delivery_fee" => isset($row['delivery_fee']) ? (double)$row['delivery_fee'] : 0.0,
        "driver_delivery_fee" => isset($row['driver_delivery_fee']) ? (double)$row['driver_delivery_fee'] : null,
        "free_delivery" => isset($row['free_delivery']) ? (int)$row['free_delivery'] : null,
        "discount_percent" => isset($row['discount_percent']) ? (double)$row['discount_percent'] : null,
        "sub_total" => isset($row['sub_total']) ? (double)$row['sub_total'] : 0.0,
        "grand_total" => isset($row['grand_total']) ? (double)$row['grand_total'] : 0.0,
        "receive_amount" => isset($row['receive_amount']) ? (double)$row['receive_amount'] : null,

        "scheduled_date" => isset($row['scheduled_date']) ? (string)$row['scheduled_date'] : null,
        "invoice_date" => isset($row['invoice_date']) ? (string)$row['invoice_date'] : (string)$row['order_date'],
        "order_date" => isset($row['order_date']) ? (string)$row['order_date'] : "",

        "status" => isset($row['status']) ? (string)$row['status'] : "PENDING",
        "remark" => isset($row['remark']) ? (string)$row['remark'] : null,
        "payment_type" => isset($row['payment_type']) ? (string)$row['payment_type'] : "Cash",
        "order_from" => isset($row['order_from']) ? (string)$row['order_from'] : "Android App",
        "user_id" => isset($row['user_id']) ? (int)$row['user_id'] : null,

        "created_at" => isset($row['created_at']) ? (string)$row['created_at'] : null,
        "updated_at" => isset($row['updated_at']) ? (string)$row['updated_at'] : null,

        "branch_name" => "Little Duckling Branch",
        "driver_name" => null,
        "driver_phone" => null,
        "items" => null
    ];
}

echo json_encode([
    "success" => true,
    "message" => "Extraction successful.",
    "orders" => $orders
]);

$stmt->close();
$conn->close();
