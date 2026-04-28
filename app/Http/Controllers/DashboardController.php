<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $orderCounts = DB::table('orders')
            ->select('order_status', DB::raw('COUNT(*) as total'))
            ->groupBy('order_status')
            ->pluck('total', 'order_status');

        $allOrders = (int) DB::table('orders')->count();
        $pendingOrders = (int) ($orderCounts['pending'] ?? 0);
        $confirmedOrders = (int) ($orderCounts['confirmed'] ?? 0);
        $rejectedOrders = (int) ($orderCounts['rejected'] ?? 0);
        $cancelledOrders = (int) ($orderCounts['cancelled'] ?? 0);
        $cookingStartedOrders = (int) ($orderCounts['cooking_started'] ?? 0);
        $cookingAlmostFinishedOrders = (int) ($orderCounts['cooking_almost_finished'] ?? 0);
        $foodReadyOrders = (int) ($orderCounts['food_ready'] ?? 0);
        $deliveryStartedOrders = (int) ($orderCounts['delivery_started'] ?? 0);
        $finishedOrders = (int) ($orderCounts['finished'] ?? 0);

        $userCounts = DB::table('users')
            ->select('role', DB::raw('COUNT(*) as total'))
            ->where('status', 'active')
            ->groupBy('role')
            ->pluck('total', 'role');

        $userAdmin = (int) ($userCounts['admin'] ?? $userCounts['user_admin'] ?? 0);
        $userDriver = (int) ($userCounts['driver'] ?? $userCounts['user_driver'] ?? 0);
        $posAdmin = (int) ($userCounts['pos_admin'] ?? $userCounts['cashier'] ?? $userCounts['pos'] ?? 0);

        $userCustomer = (int) DB::table('customers')->count();

        $ordersBySource = DB::table('orders')
            ->select(
                DB::raw("COALESCE(order_source, 'Unknown') as order_source"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('order_source')
            ->orderByDesc('total')
            ->get();

        $paidAmount = (float) DB::table('payments')->sum('amount');

        $monthlyRevenue = DB::table('invoices')
            ->selectRaw('MONTH(invoice_date) as month_num')
            ->selectRaw('SUM(grand_total) as grand_total')
            ->selectRaw('SUM(subtotal - discount) as after_discount')
            ->whereNotNull('invoice_date')
            ->groupByRaw('MONTH(invoice_date)')
            ->orderByRaw('MONTH(invoice_date)')
            ->get()
            ->keyBy('month_num');

        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];

        $revenueLabels = [];
        $afterDiscountData = [];
        $grandTotalData = [];

        foreach ($months as $monthNumber => $monthLabel) {
            $row = $monthlyRevenue->get($monthNumber);

            $revenueLabels[] = $monthLabel;
            $afterDiscountData[] = (float) ($row->after_discount ?? 0);
            $grandTotalData[] = (float) ($row->grand_total ?? 0);
        }

        return view('dashboard', compact(
            'allOrders',
            'pendingOrders',
            'confirmedOrders',
            'rejectedOrders',
            'cancelledOrders',
            'cookingStartedOrders',
            'cookingAlmostFinishedOrders',
            'foodReadyOrders',
            'deliveryStartedOrders',
            'finishedOrders',
            'userAdmin',
            'userDriver',
            'posAdmin',
            'userCustomer',
            'ordersBySource',
            'paidAmount',
            'revenueLabels',
            'afterDiscountData',
            'grandTotalData'
        ));
    }
}