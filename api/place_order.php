<?php
/**
 * api/place_order.php
 *
 * This file is automatically intercepted and run by Laravel's web.php fallback.
 * It bypasses Laravel's global middleware and connects directly to Aiven MySQL.
 */

// 1. Force the response format headers required by Android Retrofit
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight OPTIONS requests gracefully
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// 2. Safely read and extract the raw JSON data string sent from the phone app
$rawInput = file_get_contents('php://input');
$payload = json_decode($rawInput, true);

if (empty($payload)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid Payload Error: No post payload data received.",
        "order_id" => null,
        "code" => null
    ]);
    exit;
}

// 3. Define production database credential links for your Aiven Cloud
$DB_HOST = 'foodmonster-foodmonster2077.l.aivencloud.com';
$DB_PORT = '27243';
$DB_USER = 'avnadmin';
$DB_PASS = 'AVNS_zm11DvJhdhSKo24pyuy';
$DB_NAME = 'little_duckling_db';

try {
    // 4. Build PDO connection string structure with explicit non-verify SSL enforcement
    $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false // Allows secure data transit without requiring a physical cert file
    ];
    
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
    
    // Start atomic sql transaction
    $pdo->beginTransaction();

    // 5. Insert master parameters into orders table
    $orderQuery = "INSERT INTO orders (
        branch_id, order_source_id, customer_id, name, phone, address, 
        delivery_fee, driver_delivery_fee, free_delivery, discount_percent, 
        sub_total, grand_total, payment_type, user_id, remark, created_at, updated_at
    ) VALUES (
        :branch_id, :order_source_id, :customer_id, :name, :phone, :address,
        :delivery_fee, :driver_delivery_fee, :free_delivery, :discount_percent,
        :sub_total, :grand_total, :payment_type, :user_id, :remark, NOW(), NOW()
    )";

    $stmtOrder = $pdo->prepare($orderQuery);
    $stmtOrder->execute([
        ':branch_id'           => $payload['branch_id'] ?? 1,
        ':order_source_id'     => $payload['order_source_id'] ?? 1,
        ':customer_id'         => $payload['customer_id'] ?? null,
        ':name'                => $payload['name'] ?? '',
        ':phone'               => $payload['phone'] ?? '',
        ':address'             => $payload['address'] ?? '',
        ':delivery_fee'        => (double)($payload['delivery_fee'] ?? 0.0),
        ':driver_delivery_fee' => (double)($payload['driver_delivery_fee'] ?? 0.0),
        ':free_delivery'       => (int)($payload['free_delivery'] ?? 0),
        ':discount_percent'    => (double)($payload['discount_percent'] ?? 0.0),
        ':sub_total'           => (double)($payload['sub_total'] ?? 0.0),
        ':grand_total'         => (double)($payload['grand_total'] ?? 0.0),
        ':payment_type'        => $payload['payment_type'] ?? 'CASH',
        ':user_id'             => $payload['user_id'] ?? 1,
        ':remark'              => $payload['remark'] ?? null
    ]);

    $insertedOrderId = $pdo->lastInsertId();

    // 6. Generate custom readable invoice code layout string (e.g., FM-000124)
    $formattedCode = "FM-" . str_pad($insertedOrderId, 6, "0", STR_PAD_LEFT);
    $updateQuery = "UPDATE orders SET code = :code WHERE id = :id";
    $pdo->prepare($updateQuery)->execute([':code' => $formattedCode, ':id' => $insertedOrderId]);

    // 7. Loop through relational child array collection and save ordered items
    $orderItems = $payload['items'] ?? [];
    if (is_array($orderItems)) {
        $itemQuery = "INSERT INTO order_items (
            order_id, product_variate_id, description, quantity, unit_price, sub_total, grand_total, created_at, updated_at
        ) VALUES (
            :order_id, :product_variate_id, :description, :quantity, :unit_price, :sub_total, :grand_total, NOW(), NOW()
        )";
        
        $stmtItem = $pdo->prepare($itemQuery);
        
        foreach ($orderItems as $item) {
            $stmtItem->execute([
                ':order_id'           => $insertedOrderId,
                ':product_variate_id' => (int)($item['product_variate_id'] ?? 0),
                ':description'        => $item['description'] ?? '',
                ':quantity'           => (int)($item['quantity'] ?? 1),
                ':unit_price'         => (double)($item['unit_price'] ?? 0.0),
                ':sub_total'          => (double)($item['sub_total'] ?? 0.0),
                ':grand_total'        => (double)($item['grand_total'] ?? 0.0)
            ]);
        }
    }

    // Save modifications definitively to the cluster storage layers
    $pdo->commit();

    // Return the response object expected by Kotlin CheckoutViewModel callback handlers
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Order placed successfully.",
        "order_id" => (int)$insertedOrderId,
        "code" => $formattedCode
    ]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Aiven Cloud Database Operations Exception: " . $e->getMessage(),
        "order_id" => null,
        "code" => null
    ]);
}
exit;