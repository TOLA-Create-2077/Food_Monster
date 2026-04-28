<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index()
    {
        $branches = DB::table('branches')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $products = DB::table('products')
            ->where('status', 'active')
            ->orderBy('title_en')
            ->get();

        return view('pos', compact('branches', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'customer_type' => 'required|string|max:50',
            'name' => 'nullable|string|max:150',
            'phone' => 'required|string|max:30',
            'address' => 'nullable|string',
            'delivery_date' => 'nullable|date',
            'delivery_time' => 'nullable',
            'chef_group' => 'nullable|string|max:100',
            'order_source' => 'nullable|string|max:100',
            'customer_category' => 'nullable|string|max:50',
            'delivery_fee' => 'nullable|numeric',
            'subtotal' => 'required|numeric',
            'discount' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.description' => 'nullable|string',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'payments' => 'nullable|array',
            'payments.*.method' => 'nullable|string|max:50',
            'payments.*.remark' => 'nullable|string',
            'payments.*.amount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $customerId = DB::table('customers')->insertGetId([
                'customer_type' => $request->customer_type,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'extra_info' => $request->extra_info,
                'note' => $request->note,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $orderNo = 'ORD' . now()->format('YmdHis');

            $orderId = DB::table('orders')->insertGetId([
                'branch_id' => $request->branch_id,
                'customer_id' => $customerId,
                'order_no' => $orderNo,
                'order_date' => now(),
                'delivery_date' => $request->delivery_date,
                'delivery_time' => $request->delivery_time,
                'chef_group' => $request->chef_group,
                'order_source' => $request->order_source,
                'customer_category' => $request->customer_category,
                'delivery_fee' => $request->delivery_fee ?? 0,
                'subtotal' => $request->subtotal,
                'discount' => $request->discount,
                'grand_total' => $request->grand_total,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($request->items as $item) {
                $product = DB::table('products')->where('id', $item['product_id'])->first();

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'item_type' => 'product',
                    'product_id' => $item['product_id'],
                    'set_menu_id' => null,
                    'item_name' => $product->title_en,
                    'description' => $item['description'] ?? null,
                    'qty' => $item['qty'],
                    'unit_price' => $item['price'],
                    'discount_percent' => $item['discount'] ?? 0,
                    'total_price' => $item['total'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $paidAmount = 0;

            if ($request->filled('payments')) {
                foreach ($request->payments as $payment) {
                    if (
                        !empty($payment['method']) &&
                        isset($payment['amount']) &&
                        $payment['amount'] !== null &&
                        $payment['amount'] !== ''
                    ) {
                        DB::table('payments')->insert([
                            'order_id' => $orderId,
                            'payment_method' => $payment['method'],
                            'remark' => $payment['remark'] ?? null,
                            'amount' => $payment['amount'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $paidAmount += (float) $payment['amount'];
                    }
                }
            }

            $paymentStatus = 'pending';
            if ($paidAmount > 0 && $paidAmount < (float) $request->grand_total) {
                $paymentStatus = 'partial';
            } elseif ($paidAmount >= (float) $request->grand_total && (float) $request->grand_total > 0) {
                $paymentStatus = 'paid';
            }

            DB::table('orders')
                ->where('id', $orderId)
                ->update([
                    'payment_status' => $paymentStatus,
                    'updated_at' => now(),
                ]);

            if ($paidAmount > 0 || (float) $request->grand_total >= 0) {
                DB::table('invoices')->insert([
                    'order_id' => $orderId,
                    'invoice_no' => 'INV' . now()->format('YmdHis'),
                    'invoice_date' => now(),
                    'subtotal' => $request->subtotal,
                    'discount' => $request->discount,
                    'delivery_fee' => $request->delivery_fee ?? 0,
                    'grand_total' => $request->grand_total,
                    'payment_status' => $paymentStatus,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('receipts')->insert([
                    'order_id' => $orderId,
                    'receipt_no' => 'RCT' . now()->format('YmdHis'),
                    'receipt_date' => now(),
                    'total_amount' => $request->grand_total,
                    'include_tax' => 'no',
                    'print_status' => 'not_printed_yet',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('pos.index')
                ->with('success', 'Order created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}