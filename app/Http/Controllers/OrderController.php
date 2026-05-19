<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->input('per_page', 50);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $search = trim((string) $request->input('search', ''));
        $branchId = (int) $request->input('branch', 0);
        $payment = trim((string) $request->input('payment', ''));
        $status = trim((string) $request->input('status', ''));

        $query = Order::query()->with(['branch', 'items', 'payments']);

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('order_no', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('payment_status', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('order_status', 'like', "%{$search}%");
            });
        }

        if ($branchId > 0) {
            $query->where('branch_id', $branchId);
        }

        if ($payment !== '') {
            $query->where('payment_status', $payment);
        }

        if ($status !== '') {
            $query->where(function ($q) use ($status): void {
                $q->where('status', $status)
                    ->orWhere('order_status', $status);
            });
        }

        $orders = $query
            ->latest('id')
            ->paginate($perPage)
            ->appends($request->query());

        $branches = Branch::query()
            ->where('status', 'ACTIVE')
            ->orderBy('id')
            ->get();

        return view('order.index', compact('orders', 'branches'));
    }

    public function show(int $id): View
    {
        $order = Order::with(['branch', 'items', 'payments'])->findOrFail($id);

        return view('order.order-show', compact('order'));
    }

    public function edit(int $id): View
    {
        $order = Order::with(['branch', 'items', 'payments'])->findOrFail($id);

        $branches = Branch::query()
            ->where('status', 'ACTIVE')
            ->orderBy('id')
            ->get();

        $customerTypes = ['VIP', 'New Customer', 'Regular', 'General'];
        $paymentTypes = ['pending', 'paid', 'partial'];
        $orderSources = ['POS', 'Telegram', 'Facebook', 'Walk-in'];
        $customerCategories = ['New Customer', 'Old Customer', 'New', 'Old'];
        $paymentMethods = ['Cash', 'ABA', 'AMK', 'ACLEDA', 'Card'];

        return view('order.order-edit', compact(
            'order',
            'branches',
            'customerTypes',
            'paymentTypes',
            'orderSources',
            'customerCategories',
            'paymentMethods'
        ));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $order = Order::with(['items', 'payments'])->findOrFail($id);

        $validated = $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:191'],
            'address' => ['nullable', 'string'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['nullable', 'string', 'in:pending,paid,partial'],
            'delivery_date' => ['nullable', 'date'],
            'delivery_time' => ['nullable'],
            'order_source' => ['nullable', 'string', 'max:255'],
            'customer_category' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'string', 'max:255'],
            'longitude' => ['nullable', 'string', 'max:255'],
            'extra_info' => ['nullable', 'string', 'max:255'],
            'remark' => ['nullable', 'string'],
            'customer_type' => ['nullable', 'string', 'max:255'],
            'items' => ['nullable', 'array'],
            'items.*.product_variate_id' => ['nullable', 'integer', 'exists:product_variates,id'],
            'items.*.item_name' => ['nullable', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.qty' => ['nullable', 'numeric', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'payments' => ['nullable', 'array'],
            'payments.*.payment_method' => ['nullable', 'string', 'max:255'],
            'payments.*.remark' => ['nullable', 'string'],
            'payments.*.amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $order): void {
            $deliveryFee = round((float) ($validated['delivery_fee'] ?? 0), 2);

            $itemRows = collect($validated['items'] ?? [])
                ->filter(fn (array $item): bool => !empty($item['item_name']) || !empty($item['product_variate_id']))
                ->values();

            $subTotal = 0.0;
            $discountAmount = 0.0;
            $orderItems = [];

            foreach ($itemRows as $item) {
                $qty = max(1, (int) round((float) ($item['qty'] ?? 1)));
                $unitPrice = round((float) ($item['unit_price'] ?? 0), 2);
                $discountPercent = min(100, max(0, round((float) ($item['discount'] ?? 0), 2)));

                $lineSubTotal = round($qty * $unitPrice, 2);
                $lineDiscountAmount = round($lineSubTotal * ($discountPercent / 100), 2);
                $lineGrandTotal = round($lineSubTotal - $lineDiscountAmount, 2);

                $subTotal += $lineSubTotal;
                $discountAmount += $lineDiscountAmount;

                $orderItems[] = [
                    'product_variate_id' => !empty($item['product_variate_id']) ? (int) $item['product_variate_id'] : null,
                    'description' => $item['description'] ?? ($item['item_name'] ?? null),
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'discount_percent' => $discountPercent,
                    'sub_total' => $lineSubTotal,
                    'grand_total' => $lineGrandTotal,
                    'status' => 'PENDING',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $subTotal = round($subTotal, 2);
            $discountPercentForOrder = $subTotal > 0 ? round(($discountAmount / $subTotal) * 100, 2) : 0;
            $grandTotal = round(($subTotal - $discountAmount) + $deliveryFee, 2);

            $paymentRows = collect($validated['payments'] ?? [])
                ->filter(fn (array $payment): bool => !empty($payment['payment_method']) || (float) ($payment['amount'] ?? 0) > 0)
                ->values();

            $receiveAmount = round(
                $paymentRows->sum(fn (array $payment) => (float) ($payment['amount'] ?? 0)),
                2
            );

            $status = match ($validated['payment_status'] ?? 'pending') {
                'paid' => 'PAID',
                'partial' => 'PARTIAL',
                default => 'PENDING',
            };

            $scheduledDate = null;
            if (!empty($validated['delivery_date'])) {
                $scheduledDate = $validated['delivery_date'] . ' ' . (!empty($validated['delivery_time']) ? $validated['delivery_time'] : '00:00:00');
            }

            $locationParts = array_filter([
                $validated['latitude'] ?? null,
                $validated['longitude'] ?? null,
            ], fn ($value) => $value !== null && $value !== '');

            $order->update([
                'branch_id' => $validated['branch_id'],
                'name' => $validated['name'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'location' => !empty($locationParts) ? implode(',', $locationParts) : null,
                'delivery_fee' => $deliveryFee,
                'discount_percent' => $discountPercentForOrder,
                'sub_total' => $subTotal,
                'grand_total' => $grandTotal,
                'receive_amount' => $receiveAmount,
                'scheduled_date' => $scheduledDate,
                'order_resource_from' => $validated['order_source'] ?? $order->order_resource_from,
                'customer_category' => $validated['customer_category'] ?? null,
                'extra_info' => $validated['extra_info'] ?? null,
                'remark' => $validated['remark'] ?? null,
                'customer_type' => $validated['customer_type'] ?? $order->customer_type,
                'payment_type' => $paymentRows->pluck('payment_method')->filter()->unique()->implode(', '),
                'status' => $status,
                'updated_at' => now(),
            ]);

            OrderItem::where('order_id', $order->id)->delete();

            foreach ($orderItems as $itemData) {
                OrderItem::create(array_merge($itemData, [
                    'order_id' => $order->id,
                ]));
            }

            Payment::where('order_id', $order->id)->delete();

            foreach ($paymentRows as $payment) {
                Payment::create([
                    'order_id' => $order->id,
                    'payment_type_id' => null,
                    'payment_method_id' => null,
                    'amount' => round((float) ($payment['amount'] ?? 0), 2),
                    'status' => 'ACTIVE',
                    'image' => null,
                    'remark' => trim((string) (($payment['payment_method'] ?? '') . ' ' . ($payment['remark'] ?? ''))),
                    'user_id' => auth()->id(),
                ]);
            }
        });

        return redirect()
            ->route('order.index')
            ->with('success', 'Order updated successfully.');
    }

    public function receipt(int $id): View
    {
        $order = Order::with(['branch', 'items', 'payments'])->findOrFail($id);

        return view('order.order-receipt', compact('order'));
    }

    public function bill(int $id): View
    {
        $order = Order::with(['branch', 'items', 'payments'])->findOrFail($id);

        return view('order.order-bill', compact('order'));
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:lock,cancel,void,ready,refund'],
        ]);

        $order = Order::findOrFail($id);

        $order->status = match ($validated['status']) {
            'lock' => 'LOCKED',
            'cancel' => 'CANCEL',
            'void' => 'VOID',
            'ready' => 'FOOD READY',
            'refund' => 'REFUND',
        };

        $order->save();

        return redirect()
            ->route('order.index')
            ->with('success', 'Status updated successfully.');
    }
}