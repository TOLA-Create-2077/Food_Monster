<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Credentials now come from config_pdo.php (env vars), not hardcoded here.
require_once __DIR__ . '/config_pdo.php';

$data = json_decode(file_get_contents("php://input"));

if (
    empty($data->user_id) ||
    empty($data->receiver_name) ||
    empty($data->receiver_phone) ||
    empty($data->delivery_location_name) ||
    empty($data->payment_method) ||
    !isset($data->total_amount) ||
    empty($data->items)
) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Validation Failure: Malformed entity request payload fields."));
    exit();
}

try {
    $conn->beginTransaction();

    $status = "Pending";
    $payment_status = ($data->payment_method === "CASH") ? "Pending" : "Waiting Payment";

    $query = "INSERT INTO orders SET
                user_id = :user_id,
                address_id = :address_id,
                sub_total = :sub_total,
                discount = :discount,
                delivery_fee = :delivery_fee,
                vat = :vat,
                driver_tip = :driver_tip,
                total_amount = :total_amount,
                payment_method = :payment_method,
                payment_status = :payment_status,
                status = :status,
                receiver_name = :receiver_name,
                receiver_phone = :receiver_phone,
                delivery_location_name = :delivery_location_name,
                delivery_note = :delivery_note,
                contactless_delivery = :contactless_delivery,
                gift_order = :gift_order,
                coupon_code = :coupon_code,
                latitude = :latitude,
                longitude = :longitude,
                restaurant_id = :restaurant_id,
                created_at = NOW(),
                updated_at = NOW()";

    $stmt = $conn->prepare($query);

    $stmt->bindParam(':user_id', $data->user_id);
    $stmt->bindParam(':address_id', $data->address_id);
    $stmt->bindParam(':sub_total', $data->sub_total);
    $stmt->bindParam(':discount', $data->discount);
    $stmt->bindParam(':delivery_fee', $data->delivery_fee);
    $stmt->bindParam(':vat', $data->vat);
    $stmt->bindParam(':driver_tip', $data->driver_tip);
    $stmt->bindParam(':total_amount', $data->total_amount);
    $stmt->bindParam(':payment_method', $data->payment_method);
    $stmt->bindParam(':payment_status', $payment_status);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':receiver_name', $data->receiver_name);
    $stmt->bindParam(':receiver_phone', $data->receiver_phone);
    $stmt->bindParam(':delivery_location_name', $data->delivery_location_name);
    $stmt->bindParam(':delivery_note', $data->delivery_note);
    $stmt->bindParam(':contactless_delivery', $data->contactless_delivery);
    $stmt->bindParam(':gift_order', $data->gift_order);
    $stmt->bindParam(':coupon_code', $data->coupon_code);
    $stmt->bindParam(':latitude', $data->latitude);
    $stmt->bindParam(':longitude', $data->longitude);
    $stmt->bindParam(':restaurant_id', $data->restaurant_id);

    $stmt->execute();

    $order_id = $conn->lastInsertId();

    $details_query = "INSERT INTO order_details SET
                        order_id = :order_id,
                        food_id = :food_id,
                        quantity = :quantity,
                        price = :price,
                        sub_total = :sub_total,
                        created_at = NOW(),
                        updated_at = NOW()";

    $details_stmt = $conn->prepare($details_query);

    foreach ($data->items as $item) {
        $details_stmt->bindParam(':order_id', $order_id);
        $details_stmt->bindParam(':food_id', $item->food_id);
        $details_stmt->bindParam(':quantity', $item->quantity);
        $details_stmt->bindParam(':price', $item->price);
        $details_stmt->bindParam(':sub_total', $item->sub_total);
        $details_stmt->execute();
    }

    $conn->commit();
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "order_id" => intval($order_id),
        "message" => "Order placed successfully.",
        "eta_minutes" => 35
    ));

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Transactional database mapping fault."
    ));
}
