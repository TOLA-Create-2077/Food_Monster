<?php
// File: api/create_order.php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// NOTE: the original version of this file used $conn without ever creating
// it (no connection code at all) — that's fixed here via the shared PDO
// config, which also pulls credentials from env vars instead of hardcoding.
require_once __DIR__ . '/config_pdo.php';

$data = json_decode(file_get_contents("php://input"));

if (
    empty($data->customer_id) ||
    empty($data->restaurant_id) ||
    empty($data->receiver_name) ||
    empty($data->receiver_phone) ||
    empty($data->delivery_location_name) ||
    empty($data->payment_method) ||
    !isset($data->grand_total) ||
    empty($data->items)
) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Incomplete payload parsing constraints. Parameter mapping failure."));
    exit();
}

try {
    $conn->beginTransaction();

    $status = "Pending";
    $payment_status = ($data->payment_method === "CASH") ? "Pending" : "Waiting Payment";

    $query = "INSERT INTO orders SET
                customer_id = :customer_id,
                restaurant_id = :restaurant_id,
                receiver_name = :receiver_name,
                receiver_phone = :receiver_phone,
                delivery_location_name = :delivery_location_name,
                delivery_note = :delivery_note,
                payment_method = :payment_method,
                driver_tip = :driver_tip,
                subtotal = :subtotal,
                delivery_fee = :delivery_fee,
                tax = :tax,
                discount = :discount,
                grand_total = :grand_total,
                contactless_delivery = :contactless_delivery,
                gift_order = :gift_order,
                coupon_code = :coupon_code,
                latitude = :latitude,
                longitude = :longitude,
                status = :status,
                payment_status = :payment_status,
                created_at = NOW(),
                updated_at = NOW()";

    $stmt = $conn->prepare($query);

    $stmt->bindParam(':customer_id', $data->customer_id);
    $stmt->bindParam(':restaurant_id', $data->restaurant_id);
    $stmt->bindParam(':receiver_name', $data->receiver_name);
    $stmt->bindParam(':receiver_phone', $data->receiver_phone);
    $stmt->bindParam(':delivery_location_name', $data->delivery_location_name);
    $stmt->bindParam(':delivery_note', $data->delivery_note);
    $stmt->bindParam(':payment_method', $data->payment_method);
    $stmt->bindParam(':driver_tip', $data->driver_tip);
    $stmt->bindParam(':subtotal', $data->subtotal);
    $stmt->bindParam(':delivery_fee', $data->delivery_fee);
    $stmt->bindParam(':tax', $data->tax);
    $stmt->bindParam(':discount', $data->discount);
    $stmt->bindParam(':grand_total', $data->grand_total);
    $stmt->bindParam(':contactless_delivery', $data->contactless_delivery);
    $stmt->bindParam(':gift_order', $data->gift_order);
    $stmt->bindParam(':coupon_code', $data->coupon_code);
    $stmt->bindParam(':latitude', $data->latitude);
    $stmt->bindParam(':longitude', $data->longitude);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':payment_status', $payment_status);

    $stmt->execute();

    $order_id = $conn->lastInsertId();

    $details_query = "INSERT INTO order_details SET
                        order_id = :order_id,
                        food_id = :food_id,
                        quantity = :quantity,
                        price = :price,
                        subtotal = :subtotal";

    $details_stmt = $conn->prepare($details_query);

    foreach ($data->items as $item) {
        $details_stmt->bindParam(':order_id', $order_id);
        $details_stmt->bindParam(':food_id', $item->food_id);
        $details_stmt->bindParam(':quantity', $item->quantity);
        $details_stmt->bindParam(':price', $item->price);
        $details_stmt->bindParam(':subtotal', $item->subtotal);
        $details_stmt->execute();
    }

    $conn->commit();
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "order_id" => intval($order_id),
        "message" => "Order verified and saved successfully.",
        "eta_minutes" => 35
    ));

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Transactional database error."
    ));
}
