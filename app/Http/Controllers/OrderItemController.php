<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderItemController extends Controller
{
    public function index(Request $request): View
    {
        $orderItems = DB::table('order_items')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'order_items.id',
                'order_items.order_id',
                'order_items.item_type',
                'order_items.product_id',
                'order_items.set_menu_id',
                'order_items.item_name',
                'order_items.description',
                'order_items.qty',
                'order_items.unit_price',
                'order_items.discount_percent',
                'order_items.total_price',
                'orders.order_no',
                'products.title_en as product_name'
            )
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('order_items.item_name', 'like', "%{$search}%")
                        ->orWhere('order_items.description', 'like', "%{$search}%")
                        ->orWhere('orders.order_no', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('order_items.id')
            ->paginate(10)
            ->withQueryString();

        return view('order_items.index', compact('orderItems'));
    }

    public function create(): View
    {
        $orders = DB::table('orders')
            ->orderByDesc('id')
            ->get();

        $products = DB::table('products')
            ->orderBy('title_en')
            ->get();

        $orderItem = null;

        return view('order_items.form', compact('orders', 'products', 'orderItem'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'item_type' => ['required', 'string', 'max:50'],
            'product_id' => ['nullable', 'exists:products,id'],
            'set_menu_id' => ['nullable', 'integer'],
            'item_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $qty = (float) $validated['qty'];
        $unitPrice = (float) $validated['unit_price'];
        $discountPercent = (float) ($validated['discount_percent'] ?? 0);
        $gross = $qty * $unitPrice;
        $discountAmount = $gross * ($discountPercent / 100);
        $totalPrice = $gross - $discountAmount;

        DB::table('order_items')->insert([
            'order_id' => $validated['order_id'],
            'item_type' => $validated['item_type'],
            'product_id' => $validated['product_id'] ?? null,
            'set_menu_id' => $validated['set_menu_id'] ?? null,
            'item_name' => $validated['item_name'],
            'description' => $validated['description'] ?? null,
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'discount_percent' => $discountPercent,
            'total_price' => $totalPrice,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('order_items.index')
            ->with('success', 'Order item created successfully.');
    }

    public function edit(int $id): View
    {
        $orders = DB::table('orders')
            ->orderByDesc('id')
            ->get();

        $products = DB::table('products')
            ->orderBy('title_en')
            ->get();

        $orderItem = DB::table('order_items')->where('id', $id)->first();

        abort_if(!$orderItem, 404);

        return view('order_items.form', compact('orders', 'products', 'orderItem'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $orderItem = DB::table('order_items')->where('id', $id)->first();

        abort_if(!$orderItem, 404);

        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'item_type' => ['required', 'string', 'max:50'],
            'product_id' => ['nullable', 'exists:products,id'],
            'set_menu_id' => ['nullable', 'integer'],
            'item_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $qty = (float) $validated['qty'];
        $unitPrice = (float) $validated['unit_price'];
        $discountPercent = (float) ($validated['discount_percent'] ?? 0);
        $gross = $qty * $unitPrice;
        $discountAmount = $gross * ($discountPercent / 100);
        $totalPrice = $gross - $discountAmount;

        DB::table('order_items')
            ->where('id', $id)
            ->update([
                'order_id' => $validated['order_id'],
                'item_type' => $validated['item_type'],
                'product_id' => $validated['product_id'] ?? null,
                'set_menu_id' => $validated['set_menu_id'] ?? null,
                'item_name' => $validated['item_name'],
                'description' => $validated['description'] ?? null,
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'discount_percent' => $discountPercent,
                'total_price' => $totalPrice,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('order_items.index')
            ->with('success', 'Order item updated successfully.');
    }
}