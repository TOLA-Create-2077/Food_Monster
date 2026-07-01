<?php
// Force PHP to catch and return errors cleanly instead of throwing a 500 page
ini_set('display_errors', 0);
error_reporting(E_ALL);
// Explicitly set server timezone to Cambodia (Phnom Penh)
date_default_timezone_set('Asia/Phnom_Penh');

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Global Exception handler to capture critical crashes and return them as JSON
set_exception_handler(function ($e) {
    echo json_encode([
        "success" => false,
        "message" => "Fatal Server Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine()
    ]);
    exit();
});

// Credentials come from config.php
require_once __DIR__ . '/config.php';

$json_input = file_get_contents("php://input");
$data = json_decode($json_input, true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "No valid input data received. JSON parser failed."]);
    exit();
}

$conn->begin_transaction();

try {
    $branch_id           = $data['branch_id'] ?? $data['branchId'] ?? 1;
    $order_source_id     = $data['order_source_id'] ?? $data['orderSourceId'] ?? 1;
    $code                = $data['code'] ?? ('ORD-' . strtoupper(uniqid()));
    $customer_id         = $data['customer_id'] ?? $data['customerId'] ?? null;

    $name                = $data['name'] ?? 'Customer';
    $phone               = $data['phone'] ?? '';
    $address             = $data['address'] ?? '';
    $location            = $data['location'] ?? null;
    $delivery_id         = $data['delivery_id'] ?? $data['deliveryId'] ?? null;

    $delivery_fee        = (double)($data['delivery_fee'] ?? $data['deliveryFee'] ?? 0.0);
    $driver_delivery_fee = (double)($data['driver_delivery_fee'] ?? $data['driverDeliveryFee'] ?? 0.0);
    $free_delivery       = (int)($data['free_delivery'] ?? $data['freeDelivery'] ?? 0);
    $discount_percent    = (double)($data['discount_percent'] ?? $data['discountPercent'] ?? 0.0);

    $sub_total           = (double)($data['sub_total'] ?? $data['subtotal'] ?? $data['subTotal'] ?? 0.0);
    $grand_total         = (double)($data['grand_total'] ?? $data['grandtotal'] ?? $data['grandTotal'] ?? 0.0);
    $receive_amount      = (double)($data['receive_amount'] ?? $data['receiveAmount'] ?? 0.0);

    $scheduled_date      = $data['scheduled_date'] ?? $data['scheduledDate'] ?? null;
    $invoice_date        = $data['invoice_date'] ?? $data['invoiceDate'] ?? date('Y-m-d H:i:s');
    $order_date          = $data['order_date'] ?? $data['orderDate'] ?? date('Y-m-d H:i:s');
    $status              = $data['status'] ?? 'PENDING';

    $remark              = $data['remark'] ?? null;
    $extra_info          = $data['extra_info'] ?? $data['extraInfo'] ?? null;
    $proof_image         = $data['proof_image'] ?? $data['proofImage'] ?? null;
    $fcm_token           = $data['fcm_token'] ?? $data['fcmToken'] ?? null;
    $order_resource_from = $data['order_resource_from'] ?? $data['orderResourceFrom'] ?? null;
    $customer_type       = $data['customer_type'] ?? $data['customerType'] ?? null;
    $customer_preference = $data['customer_preference'] ?? $data['customerPreference'] ?? null;
    $membership_type     = $data['membership_type'] ?? $data['membershipType'] ?? null;
    $membership_number   = $data['membership_number'] ?? $data['membershipNumber'] ?? null;
    $benefits            = $data['benefits'] ?? null;

    $payment_type        = $data['payment_type'] ?? $data['paymentType'] ?? 'CASH';
    $is_locked           = (int)($data['is_locked'] ?? $data['isLocked'] ?? 0);
    $order_from          = $data['order_from'] ?? $data['orderFrom'] ?? 'Android App';
    $customer_category   = $data['customer_category'] ?? $data['customerCategory'] ?? null;
    $user_id             = $data['user_id'] ?? $data['userId'] ?? 1;

    $created_at          = date('Y-m-d H:i:s');
    $updated_at          = date('Y-m-d H:i:s');

    $items = $data['items'] ?? [];
    if (empty($items)) {
        throw new Exception("Your cart items payload is empty.");
    }

    if ($sub_total == 0.0) {
        foreach ($items as $item) {
            $qty = (int)($item['quantity'] ?? 1);
            $u_price = (double)($item['unit_price'] ?? $item['unitPrice'] ?? 0.0);
            $sub_total += ($qty * $u_price);
        }
    }
    if ($grand_total == 0.0) {
        $grand_total = $sub_total + $delivery_fee;
    }

    $orderQuery = "INSERT INTO `orders` (
        branch_id, order_source_id, code, customer_id, name, phone, address, location,
        delivery_id, delivery_fee, driver_delivery_fee, free_delivery, discount_percent,
        sub_total, grand_total, receive_amount, scheduled_date, invoice_date, order_date,
        status, remark, extra_info, proof_image, fcm_token, order_resource_from,
        customer_type, customer_preference, membership_type, membership_number, benefits,
        payment_type, is_locked, order_from, customer_category, user_id, created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($orderQuery);
    if (!$stmt) {
        throw new Exception("SQL syntax preparing error on master table: " . $conn->error);
    }

    $types = "iiissssssiddiddddssssssssssssssisii" . "ss";

    $stmt->bind_param(
        $types,
        $branch_id, $order_source_id, $code, $customer_id, $name, $phone, $address, $location,
        $delivery_id, $delivery_fee, $driver_delivery_fee, $free_delivery, $discount_percent,
        $sub_total, $grand_total, $receive_amount, $scheduled_date, $invoice_date, $order_date,
        $status, $remark, $extra_info, $proof_image, $fcm_token, $order_resource_from,
        $customer_type, $customer_preference, $membership_type, $membership_number, $benefits,
        $payment_type, $is_locked, $order_from, $customer_category, $user_id, $created_at, $updated_at
    );

    if (!$stmt->execute()) {
        throw new Exception("Execution saving error on orders master raw: " . $stmt->error);
    }

    $orderId = $conn->insert_id;
    $stmt->close();

    $detailQuery = "INSERT INTO `order_details` (
        order_id, product_variate_id, description, quantity,
        unit_price, discount_percent, sub_total, grand_total, status, created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $detailStmt = $conn->prepare($detailQuery);
    if (!$detailStmt) {
        throw new Exception("SQL syntax planning error on items detail table: " . $conn->error);
    }

    // Explicitly initialize the tracking reference placeholders
    $p_variate_id = 1;
    $p_desc       = '';
    $p_qty        = 1;
    $p_u_price    = 0.0;
    $p_discount   = 0.0;
    $p_sub        = 0.0;
    $p_grand      = 0.0;
    $p_status     = 'PENDING';

    $detailStmt->bind_param(
        "iisisdddsss",
        $orderId, $p_variate_id, $p_desc, $p_qty,
        $p_u_price, $p_discount, $p_sub, $p_grand, $p_status, $created_at, $updated_at
    );

    foreach ($items as $item) {
        // 🛠️ FIXED: Prevent passing null references to the integer binder 'i'
        $raw_id = $item['product_variate_id'] ?? $item['productVariateId'] ?? 1;
        $p_variate_id = ($raw_id !== null && (int)$raw_id > 0) ? (int)$raw_id : 1;
        
        $p_desc       = $item['description'] ?? 'Food item ordered';
        $p_qty        = (int)($item['quantity'] ?? 1);
        $p_u_price    = (double)($item['unit_price'] ?? $item['unitPrice'] ?? 0.0);
        $p_discount   = (double)($item['discount_percent'] ?? $item['discountPercent'] ?? 0.0);
        $p_sub        = (double)($item['sub_total'] ?? $item['subTotal'] ?? ($p_u_price * $p_qty));
        $p_grand      = (double)($item['grand_total'] ?? $item['grandTotal'] ?? $p_sub);
        $p_status     = $item['status'] ?? 'PENDING';

        if (!$detailStmt->execute()) {
            throw new Exception("Failed saving entry item line: " . $detailStmt->error);
        }
    }
    
    $detailStmt->close();
    $conn->commit();

    echo json_encode([
        "success" => true,
        "message" => "ការបញ្ជាទិញទទួលបានជោគជ័យ (Order placed successfully)",
        "orderId" => $orderId,
        "code" => $code
    ]);

} catch (Exception $e) {
    if (isset($conn) && $conn->ping()) {
        $conn->rollback();
    }
    echo json_encode([
        "success" => false,
        "message" => "Database rollback triggered: " . $e->getMessage()
    ]);
}

$conn->close();