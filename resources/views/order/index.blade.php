{{-- resources/views/order/index.blade.php --}}

@php
    use App\Models\Branch;
    use Carbon\Carbon;

    $pageTitle = 'Order Management';
    $currentPage = 'order';

    $branchIds = collect($orders ?? [])->pluck('branch_id')->filter()->unique()->values();
    $branches = Branch::whereIn('id', $branchIds)->get()->keyBy('id');
@endphp

@include('layout.header')
@include('layout.sidebar')

{{-- ហៅឯកសារ CSS មកប្រើ --}}
@include('order.partials.styles')

<main class="main">
    <div class="order-card">
        <div class="order-topbar">
            <div class="order-title">
                All Items : {{ count($orders) }}
            </div>
        </div>

        <div class="table-wrap">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Code</th>
                        <th>Branch</th>
                        <th>Order date</th>
                        <th>Schedule Date</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Payment</th>
                        <th>Print Status</th>
                        <th>Status</th>
                        <th style="width:120px;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($orders as $key => $order)
                        @php
                            $branch = $branches[$order->branch_id] ?? null;

                            $orderDate = !empty($order->created_at)
                                ? Carbon::parse($order->created_at)->format('d/m/Y H:i')
                                : '-';

                            $billDate = !empty($order->created_at)
                                ? Carbon::parse($order->created_at)->format('d-M-Y')
                                : '-';

                            $scheduleDate = '-';
                            if (!empty($order->delivery_date) && !empty($order->delivery_time)) {
                                $scheduleDate = Carbon::parse($order->delivery_date . ' ' . $order->delivery_time)->format('d/m/Y H:i');
                            } elseif (!empty($order->delivery_date)) {
                                $scheduleDate = Carbon::parse($order->delivery_date)->format('d/m/Y');
                            }

                            $orderNo = $order->order_no ?? 'LD0002598';
                            $printTime = $order->printing_at ?? '06:21 AM';
                            $openedAt = $order->opened_at ?? '06:21 AM';
                            $deliveryTime = !empty($order->delivery_time)
                                ? Carbon::parse($order->delivery_time)->format('h:i A')
                                : '12:00 PM';

                            $customerType = $order->customer_type ?? 'General';
                            $customerName = $order->name ?? '-';
                            $customerPhone = $order->phone ?? '-';
                            $customerAddress = $order->address ?? '-';
                            $extraInfo = $order->extra_info ?? '';
                            $note = $order->remark ?? '-';
                            $orderMan = $order->confirmed_by ?? 'Ms. Petercontinue';

                            $unitPrice = (float)($order->price ?? ($order->sub_total ?? $order->grand_total ?? 0));

                            $statusUrl = url('/order/' . $order->id . '/status');
                            $editUrl = url('/order/' . $order->id . '/edit');
                            $receiptUrl = url('/order/' . $order->id . '/receipt');

                            $billGroups = [
                                [
                                    'no' => 1,
                                    'group' => 'Customer',
                                    'type' => 'customer',
                                    'order_man' => $orderMan,
                                    'customer_type' => $customerType,
                                    'printing_at' => $printTime,
                                    'opened_at' => $openedAt,
                                    'station' => '',
                                    'extra_info' => $extraInfo,
                                    'print_status' => $order->print_status ?? 'Printed',
                                    'note' => $note,
                                    'bill_no' => $order->bill_no_customer ?? '17523',
                                    'date' => $billDate,
                                    'table_order' => $orderNo,
                                    'guest_count' => 0,
                                    'delivery_time' => $deliveryTime,
                                    'tel' => $customerPhone,
                                    'customer_name' => $customerName,
                                    'address' => $customerAddress,
                                    'items' => [
                                        ['description' => $order->item_name ?? 'Order Item', 'qty' => (int) ($order->qty ?? 1)],
                                        ['description' => $order->description ?? 'Extra Item', 'qty' => 1],
                                    ],
                                ],
                                [
                                    'no' => 2,
                                    'group' => $order->chef_group_1 ?? 'Meat Box Kitchen',
                                    'type' => 'chef',
                                    'order_man' => $orderMan,
                                    'customer_type' => $customerType,
                                    'printing_at' => $printTime,
                                    'opened_at' => $openedAt,
                                    'station' => $order->chef_station_1 ?? 'Meat Box Kitchen',
                                    'extra_info' => $extraInfo,
                                    'print_status' => $order->print_status ?? 'Printed',
                                    'note' => $note,
                                    'bill_no' => $order->bill_no_chef_1 ?? '17524',
                                    'date' => $billDate,
                                    'table_order' => $orderNo,
                                    'guest_count' => 0,
                                    'delivery_time' => $deliveryTime,
                                    'tel' => $customerPhone,
                                    'customer_name' => $customerName,
                                    'address' => $customerAddress,
                                    'items' => [
                                        ['description' => $order->chef_item_1 ?? 'Meat item 1', 'qty' => 1],
                                        ['description' => $order->chef_item_2 ?? 'Meat item 2', 'qty' => 1],
                                    ],
                                ],
                            ];
                        @endphp

                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $order->order_no ?? '-' }}</td>
                            <td>{{ $branch->name ?? '-' }}</td>
                            <td>{{ $orderDate }}</td>
                            <td>{{ $scheduleDate }}</td>
                            <td>{{ $customerName }}</td>
                            <td>{{ $customerPhone }}</td>
                            <td>{{ $order->qty ?? 1 }}</td>
                            <td>{{ number_format((float) ($order->grand_total ?? 0), 2) }}</td>
                            <td>{{ $order->payment_status ?? '-' }}</td>
                            <td>{{ $order->print_status ?? 'Printed' }}</td>
                            <td>
                                <span class="status-badge-order">
                                    {{ $order->order_status ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-wrap">
                                    <button
                                        type="button"
                                        class="view-btn"
                                        onclick="openOrderPopup(this)"
                                        data-id="{{ $order->id }}"
                                        data-order_no="{{ $order->order_no ?? '-' }}"
                                        data-order_date="{{ $orderDate }}"
                                        data-schedule_date="{{ $scheduleDate }}"
                                        data-branch="{{ $branch->name ?? '-' }}"
                                        data-remark="{{ $order->remark ?? '-' }}"
                                        data-name="{{ $customerName }}"
                                        data-phone="{{ $customerPhone }}"
                                        data-address="{{ $customerAddress }}"
                                        data-payment="{{ $order->payment_status ?? '-' }}"
                                        data-order_status="{{ $order->order_status ?? '-' }}"
                                        data-grand_total="{{ number_format((float) ($order->grand_total ?? 0), 2, '.', '') }}"
                                        data-sub_total="{{ number_format((float) ($order->sub_total ?? $order->subtotal ?? ($order->grand_total ?? 0)), 2, '.', '') }}"
                                        data-discount="{{ number_format((float) ($order->discount ?? 0), 2, '.', '') }}"
                                        data-delivery_fee="{{ number_format((float) ($order->delivery_fee ?? 0), 2, '.', '') }}"
                                        data-qty="{{ $order->qty ?? 1 }}"
                                        data-price="{{ number_format($unitPrice, 2, '.', '') }}"
                                        data-item_name="{{ $order->item_name ?? 'Order Item' }}"
                                        data-description="{{ $order->description ?? '-' }}"
                                        data-image="{{ $order->image ?? 'https://cdn-icons-png.flaticon.com/512/1046/1046784.png' }}"
                                        data-chef_group="{{ $order->chef_group ?? 'CP002' }}"
                                        data-chef_station="{{ $order->chef_station ?? 'LD PIG&DUCK' }}"
                                        data-lat="{{ $order->latitude ?? '11.5564' }}"
                                        data-lng="{{ $order->longitude ?? '104.9282' }}"
                                        data-status_url="{{ $statusUrl }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <div class="action-dropdown">
                                        <button type="button" class="dots-btn" onclick="toggleActionMenu({{ $order->id }}, event)">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>

                                        <div class="action-menu" id="actionMenu{{ $order->id }}">
                                            <a href="javascript:void(0);" onclick="openPopupFromMenu(this)">
                                                <i class="fa-solid fa-eye"></i> View
                                            </a>

                                            <a href="{{ $editUrl }}">
                                                <i class="fa-solid fa-pen"></i> Edit
                                            </a>

                                            <a href="javascript:void(0);" onclick='openBillListPopup({!! htmlspecialchars(json_encode($billGroups), ENT_QUOTES, "UTF-8") !!})'>
                                                <i class="fa-solid fa-file-invoice"></i> Bill
                                            </a>

                                            <a href="{{ $receiptUrl }}">
                                                <i class="fa-solid fa-receipt"></i> Receipt
                                            </a>

                                            <form action="{{ $statusUrl }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="lock">
                                                <button type="submit">
                                                    <i class="fa-solid fa-lock"></i> Lock
                                                </button>
                                            </form>

                                            <form action="{{ $statusUrl }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="cancel">
                                                <button type="submit">
                                                    <i class="fa-solid fa-ban"></i> Cancel
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>

{{-- ហៅសមាសភាគ Popups (View / Bill / Detail) --}}
@include('order.partials.popups')

@include('layout.footer')

{{-- ហៅឯកសារ JavaScript មកប្រើ --}}
@include('order.partials.scripts')