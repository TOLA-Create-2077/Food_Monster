<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $orderCounts = DB::table('orders')
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $allOrders = (int) DB::table('orders')->count();
        $pendingOrders = (int) ($orderCounts['PENDING'] ?? 0);
        $confirmedOrders = (int) ($orderCounts['CONFIRMED'] ?? 0);
        $rejectedOrders = (int) ($orderCounts['REJECTED'] ?? 0);
        $cancelledOrders = (int) ($orderCounts['CANCELLED'] ?? 0);
        $cookingStartedOrders = (int) ($orderCounts['COOKING_STARTED'] ?? 0);
        $cookingAlmostFinishedOrders = (int) ($orderCounts['COOKING_ALMOST_FINISHED'] ?? 0);
        $foodReadyOrders = (int) ($orderCounts['FOOD_READY'] ?? 0);
        $deliveryStartedOrders = (int) ($orderCounts['DELIVERY_STARTED'] ?? 0);
        $finishedOrders = (int) ($orderCounts['FINISHED'] ?? 0);

        $userCounts = DB::table('users')
            ->select('role', DB::raw('COUNT(*) as total'))
            ->where('status', 'ACTIVE')
            ->groupBy('role')
            ->pluck('total', 'role');

        $userAdmin = (int) ($userCounts['admin'] ?? 0);
        $userDriver = (int) ($userCounts['driver'] ?? 0);
        $posAdmin = (int) ($userCounts['cashier'] ?? $userCounts['pos_admin'] ?? $userCounts['pos'] ?? 0);
        $userCustomer = (int) ($userCounts['customer'] ?? 0);

        $ordersBySource = DB::table('orders')
            ->selectRaw("COALESCE(order_resource_from, order_from, 'Unknown') as source")
            ->selectRaw('COUNT(*) as total')
            ->groupByRaw("COALESCE(order_resource_from, order_from, 'Unknown')")
            ->orderByDesc('total')
            ->get();

        $paidAmount = (float) DB::table('order_payments')
            ->whereNull('deleted_at')
            ->where(function ($query) {
                $query->where('status', 'PAID')
                    ->orWhere('status', 'SUCCESS')
                    ->orWhereNull('status');
            })
            ->sum('amount');

        $monthlyRevenue = DB::table('invoices')
            ->selectRaw('MONTH(invoice_date) as month_num')
            ->selectRaw('SUM(grand_total_amount) as grand_total')
            ->selectRaw('SUM(sub_total_amount - COALESCE(discount_amount, 0)) as after_discount')
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

        return view('dashboard.dashboard', compact(
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