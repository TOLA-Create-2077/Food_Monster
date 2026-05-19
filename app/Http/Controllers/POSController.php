<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class POSController extends Controller
{
    public function index(): View
    {
        $branches = DB::table('branches')
            ->where('status', 'ACTIVE')
            ->orderBy('id')
            ->get([
                'id',
                'title',
                'phone',
                'email',
                'location',
                'image',
            ]);

        $products = DB::table('product_variates as pv')
            ->join('item_variates as iv', 'iv.id', '=', 'pv.item_variate_id')
            ->join('items as i', 'i.id', '=', 'iv.item_id')
            ->leftJoin('item_category as ic', 'ic.item_id', '=', 'i.id')
            ->leftJoin('categories as c', 'c.id', '=', 'ic.category_id')
            ->where('i.status', 'ACTIVE')
            ->where('iv.status', 'ACTIVE')
            ->where('iv.pos_show', 1)
            ->where('pv.is_available', 1)
            ->orderBy('i.id')
            ->orderBy('iv.id')
            ->orderBy('pv.id')
            ->get([
                'pv.id as product_variate_id',
                'pv.price',
                'pv.description as variate_description',
                'pv.type as variate_type',
                'iv.title as variate_title',
                'iv.image as variate_image',
                'i.id as item_id',
                'i.title as item_title',
                'i.description as item_description',
                'i.image as item_image',
                'i.type as item_type',
                'c.title as category_title',
            ])
            ->map(function ($product) {
                $product->product_name = $this->extractDisplayText($product->variate_title)
                    ?: $this->extractDisplayText($product->item_title)
                    ?: 'Product';

                $product->product_description = $this->extractDisplayText($product->variate_description)
                    ?: $this->extractDisplayText($product->item_description)
                    ?: '';

                $product->product_type = $product->variate_type ?: $product->item_type ?: 'POS';
                $product->product_category = $this->extractDisplayText($product->category_title);
                $product->product_image = $product->variate_image ?: $product->item_image ?: '';

                return $product;
            });

        return view('pos.index', compact('branches', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'customer_type' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:191'],
            'address' => ['nullable', 'string'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'delivery_date' => ['nullable', 'date'],
            'delivery_time' => ['nullable'],
            'order_source' => ['nullable', 'string', 'max:255'],
            'customer_category' => ['nullable', 'string', 'max:255'],
            'extra_info' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variate_id' => ['nullable', 'integer', 'exists:product_variates,id'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.qty' => ['nullable', 'numeric', 'min:0.01'],
            'items.*.price' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'payments' => ['nullable', 'array'],
            'payments.*.method' => ['nullable', 'string', 'max:255'],
            'payments.*.remark' => ['nullable', 'string'],
            'payments.*.amount' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        $user = Auth::user();
        $userId = is_numeric($user?->id) ? (int) $user->id : null;

        if (!$userId) {
            return back()
                ->withInput()
                ->with('error', 'Logged in user id is invalid.');
        }

        $validItems = collect($validated['items'])
            ->filter(fn (array $item): bool => !empty($item['product_variate_id']))
            ->map(function (array $item): array {
                return [
                    'product_variate_id' => (int) $item['product_variate_id'],
                    'description' => $item['description'] ?? null,
                    'qty' => (float) ($item['qty'] ?? 0),
                    'price' => round((float) ($item['price'] ?? 0), 2),
                    'discount' => round((float) ($item['discount'] ?? 0), 2),
                ];
            })
            ->values();

        if ($validItems->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['items' => 'Please select at least one product.']);
        }

        foreach ($validItems as $index => $item) {
            if ($item['qty'] <= 0) {
                return back()
                    ->withInput()
                    ->withErrors(["items.$index.qty" => 'Quantity must be greater than 0.']);
            }
        }

        $productVariateIds = $validItems->pluck('product_variate_id')->unique()->values();

        $productLookup = DB::table('product_variates as pv')
            ->join('item_variates as iv', 'iv.id', '=', 'pv.item_variate_id')
            ->join('items as i', 'i.id', '=', 'iv.item_id')
            ->whereIn('pv.id', $productVariateIds)
            ->where('i.status', 'ACTIVE')
            ->where('iv.status', 'ACTIVE')
            ->get([
                'pv.id as product_variate_id',
                'pv.price as default_price',
                'pv.description as variate_description',
                'iv.title as variate_title',
                'i.title as item_title',
            ])
            ->keyBy('product_variate_id');

        if ($productLookup->count() !== $productVariateIds->count()) {
            return back()
                ->withInput()
                ->withErrors(['items' => 'One or more selected products are invalid.']);
        }

        $deliveryFee = round((float) ($validated['delivery_fee'] ?? 0), 2);
        $subtotal = 0.0;
        $discountTotal = 0.0;
        $grandTotal = 0.0;
        $detailsToInsert = [];

        foreach ($validItems as $item) {
            $product = $productLookup->get($item['product_variate_id']);

            if (!$product) {
                return back()
                    ->withInput()
                    ->withErrors(['items' => 'One or more selected products are invalid.']);
            }

            $qty = $item['qty'];
            $unitPrice = $item['price'];
            $discountPercent = min(100, max(0, $item['discount']));

            $lineSubtotal = round($qty * $unitPrice, 2);
            $lineGrandTotal = round($lineSubtotal - ($lineSubtotal * ($discountPercent / 100)), 2);

            $subtotal += $lineSubtotal;
            $grandTotal += $lineGrandTotal;
            $discountTotal += round($lineSubtotal - $lineGrandTotal, 2);

            $detailsToInsert[] = [
                'product_variate_id' => $item['product_variate_id'],
                'description' => $item['description']
                    ?: $this->extractDisplayText($product->variate_description)
                    ?: null,
                'quantity' => (int) round($qty),
                'unit_price' => $unitPrice,
                'discount_percent' => $discountPercent,
                'sub_total' => $lineSubtotal,
                'grand_total' => $lineGrandTotal,
                'status' => 'PENDING',
            ];
        }

        $subtotal = round($subtotal, 2);
        $discountTotal = round($discountTotal, 2);
        $grandTotal = round($grandTotal + $deliveryFee, 2);

        try {
            DB::transaction(function () use (
                $validated,
                $userId,
                $deliveryFee,
                $subtotal,
                $discountTotal,
                $grandTotal,
                $detailsToInsert
            ): void {
                $now = now();
                $orderCode = $this->generateCode('ORD');

                $paidAmount = 0.0;
                $paymentMethods = [];
                $paymentsToInsert = [];

                foreach (($validated['payments'] ?? []) as $payment) {
                    $method = trim((string) ($payment['method'] ?? ''));
                    $amount = round((float) ($payment['amount'] ?? 0), 2);

                    if ($method === '' || $amount <= 0) {
                        continue;
                    }

                    $paymentsToInsert[] = [
                        'payment_type_id' => null,
                        'payment_method_id' => null,
                        'amount' => $amount,
                        'status' => 'ACTIVE',
                        'image' => null,
                        'remark' => $payment['remark'] ?? $method,
                        'user_id' => $userId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $paidAmount += $amount;
                    $paymentMethods[] = $method;
                }

                $receiveAmount = round($paidAmount, 2);
                $orderStatus = $receiveAmount >= $grandTotal && $grandTotal > 0 ? 'PAID' : 'PENDING';

                $orderId = DB::table('orders')->insertGetId([
                    'branch_id' => $validated['branch_id'],
                    'order_source_id' => null,
                    'code' => $orderCode,
                    'customer_id' => null,
                    'name' => $validated['name'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'location' => null,
                    'delivery_id' => null,
                    'delivery_fee' => $deliveryFee,
                    'discount_percent' => $subtotal > 0 ? round(($discountTotal / $subtotal) * 100, 2) : 0,
                    'sub_total' => $subtotal,
                    'grand_total' => $grandTotal,
                    'receive_amount' => $receiveAmount,
                    'scheduled_date' => !empty($validated['delivery_date'])
                        ? $validated['delivery_date'] . ' ' . ($validated['delivery_time'] ?: '00:00:00')
                        : null,
                    'invoice_date' => $now,
                    'order_date' => $now,
                    'status' => $orderStatus,
                    'remark' => $validated['note'] ?? null,
                    'extra_info' => $validated['extra_info'] ?? null,
                    'proof_image' => null,
                    'fcm_token' => null,
                    'order_resource_from' => $validated['order_source'] ?? 'POS',
                    'customer_type' => $validated['customer_type'] ?? 'General',
                    'customer_preference' => null,
                    'membership_type' => null,
                    'membership_number' => null,
                    'benefits' => null,
                    'payment_type' => !empty($paymentMethods) ? implode(', ', array_unique($paymentMethods)) : null,
                    'is_locked' => 0,
                    'order_from' => 'POS',
                    'customer_category' => $validated['customer_category'] ?? 'New',
                    'user_id' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $orderDetails = array_map(function (array $detail) use ($orderId, $now): array {
                    return [
                        'order_id' => $orderId,
                        'product_variate_id' => $detail['product_variate_id'],
                        'description' => $detail['description'],
                        'quantity' => $detail['quantity'],
                        'unit_price' => $detail['unit_price'],
                        'discount_percent' => $detail['discount_percent'],
                        'sub_total' => $detail['sub_total'],
                        'grand_total' => $detail['grand_total'],
                        'status' => $detail['status'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }, $detailsToInsert);

                DB::table('order_details')->insert($orderDetails);

                if (!empty($paymentsToInsert)) {
                    $payments = array_map(function (array $payment) use ($orderId): array {
                        $payment['order_id'] = $orderId;
                        return $payment;
                    }, $paymentsToInsert);

                    DB::table('order_payments')->insert($payments);
                }

                DB::table('invoices')->insert([
                    'invoice_no' => $this->generateCode('INV'),
                    'order_id' => $orderId,
                    'order_no' => $orderCode,
                    'delivery_fee' => $deliveryFee,
                    'discount_amount' => $discountTotal,
                    'sub_total_amount' => $subtotal,
                    'vat_amount' => 0,
                    'grand_total_amount' => $grandTotal,
                    'invoice_date' => now()->toDateString(),
                    'start_period' => null,
                    'end_period' => null,
                    'exchange_rate' => 1,
                    'payment_status' => $receiveAmount >= $grandTotal && $grandTotal > 0 ? 'PAID' : 'PENDING',
                    'remark' => $validated['note'] ?? null,
                    'status' => 'ACTIVE',
                    'invoice_detail' => null,
                    'include_tax' => 'no',
                    'branch_id' => $validated['branch_id'],
                    'user_id' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('receipts')->insert([
                    'receipt_no' => $this->generateCode('RCT'),
                    'order_id' => $orderId,
                    'order_no' => $orderCode,
                    'cashier' => Auth::user()?->name,
                    'delivery_fee' => $deliveryFee,
                    'discount_amount' => $discountTotal,
                    'sub_total_amount' => $subtotal,
                    'vat_amount' => 0,
                    'grand_total_amount' => $grandTotal,
                    'receive_amount' => $receiveAmount,
                    'receipt_date' => now()->toDateString(),
                    'receipt_time' => now()->format('H:i:s'),
                    'extra_info' => $validated['extra_info'] ?? null,
                    'note' => $validated['note'] ?? null,
                    'status' => 'ACTIVE',
                    'include_tax' => 'no',
                    'branch_id' => $validated['branch_id'],
                    'exchange_rate' => 1,
                    'receipt_detail' => null,
                    'print_image_url' => null,
                    'is_printed' => 0,
                    'print_status' => 'pending',
                    'print_error' => null,
                    'printed_at' => null,
                    'print_success_count' => 0,
                    'user_id' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }, 3);

            return redirect()
                ->route('pos.index')
                ->with('success', 'Order created successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    private function generateCode(string $prefix): string
    {
        return $prefix . now()->format('YmdHisv') . random_int(1000, 9999);
    }

    private function extractDisplayText(mixed $value): string
    {
        if (is_null($value)) {
            return '';
        }

        if (is_string($value)) {
            $trimmed = trim($value);

            if ($trimmed === '') {
                return '';
            }

            $decoded = json_decode($trimmed, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                foreach (['en', 'km', 'title_en', 'title', 'name'] as $key) {
                    if (!empty($decoded[$key]) && is_string($decoded[$key])) {
                        return trim($decoded[$key]);
                    }
                }

                foreach ($decoded as $item) {
                    if (is_string($item) && trim($item) !== '') {
                        return trim($item);
                    }
                }
            }

            return $trimmed;
        }

        return (string) $value;
    }
}