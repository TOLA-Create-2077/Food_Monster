<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Branch;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['branch', 'items', 'payments'])->latest()->get();
        return view('order', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['branch', 'items', 'payments'])->findOrFail($id);
        return view('order-show', compact('order'));
    }

    public function edit($id)
    {
        $order = Order::with(['branch', 'items', 'payments'])->findOrFail($id);
        $branches = Branch::all();

        $customerTypes = ['VIP', 'New Customer', 'Regular'];
        $paymentTypes = ['pending', 'paid', 'partial'];
        $orderSources = ['Telegram', 'Facebook', 'Walk-in'];
        $customerCategories = ['New Customer', 'Old Customer'];
        $paymentMethods = ['Cash', 'ABA', 'AMK', 'ACLEDA'];

        return view('order-edit', compact(
            'order',
            'branches',
            'customerTypes',
            'paymentTypes',
            'orderSources',
            'customerCategories',
            'paymentMethods'
        ));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'delivery_fee' => $request->delivery_fee,
            'payment_status' => $request->payment_status,
            'delivery_date' => $request->delivery_date,
            'delivery_time' => $request->delivery_time,
            'order_source' => $request->order_source,
            'customer_category' => $request->customer_category,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'extra_info' => $request->extra_info,
            'chef_group' => $request->chef_group,
            'remark' => $request->remark,
            'discount' => $request->discount ?? 0,
            'grand_total' => $request->grand_total ?? 0,
        ]);

        OrderItem::where('order_id', $order->id)->delete();

        if ($request->items) {
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_name' => $item['item_name'] ?? '',
                    'description' => $item['description'] ?? '',
                    'qty' => $item['qty'] ?? 1,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'discount' => $item['discount'] ?? 0,
                    'total_price' => $item['total_price'] ?? 0,
                ]);
            }
        }

        Payment::where('order_id', $order->id)->delete();

        if ($request->payments) {
            foreach ($request->payments as $pay) {
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $pay['payment_method'] ?? '',
                    'remark' => $pay['remark'] ?? '',
                    'amount' => $pay['amount'] ?? 0,
                ]);
            }
        }

        return redirect()->route('order.index')->with('success', 'Order Updated');
    }

    public function receipt($id)
    {
        $order = Order::with(['branch', 'items', 'payments'])->findOrFail($id);
        return view('order-receipt', compact('order'));
    }

    public function bill($id)
    {
        $order = Order::with(['branch', 'items', 'payments'])->findOrFail($id);
        return view('order-bill', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        switch ($request->status) {
            case 'lock':
                $order->order_status = 'LOCKED';
                break;
            case 'cancel':
                $order->order_status = 'CANCEL';
                break;
            case 'void':
                $order->order_status = 'VOID';
                break;
            case 'ready':
                $order->order_status = 'FOOD READY';
                break;
            case 'refund':
                $order->order_status = 'REFUND';
                break;
        }

        $order->save();

        return response()->json(['success' => true]);
    }
}