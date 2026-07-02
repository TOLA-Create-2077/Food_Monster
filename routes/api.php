<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// This bypasses session CSRF checks perfectly!
Route::post('/place_order.php', function (Request $request) {
    
    // Check if payload contains data
    if (empty($request->all())) {
        return response()->json([
            "success" => false,
            "message" => "Invalid Payload Error: No payload data received.",
            "order_id" => null,
            "code" => null
        ], 400);
    }

    try {
        DB::beginTransaction();

        // 1. Insert into orders table matching your Android payload properties
        $orderId = DB::table('orders')->insertGetId([
            'branch_id'           => $request->input('branch_id', 1),
            'order_source_id'     => $request->input('order_source_id', 1),
            'customer_id'         => $request->input('customer_id'),
            'name'                => $request->input('name', 'Customer'),
            'phone'               => $request->input('phone', ''),
            'address'             => $request->input('address', ''),
            'delivery_fee'        => (double)$request->input('delivery_fee', 0.0),
            'driver_delivery_fee' => (double)$request->input('driver_delivery_fee', 0.0),
            'free_delivery'       => (int)$request->input('free_delivery', 0),
            'discount_percent'    => (double)$request->input('discount_percent', 0.0),
            'sub_total'           => (double)$request->input('sub_total', 0.0),
            'grand_total'         => (double)$request->input('grand_total', 0.0),
            'payment_type'        => $request->input('payment_type', 'CASH'),
            'user_id'             => $request->input('user_id', 1),
            'remark'              => $request->input('remark'),
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        // Generate custom sequential tracking code sequence
        $generatedCode = "FM-" . str_pad($orderId, 6, "0", STR_PAD_LEFT);
        DB::table('orders')->where('id', $orderId)->update(['code' => $generatedCode]);

        // 2. Loop through child elements items list payload array
        $items = $request->input('items', []);
        foreach ($items as $item) {
            DB::table('order_items')->insert([
                'order_id'           => $orderId,
                'product_variate_id' => isset($item['product_variate_id']) ? (int)$item['product_variate_id'] : 0,
                'description'        => isset($item['description']) ? $item['description'] : 'Food Item',
                'quantity'           => isset($item['quantity']) ? (int)$item['quantity'] : 1,
                'unit_price'         => isset($item['unit_price']) ? (double)$item['unit_price'] : 0.0,
                'sub_total'          => isset($item['sub_total']) ? (double)$item['sub_total'] : 0.0,
                'grand_total'        => isset($item['grand_total']) ? (double)$item['grand_total'] : 0.0,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }

        DB::commit();

        return response()->json([
            "success" => true,
            "message" => "Order placed successfully.",
            "order_id" => (int)$orderId,
            "code" => $generatedCode
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            "success" => false,
            "message" => "Database Transaction Exception: " . $e->getMessage(),
            "order_id" => null,
            "code" => null
        ], 500);
    }
});