<?php
// Set headers to allow JSON content and cross-origin mobile app calls
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 1. Include your database connection setup
require_once __DIR__ . '/config_pdo.php'; 

// 2. Fetch the raw input data coming from Android Retrofit client body
$inputRaw = file_get_contents("php://input");
$data = json_decode($inputRaw, true);

if (!empty($data)) {
    try {
        // Start a database transaction using your $conn variable safely
        $conn->beginTransaction();

        // 3. Extract the primary order request details mapped from Kotlin OrderRequest
        $branch_id          = isset($data['branch_id']) ? (int)$data['branch_id'] : 1;
        $order_source_id    = isset($data['order_source_id']) ? (int)$data['order_source_id'] : 1;
        $customer_id        = isset($data['customer_id']) ? (int)$data['customer_id'] : null;
        $name               = isset($data['name']) ? $data['name'] : '';
        $phone              = isset($data['phone']) ? $data['phone'] : '';
        $address            = isset($data['address']) ? $data['address'] : '';
        $delivery_fee       = isset($data['delivery_fee']) ? (double)$data['delivery_fee'] : 0.0;
        $driver_delivery_fee= isset($data['driver_delivery_fee']) ? (double)$data['driver_delivery_fee'] : 0.0;
        $free_delivery      = isset($data['free_delivery']) ? (int)$data['free_delivery'] : 0;
        $discount_percent   = isset($data['discount_percent']) ? (double)$data['discount_percent'] : 0.0;
        $sub_total          = isset($data['sub_total']) ? (double)$data['sub_total'] : 0.0;
        $grand_total        = isset($data['grand_total']) ? (double)$data['grand_total'] : 0.0;
        $payment_type       = isset($data['payment_type']) ? $data['payment_type'] : 'CASH';
        $user_id            = isset($data['user_id']) ? (int)$data['user_id'] : 1;
        $remark             = isset($data['remark']) ? $data['remark'] : null;
        $items              = isset($data['items']) ? $data['items'] : [];

        // Validate basic parameters
        if (empty($name) || empty($phone) || empty($items)) {
            throw new Exception("Missing required order fields or items list.");
        }

        // 4. Insert order details into the main `orders` table
        $orderQuery = "INSERT INTO orders (
            branch_id, order_source_id, customer_id, name, phone, address, 
            delivery_fee, driver_delivery_fee, free_delivery, discount_percent, 
            sub_total, grand_total, payment_type, user_id, remark, created_at
        ) VALUES (
            :branch_id, :order_source_id, :customer_id, :name, :phone, :address, 
            :delivery_fee, :driver_delivery_fee, :free_delivery, :discount_percent, 
            :sub_total, :grand_total, :payment_type, :user_id, :remark, NOW()
        )";

        $orderStmt = $conn->prepare($orderQuery);
        $orderStmt->execute([
            ':branch_id'          => $branch_id,
            ':order_source_id'    => $order_source_id,
            ':customer_id'        => $customer_id,
            ':name'               => $name,
            ':phone'              => $phone,
            ':address'            => $address,
            ':delivery_fee'       => $delivery_fee,
            ':driver_delivery_fee'=> $driver_delivery_fee,
            ':free_delivery'      => $free_delivery,
            ':discount_percent'   => $discount_percent,
            ':sub_total'          => $sub_total,
            ':grand_total'        => $grand_total,
            ':payment_type'       => $payment_type,
            ':user_id'            => $user_id,
            ':remark'             => $remark
        ]);

        // Get the structural ID generated for this unique order transaction
        $orderId = $conn->lastInsertId();
        
        // Generate a clean user-facing tracking string code format (e.g., FM-000124)
        $generatedCode = "FM-" . str_pad($orderId, 6, "0", STR_PAD_LEFT);
        
        // Update the order entry row with the tracking sequence code text
        $updateCodeQuery = "UPDATE orders SET code = :code WHERE id = :id";
        $updateStmt = $conn->prepare($updateCodeQuery);
        $updateStmt->execute([':code' => $generatedCode, ':id' => $orderId]);

        // 5. Loop through and execute child rows mapping items array list inside `order_items`
        foreach ($items as $item) {
            $itemQuery = "INSERT INTO order_items (
                order_id, product_variate_id, description, quantity, unit_price, sub_total, grand_total
            ) VALUES (
                :order_id, :product_variate_id, :description, :quantity, :unit_price, :sub_total, :grand_total
            )";

            $itemStmt = $conn->prepare($itemQuery);
            $itemStmt->execute([
                ':order_id'           => $orderId,
                ':product_variate_id' => isset($item['product_variate_id']) ? (int)$item['product_variate_id'] : 0,
                ':description'        => isset($item['description']) ? $item['description'] : '',
                ':quantity'           => isset($item['quantity']) ? (int)$item['quantity'] : 1,
                ':unit_price'         => isset($item['unit_price']) ? (double)$item['unit_price'] : 0.0,
                ':sub_total'          => isset($item['sub_total']) ? (double)$item['sub_total'] : 0.0,
                ':grand_total'        => isset($item['grand_total']) ? (double)$item['grand_total'] : 0.0
            ]);
        }

        // Commit the complete transaction pipeline block together securely
        $conn->commit();

        // 6. Return successful status response directly matching your Kotlin OrderResponse model
        echo json_encode([
            "success" => true,
            "message" => "Order successfully synchronized into local storage schemas.",
            "order_id" => (int)$orderId,
            "code" => $generatedCode
        ]);

    } catch (Exception $e) {
        // Rollback structural additions safely if internal errors take place
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }

        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Database Error: " . $e->getMessage(), // Tells you exactly what table/column is wrong!
            "order_id" => null,
            "code" => null
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid Payload Error: No raw processing datasets parsed from target mobile application client.",
        "order_id" => null,
        "code" => null
    ]);
}
?>