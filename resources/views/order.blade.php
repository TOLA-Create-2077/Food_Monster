{{-- resources/views/order.blade.php --}}

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
                                [
                                    'no' => 3,
                                    'group' => $order->chef_group_2 ?? 'PIG Cooking',
                                    'type' => 'chef',
                                    'order_man' => $orderMan,
                                    'customer_type' => $customerType,
                                    'printing_at' => $printTime,
                                    'opened_at' => $openedAt,
                                    'station' => $order->chef_station_2 ?? 'PIG Cooking',
                                    'extra_info' => $extraInfo,
                                    'print_status' => $order->print_status ?? 'Printed',
                                    'note' => $note,
                                    'bill_no' => $order->bill_no_chef_2 ?? '17522',
                                    'date' => $billDate,
                                    'table_order' => $orderNo,
                                    'guest_count' => 0,
                                    'delivery_time' => $deliveryTime,
                                    'tel' => $customerPhone,
                                    'customer_name' => $customerName,
                                    'address' => $customerAddress,
                                    'items' => [
                                        ['description' => $order->chef_item_3 ?? 'Pig item 1', 'qty' => 1],
                                        ['description' => $order->chef_item_4 ?? 'Pig item 2', 'qty' => 1],
                                    ],
                                ],
                                [
                                    'no' => 4,
                                    'group' => $order->chef_group_3 ?? 'DUCK Cooking',
                                    'type' => 'chef',
                                    'order_man' => $orderMan,
                                    'customer_type' => $customerType,
                                    'printing_at' => $printTime,
                                    'opened_at' => $openedAt,
                                    'station' => $order->chef_station_3 ?? 'DUCK Cooking',
                                    'extra_info' => $extraInfo,
                                    'print_status' => $order->print_status ?? 'Printed',
                                    'note' => $note,
                                    'bill_no' => $order->bill_no_chef_3 ?? '17521',
                                    'date' => $billDate,
                                    'table_order' => $orderNo,
                                    'guest_count' => 0,
                                    'delivery_time' => $deliveryTime,
                                    'tel' => $customerPhone,
                                    'customer_name' => $customerName,
                                    'address' => $customerAddress,
                                    'items' => [
                                        ['description' => $order->chef_item_5 ?? 'Duck item 1', 'qty' => 1],
                                        ['description' => $order->chef_item_6 ?? 'Duck item 2', 'qty' => 1],
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
                            <td>{{ $order->name ?? '-' }}</td>
                            <td>{{ $order->phone ?? '-' }}</td>
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
                                        data-name="{{ $order->name ?? '-' }}"
                                        data-phone="{{ $order->phone ?? '-' }}"
                                        data-address="{{ $order->address ?? '-' }}"
                                        data-payment="{{ $order->payment_status ?? '-' }}"
                                        data-order_status="{{ $order->order_status ?? '-' }}"
                                        data-grand_total="{{ number_format((float) ($order->grand_total ?? 0), 2, '.', '') }}"
                                        data-sub_total="{{ number_format((float) ($order->sub_total ?? $order->subtotal ?? ($order->grand_total ?? 0)), 2, '.', '') }}"
                                        data-discount="{{ number_format((float) ($order->discount ?? 0), 2, '.', '') }}"
                                        data-delivery_fee="{{ number_format((float) ($order->delivery_fee ?? 0), 2, '.', '') }}"
                                        data-qty="{{ $order->qty ?? 1 }}"
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

                                            <a href="javascript:void(0);" onclick='openBillListPopup(@json($billGroups))'>
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

                                            <form action="{{ $statusUrl }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="void">
                                                <button type="submit">
                                                    <i class="fa-solid fa-circle-xmark"></i> Void
                                                </button>
                                            </form>

                                            <form action="{{ $statusUrl }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="ready">
                                                <button type="submit">
                                                    <i class="fa-solid fa-box"></i> Food Ready
                                                </button>
                                            </form>

                                            <form action="{{ $statusUrl }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="refund">
                                                <button type="submit">
                                                    <i class="fa-solid fa-rotate-left"></i> Refund
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

@include('layout.footer')

<div class="order-popup-overlay" id="orderPopupOverlay">
    <div class="order-popup-panel" id="orderPopupPanel">
        <div class="popup-header">
            <div>
                <div class="popup-title">
                    Order Num. <span id="popupOrderNo">-</span>
                    <span class="confirm-text">(Confirm by : <span id="popupConfirmBy">Ms. Petercontinue</span>)</span>
                </div>

                <div class="popup-dates">
                    <div>Order date: <span id="popupOrderDate">-</span></div>
                    <div>Schedule Date: <span id="popupScheduleDate">-</span></div>
                </div>
            </div>

            <button type="button" class="popup-close-x" onclick="closeOrderPopup()">✕</button>
        </div>

        <div class="popup-body-grid">
            <div class="popup-left">
                <div class="popup-card">
                    <div class="popup-card-header">
                        <span>Order Item</span>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>

                    <div class="order-item-box">
                        <img id="popupItemImage" src="https://cdn-icons-png.flaticon.com/512/1046/1046784.png" alt="item">
                        <div class="order-item-info">
                            <div class="item-code" id="popupItemCode">-</div>
                            <div class="item-name" id="popupItemName">Order Item</div>
                            <div class="item-desc" id="popupItemDesc">-</div>
                        </div>

                        <div class="order-item-price">
                            <div><span id="popupQtyPrice">1 × 0.00</span></div>
                            <div class="discount-orange" id="popupDiscountText">0.00</div>
                            <div id="popupTotalText">0.00</div>
                        </div>
                    </div>
                </div>

                <div class="popup-card">
                    <div class="popup-card-header">
                        <span>Order Summary</span>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>

                    <div class="summary-row">
                        <span>Sub Total</span>
                        <span id="popupSummaryQty">1</span>
                        <span id="popupSubTotal">0.00</span>
                    </div>

                    <div class="summary-row">
                        <span>Discount</span>
                        <span></span>
                        <span class="discount-orange" id="popupDiscount">0.00</span>
                    </div>

                    <div class="summary-row">
                        <span>Delivery</span>
                        <span>Delivery</span>
                        <span id="popupDeliveryFee">0.00</span>
                    </div>

                    <div class="summary-row total-row">
                        <span>Grand Total</span>
                        <span></span>
                        <span id="popupGrandTotal">0.00</span>
                    </div>
                </div>

                <div class="popup-card">
                    <div class="popup-card-header">
                        <span>Payment</span>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>

                    <div class="payment-row">
                        <span id="popupPayment">-</span>
                        <span id="popupPaymentAmount">0.00</span>
                    </div>
                </div>

                <div class="popup-map-card">
                    <iframe
                        id="popupMap"
                        width="100%"
                        height="230"
                        style="border:0;border-radius:10px;"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"
                        src="">
                    </iframe>
                </div>
            </div>

            <div class="popup-right">
                <div class="popup-card">
                    <div class="popup-card-header">
                        <span>Branch</span>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="simple-value" id="popupBranch">-</div>
                </div>

                <div class="popup-card">
                    <div class="popup-card-header">
                        <span>Remark</span>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="simple-value" id="popupRemark">-</div>
                </div>

                <div class="popup-card">
                    <div class="popup-card-header">
                        <span>Customer & Delivery Info</span>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>

                    <div class="info-line"><i class="fa-solid fa-user"></i> <span id="popupCustomerName">-</span></div>
                    <div class="info-line"><i class="fa-solid fa-phone"></i> <span id="popupCustomerPhone">-</span></div>
                    <div class="info-line"><i class="fa-solid fa-calendar-days"></i> <span id="popupCustomerOrderDate">-</span></div>
                    <div class="info-line"><i class="fa-solid fa-clock"></i> <span id="popupCustomerScheduleDate">-</span></div>
                    <div class="info-line"><i class="fa-solid fa-location-dot"></i> <span id="popupAddress">-</span></div>
                    <div class="info-line">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <a href="#" id="popupViewLocation" target="_blank">View Location</a>
                    </div>
                </div>

                <div class="popup-card">
                    <div class="popup-card-header">
                        <span>Chef Group</span>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="simple-value" id="popupChefGroup">CP002</div>
                    <div class="simple-value mt8" id="popupChefStation">LD PIG&DUCK</div>
                </div>
            </div>
        </div>

        <div class="popup-footer">
            <form id="foodReadyForm" method="POST" action="">
                @csrf
                <input type="hidden" name="status" value="ready">
                <button type="submit" class="food-ready-btn">
                    <i class="fa-solid fa-box"></i> Food Ready
                </button>
            </form>

            <button type="button" class="close-popup-btn" onclick="closeOrderPopup()">
                <i class="fa-solid fa-xmark"></i> Close
            </button>
        </div>
    </div>
</div>

<div class="bill-list-popup-overlay" id="billListPopupOverlay">
    <div class="bill-list-popup-panel">
        <div class="bill-list-header">
            <div class="bill-list-title">View Bill</div>
            <div class="bill-list-header-actions">
                <button type="button" class="bill-print-all-btn" onclick="printAllGroups()">
                    <i class="fa-solid fa-print"></i> Print All
                </button>
                <button type="button" class="bill-close-x" onclick="closeBillListPopup()">✕</button>
            </div>
        </div>

        <div class="bill-list-content">
            <table class="bill-list-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Date</th>
                        <th>Group</th>
                        <th>Type</th>
                        <th>Order Man</th>
                        <th>Customer Type</th>
                        <th>Printing At</th>
                        <th>Opened At</th>
                        <th>Station</th>
                        <th>ExtraInfo</th>
                        <th>Print Status</th>
                        <th>Note</th>
                        <th style="width:90px;">Action</th>
                    </tr>
                </thead>
                <tbody id="billListTableBody"></tbody>
            </table>
        </div>
    </div>
</div>

<div class="bill-detail-popup-overlay" id="billDetailPopupOverlay">
    <div class="bill-detail-popup-panel">
        <div class="bill-detail-header no-print">
            <div class="bill-detail-title">View Bill</div>
            <button type="button" class="bill-close-x" onclick="closeBillDetailPopup()">✕</button>
        </div>

        <div class="bill-detail-scroll">
            <div class="bill-detail-content" id="billDetailPrintArea">
                <div class="bill-logo-wrap">
                    <img
                        src="https://littleduckling.asia/wp-content/uploads/2024/03/Logo-Little-Duckling-2.png"
                        alt="logo"
                        class="bill-logo"
                        onerror="this.style.display='none'">
                </div>

                <div class="bill-main-title" id="detailTableOrder">Table/Order:LD0002598</div>
                <div class="bill-sub-title">Bill</div>

                <div class="bill-meta">
                    <div>Bill No: <span id="detailBillNo">-</span></div>
                    <div>Date: <span id="detailDate">-</span></div>
                    <div>Printing At: <span id="detailPrintingAt">-</span></div>
                    <div>Order Man: <span id="detailOrderMan">-</span></div>
                    <div>Opened At: <span id="detailOpenedAt">-</span></div>
                    <div>Guest count: <span id="detailGuestCount">0</span></div>
                    <div>Station: <span id="detailStation">-</span></div>
                    <div>Delivery time: <span id="detailDeliveryTime">-</span></div>
                </div>

                <div class="bill-section-title">CUSTOMER INFORMATION</div>
                <div class="bill-line"></div>

                <div class="bill-customer-info">
                    <div>Customer Type: <span id="detailCustomerType">-</span></div>
                    <div>Tel/RegNo: <span id="detailTel">-</span></div>
                    <div>Customer Name: <span id="detailCustomerName">-</span></div>
                    <div>Address: <span id="detailAddress">-</span></div>
                    <div>ExtraInfo: <span id="detailExtraInfo">-</span></div>
                    <div>Note: <span id="detailNote">-</span></div>
                </div>

                <table class="bill-detail-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th style="width:80px;">Qty</th>
                        </tr>
                    </thead>
                    <tbody id="billDetailItemsBody"></tbody>
                </table>
            </div>
        </div>

        <div class="bill-detail-footer no-print">
            <button type="button" class="bill-action-btn" onclick="printBillDetail()">
                <i class="fa-solid fa-print"></i> Print
            </button>
            <button type="button" class="bill-action-btn" onclick="windowPrintBillDetail()">
                <i class="fa-solid fa-print"></i> Window Print
            </button>
        </div>
    </div>
</div>

<style>
.action-wrap{display:flex;align-items:center;gap:8px}
.view-btn,.dots-btn{width:34px;height:34px;border:none;border-radius:8px;background:#f3f6fb;color:#4b5563;cursor:pointer;display:flex;align-items:center;justify-content:center}
.view-btn:hover,.dots-btn:hover{background:#e8eef8}
.order-popup-overlay,.bill-list-popup-overlay,.bill-detail-popup-overlay{position:fixed;inset:0;background:rgba(0,0,0,.28);z-index:9999;display:none}
.order-popup-overlay.show,.bill-list-popup-overlay.show,.bill-detail-popup-overlay.show{display:block}
.order-popup-panel{position:absolute;top:0;right:-100%;width:72%;max-width:1180px;height:100%;background:#fff;transition:all .25s ease;overflow-y:auto;padding:24px 28px}
.order-popup-overlay.show .order-popup-panel{right:0}
.popup-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px}
.popup-title{font-size:19px;font-weight:700;color:#374151;margin-bottom:6px}
.confirm-text{font-weight:600}
.popup-dates{font-size:14px;color:#555}
.popup-dates span{color:#ff7a00;font-weight:600}
.popup-close-x{border:none;background:transparent;color:#ff4d4f;font-size:28px;cursor:pointer;line-height:1}
.popup-body-grid{display:grid;grid-template-columns:2fr 1fr;gap:18px}
.popup-card,.popup-map-card{border:1px solid #dfe5ec;border-radius:8px;background:#fff;margin-bottom:18px}
.popup-card{padding:14px}
.popup-card-header{display:flex;justify-content:space-between;align-items:center;font-size:15px;font-weight:700;color:#475569;margin-bottom:14px}
.order-item-box{display:flex;gap:14px;align-items:flex-start}
.order-item-box img{width:66px;height:66px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb}
.order-item-info{flex:1}
.item-code{font-size:13px;color:#8b95a7;margin-bottom:3px}
.item-name{font-size:16px;font-weight:700;color:#374151;margin-bottom:4px}
.item-desc{font-size:13px;color:#8b95a7;line-height:1.5}
.order-item-price{min-width:120px;text-align:right;font-weight:600;color:#4b5563}
.discount-orange{color:#ff6a00;font-weight:700;margin:6px 0}
.summary-row,.payment-row{display:grid;grid-template-columns:1fr 100px 120px;gap:10px;padding:4px 0;color:#4b5563;font-size:15px}
.total-row{font-weight:700}
.simple-value{color:#4b5563;font-size:15px;line-height:1.6}
.info-line{display:flex;gap:10px;align-items:flex-start;color:#4b5563;margin-bottom:12px;font-size:14px}
.info-line i{width:16px;margin-top:3px;color:#111827}
.info-line a{color:#2563eb;text-decoration:none}
.mt8{margin-top:8px}
.popup-footer{display:flex;justify-content:flex-end;gap:12px;margin-top:10px;padding-bottom:16px}
.food-ready-btn{background:#f8fbff;border:1px solid #94bdf2;color:#2b63d9;border-radius:8px;padding:10px 16px;cursor:pointer;font-weight:600}
.close-popup-btn{background:#60aeea;border:none;color:#fff;border-radius:8px;padding:10px 18px;cursor:pointer;font-weight:600}
.status-badge-order{display:inline-block;padding:4px 10px;border-radius:20px;background:#e8f0ff;color:#2b63d9;font-size:12px;font-weight:600;white-space:nowrap}
.action-dropdown{position:relative}
.action-menu{display:none;position:absolute;right:0;top:40px;min-width:170px;background:#fff;border:1px solid #ddd;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.08);z-index:99999;overflow:hidden}
.action-menu a{display:block;padding:10px 14px;text-decoration:none;color:#333;font-size:14px;border-bottom:1px solid #f1f1f1}
.action-menu a:hover{background:#f7f7f7}
.action-menu form{margin:0}
.action-menu form button{width:100%;text-align:left;padding:10px 14px;border:none;background:#fff;cursor:pointer;font-size:14px;color:#333;border-bottom:1px solid #f1f1f1}
.action-menu form button:hover{background:#f7f7f7}
.bill-list-popup-panel{position:absolute;top:0;right:-100%;width:84%;max-width:1450px;height:100%;background:#fff;transition:all .25s ease;display:flex;flex-direction:column}
.bill-list-popup-overlay.show .bill-list-popup-panel{right:0}
.bill-list-header{display:flex;justify-content:space-between;align-items:center;padding:20px 28px;border-bottom:1px solid #e5e7eb}
.bill-list-title{font-size:20px;font-weight:700;color:#334155}
.bill-list-header-actions{display:flex;gap:12px;align-items:center}
.bill-print-all-btn,.bill-action-btn{border:none;background:#5dade2;color:#fff;border-radius:8px;padding:11px 18px;cursor:pointer;font-weight:600}
.bill-close-x{border:none;background:transparent;color:#ef4444;font-size:28px;cursor:pointer;line-height:1}
.bill-list-content{padding:18px 24px 24px;overflow:auto;flex:1}
.bill-list-table{width:100%;border-collapse:collapse}
.bill-list-table thead th{background:#f3f4f6;color:#667085;font-weight:700;font-size:14px}
.bill-list-table th,.bill-list-table td{padding:14px 12px;border-bottom:1px solid #eceff3;text-align:left;vertical-align:middle;font-size:14px}
.bill-list-table td:last-child{white-space:nowrap}
.bill-list-table .status-text{color:#111827;font-weight:600}
.bill-row-actions{display:flex;gap:8px}
.bill-row-btn{width:34px;height:34px;border:none;border-radius:8px;background:#f3f6fb;color:#4b5563;cursor:pointer}
.bill-row-btn:hover{background:#e8eef8}
.bill-detail-popup-panel{position:absolute;top:0;right:-100%;width:520px;max-width:100%;height:100%;background:#f6f6f6;transition:all .25s ease;display:flex;flex-direction:column}
.bill-detail-popup-overlay.show .bill-detail-popup-panel{right:0}
.bill-detail-header{display:flex;justify-content:space-between;align-items:center;padding:22px 26px}
.bill-detail-title{font-size:22px;font-weight:700;color:#334155}
.bill-detail-scroll{flex:1;overflow:auto;padding:0 22px 22px}
.bill-detail-content{max-width:340px;margin:0 auto;color:#111827}
.bill-logo-wrap{text-align:center;margin-top:24px;margin-bottom:12px}
.bill-logo{width:88px;max-width:100%;object-fit:contain}
.bill-main-title{text-align:center;font-size:28px;margin-top:8px;margin-bottom:4px}
.bill-sub-title{text-align:center;font-size:26px;margin-bottom:18px}
.bill-meta,.bill-customer-info{font-size:14px;line-height:1.9}
.bill-section-title{text-align:center;font-size:22px;margin-top:18px;margin-bottom:6px}
.bill-line{border-bottom:1px solid #333;margin-bottom:8px}
.bill-detail-table{width:100%;border-collapse:collapse;margin-top:14px}
.bill-detail-table th,.bill-detail-table td{padding:10px 6px;font-size:14px;border-bottom:1px solid #ddd;vertical-align:top}
.bill-detail-table thead th{border:1px solid #333;text-align:left;font-size:14px}
.bill-detail-table thead th:last-child,.bill-detail-table tbody td:last-child{text-align:right}
.bill-detail-footer{display:flex;justify-content:center;gap:24px;padding:16px 20px 24px}
@media(max-width:1200px){.order-popup-panel,.bill-list-popup-panel{width:100%;max-width:none}.bill-detail-popup-panel{width:100%}.popup-body-grid{grid-template-columns:1fr}}
@media print{
    body *{visibility:hidden}
    #billDetailPrintArea,#billDetailPrintArea *{visibility:visible}
    #billDetailPrintArea{position:absolute;left:0;top:0;width:100%;background:#fff;padding:20px}
    .no-print{display:none !important}
}
</style>

<script>
let currentBillGroups = [];
let currentBillGroup = null;

function openOrderPopup(button) {
    if (!button) return;

    const orderId = button.dataset.id || '';
    const orderNo = button.dataset.order_no || '-';
    const orderDate = button.dataset.order_date || '-';
    const scheduleDate = button.dataset.schedule_date || '-';
    const branch = button.dataset.branch || '-';
    const remark = button.dataset.remark || '-';
    const name = button.dataset.name || '-';
    const phone = button.dataset.phone || '-';
    const address = button.dataset.address || '-';
    const payment = button.dataset.payment || '-';
    const grandTotal = button.dataset.grand_total || '0.00';
    const subTotal = button.dataset.sub_total || '0.00';
    const discount = button.dataset.discount || '0.00';
    const deliveryFee = button.dataset.delivery_fee || '0.00';
    const qty = button.dataset.qty || '1';
    const itemName = button.dataset.item_name || 'Order Item';
    const description = button.dataset.description || '-';
    const image = button.dataset.image || 'https://cdn-icons-png.flaticon.com/512/1046/1046784.png';
    const chefGroup = button.dataset.chef_group || 'CP002';
    const chefStation = button.dataset.chef_station || 'LD PIG&DUCK';
    const lat = button.dataset.lat || '11.5564';
    const lng = button.dataset.lng || '104.9282';
    const statusUrl = button.dataset.status_url || ('/order/' + orderId + '/status');

    document.getElementById('popupOrderNo').innerText = orderNo;
    document.getElementById('popupOrderDate').innerText = orderDate;
    document.getElementById('popupScheduleDate').innerText = scheduleDate;
    document.getElementById('popupItemCode').innerText = orderNo;
    document.getElementById('popupItemName').innerText = itemName;
    document.getElementById('popupItemDesc').innerText = description;
    document.getElementById('popupItemImage').src = image;
    document.getElementById('popupQtyPrice').innerText = qty + ' × ' + grandTotal;
    document.getElementById('popupDiscountText').innerText = discount;
    document.getElementById('popupTotalText').innerText = grandTotal;
    document.getElementById('popupSummaryQty').innerText = qty;
    document.getElementById('popupSubTotal').innerText = subTotal;
    document.getElementById('popupDiscount').innerText = discount;
    document.getElementById('popupDeliveryFee').innerText = deliveryFee;
    document.getElementById('popupGrandTotal').innerText = grandTotal;
    document.getElementById('popupPayment').innerText = payment;
    document.getElementById('popupPaymentAmount').innerText = grandTotal;
    document.getElementById('popupBranch').innerText = branch;
    document.getElementById('popupRemark').innerText = remark;
    document.getElementById('popupCustomerName').innerText = name;
    document.getElementById('popupCustomerPhone').innerText = phone;
    document.getElementById('popupCustomerOrderDate').innerText = orderDate;
    document.getElementById('popupCustomerScheduleDate').innerText = scheduleDate;
    document.getElementById('popupAddress').innerText = address;
    document.getElementById('popupChefGroup').innerText = chefGroup;
    document.getElementById('popupChefStation').innerText = chefStation;

    const mapUrl = 'https://www.google.com/maps?q=' + lat + ',' + lng + '&z=15&output=embed';
    const mapLink = 'https://www.google.com/maps?q=' + lat + ',' + lng;

    document.getElementById('popupMap').src = mapUrl;
    document.getElementById('popupViewLocation').href = mapLink;
    document.getElementById('foodReadyForm').action = statusUrl;

    hideAllActionMenus();
    document.getElementById('orderPopupOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeOrderPopup() {
    document.getElementById('orderPopupOverlay').classList.remove('show');
    restoreBodyScroll();
}

function openPopupFromMenu(el) {
    const tr = el.closest('tr');
    if (!tr) return;

    const button = tr.querySelector('.view-btn');
    if (button) openOrderPopup(button);
}

function toggleActionMenu(id, event) {
    if (event) event.stopPropagation();

    const menu = document.getElementById('actionMenu' + id);
    if (!menu) return;

    document.querySelectorAll('.action-menu').forEach(function (item) {
        if (item.id !== 'actionMenu' + id) {
            item.style.display = 'none';
        }
    });

    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

function hideAllActionMenus() {
    document.querySelectorAll('.action-menu').forEach(function (menu) {
        menu.style.display = 'none';
    });
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function openBillListPopup(groups) {
    currentBillGroups = Array.isArray(groups) ? groups : [];

    const tbody = document.getElementById('billListTableBody');
    tbody.innerHTML = '';

    currentBillGroups.forEach(function (group, index) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${escapeHtml(group.no ?? index + 1)}</td>
            <td>${escapeHtml(group.date ?? '-')}</td>
            <td>${escapeHtml(group.group ?? '-')}</td>
            <td>${escapeHtml(group.type ?? '-')}</td>
            <td>${escapeHtml(group.order_man ?? '-')}</td>
            <td>${escapeHtml(group.customer_type ?? '-')}</td>
            <td>${escapeHtml(group.printing_at ?? '-')}</td>
            <td>${escapeHtml(group.opened_at ?? '-')}</td>
            <td>${escapeHtml(group.station ?? '-')}</td>
            <td>${escapeHtml(group.extra_info ?? '')}</td>
            <td class="status-text">${escapeHtml(group.print_status ?? '-')}</td>
            <td>${escapeHtml(group.note ?? '-')}</td>
            <td>
                <div class="bill-row-actions">
                    <button type="button" class="bill-row-btn" onclick="openBillDetailByIndex(${index})" title="View">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    <button type="button" class="bill-row-btn" onclick="printSingleGroup(${index})" title="Print">
                        <i class="fa-solid fa-print"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    hideAllActionMenus();
    document.getElementById('billListPopupOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeBillListPopup() {
    document.getElementById('billListPopupOverlay').classList.remove('show');
    restoreBodyScroll();
}

function openBillDetailByIndex(index) {
    const group = currentBillGroups[index];
    if (!group) return;

    currentBillGroup = group;

    document.getElementById('detailTableOrder').innerText = 'Table/Order:' + (group.table_order || '-');
    document.getElementById('detailBillNo').innerText = group.bill_no || '-';
    document.getElementById('detailDate').innerText = group.date || '-';
    document.getElementById('detailPrintingAt').innerText = group.printing_at || '-';
    document.getElementById('detailOrderMan').innerText = group.order_man || '-';
    document.getElementById('detailOpenedAt').innerText = group.opened_at || '-';
    document.getElementById('detailGuestCount').innerText = group.guest_count ?? 0;
    document.getElementById('detailStation').innerText = group.station || '-';
    document.getElementById('detailDeliveryTime').innerText = group.delivery_time || '-';
    document.getElementById('detailCustomerType').innerText = group.customer_type || '-';
    document.getElementById('detailTel').innerText = group.tel || '-';
    document.getElementById('detailCustomerName').innerText = group.customer_name || '-';
    document.getElementById('detailAddress').innerText = group.address || '-';
    document.getElementById('detailExtraInfo').innerText = group.extra_info || '-';
    document.getElementById('detailNote').innerText = group.note || '-';

    const itemsBody = document.getElementById('billDetailItemsBody');
    itemsBody.innerHTML = '';

    const items = Array.isArray(group.items) ? group.items : [];
    items.forEach(function (item) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${escapeHtml(item.description || '-')}</td>
            <td>${escapeHtml(item.qty ?? 1)}</td>
        `;
        itemsBody.appendChild(tr);
    });

    document.getElementById('billDetailPopupOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeBillDetailPopup() {
    document.getElementById('billDetailPopupOverlay').classList.remove('show');
    restoreBodyScroll();
}

function printBillDetail() {
    window.print();
}

function windowPrintBillDetail() {
    const area = document.getElementById('billDetailPrintArea');
    if (!area) return;

    const content = area.innerHTML;
    const printWindow = window.open('', '_blank', 'width=700,height=900');
    if (!printWindow) return;

    printWindow.document.open();
    printWindow.document.write(`
        <html>
            <head>
                <title>Print Bill</title>
                <style>
                    body{font-family:Arial,Helvetica,sans-serif;padding:20px;color:#111827}
                    .bill-detail-content{max-width:340px;margin:0 auto}
                    .bill-logo-wrap{text-align:center;margin-top:24px;margin-bottom:12px}
                    .bill-logo{width:88px;max-width:100%;object-fit:contain}
                    .bill-main-title{text-align:center;font-size:28px;margin-top:8px;margin-bottom:4px}
                    .bill-sub-title{text-align:center;font-size:26px;margin-bottom:18px}
                    .bill-meta,.bill-customer-info{font-size:14px;line-height:1.9}
                    .bill-section-title{text-align:center;font-size:22px;margin-top:18px;margin-bottom:6px}
                    .bill-line{border-bottom:1px solid #333;margin-bottom:8px}
                    .bill-detail-table{width:100%;border-collapse:collapse;margin-top:14px}
                    .bill-detail-table th,.bill-detail-table td{padding:10px 6px;font-size:14px;border-bottom:1px solid #ddd;vertical-align:top}
                    .bill-detail-table thead th{border:1px solid #333;text-align:left;font-size:14px}
                    .bill-detail-table thead th:last-child,.bill-detail-table tbody td:last-child{text-align:right}
                </style>
            </head>
            <body>
                <div class="bill-detail-content">${content}</div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

function printSingleGroup(index) {
    openBillDetailByIndex(index);
    setTimeout(function () {
        printBillDetail();
    }, 250);
}

function printAllGroups() {
    if (!Array.isArray(currentBillGroups) || !currentBillGroups.length) return;

    const printWindow = window.open('', '_blank', 'width=900,height=1100');
    if (!printWindow) return;

    let allHtml = '';

    currentBillGroups.forEach(function (group) {
        const items = Array.isArray(group.items) ? group.items : [];
        const itemsHtml = items.map(function (item) {
            return `
                <tr>
                    <td>${escapeHtml(item.description || '-')}</td>
                    <td style="text-align:right;">${escapeHtml(item.qty ?? 1)}</td>
                </tr>
            `;
        }).join('');

        allHtml += `
            <div class="bill-page">
                <div class="bill-logo-wrap">
                    <img src="https://littleduckling.asia/wp-content/uploads/2024/03/Logo-Little-Duckling-2.png" alt="logo" class="bill-logo">
                </div>

                <div class="bill-main-title">Table/Order:${escapeHtml(group.table_order || '-')}</div>
                <div class="bill-sub-title">Bill</div>

                <div class="bill-meta">
                    <div>Bill No: ${escapeHtml(group.bill_no || '-')}</div>
                    <div>Date: ${escapeHtml(group.date || '-')}</div>
                    <div>Printing At: ${escapeHtml(group.printing_at || '-')}</div>
                    <div>Order Man: ${escapeHtml(group.order_man || '-')}</div>
                    <div>Opened At: ${escapeHtml(group.opened_at || '-')}</div>
                    <div>Guest count: ${escapeHtml(group.guest_count ?? 0)}</div>
                    <div>Station: ${escapeHtml(group.station || '-')}</div>
                    <div>Delivery time: ${escapeHtml(group.delivery_time || '-')}</div>
                </div>

                <div class="bill-section-title">CUSTOMER INFORMATION</div>
                <div class="bill-line"></div>

                <div class="bill-customer-info">
                    <div>Customer Type: ${escapeHtml(group.customer_type || '-')}</div>
                    <div>Tel/RegNo: ${escapeHtml(group.tel || '-')}</div>
                    <div>Customer Name: ${escapeHtml(group.customer_name || '-')}</div>
                    <div>Address: ${escapeHtml(group.address || '-')}</div>
                    <div>ExtraInfo: ${escapeHtml(group.extra_info || '-')}</div>
                    <div>Note: ${escapeHtml(group.note || '-')}</div>
                </div>

                <table class="bill-detail-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th style="width:80px;">Qty</th>
                        </tr>
                    </thead>
                    <tbody>${itemsHtml}</tbody>
                </table>
            </div>
        `;
    });

    printWindow.document.open();
    printWindow.document.write(`
        <html>
            <head>
                <title>Print All Bills</title>
                <style>
                    body{font-family:Arial,Helvetica,sans-serif;margin:0;padding:20px;color:#111827}
                    .bill-page{max-width:340px;margin:0 auto 40px;page-break-after:always}
                    .bill-page:last-child{page-break-after:auto}
                    .bill-logo-wrap{text-align:center;margin-top:24px;margin-bottom:12px}
                    .bill-logo{width:88px;max-width:100%;object-fit:contain}
                    .bill-main-title{text-align:center;font-size:28px;margin-top:8px;margin-bottom:4px}
                    .bill-sub-title{text-align:center;font-size:26px;margin-bottom:18px}
                    .bill-meta,.bill-customer-info{font-size:14px;line-height:1.9}
                    .bill-section-title{text-align:center;font-size:22px;margin-top:18px;margin-bottom:6px}
                    .bill-line{border-bottom:1px solid #333;margin-bottom:8px}
                    .bill-detail-table{width:100%;border-collapse:collapse;margin-top:14px}
                    .bill-detail-table th,.bill-detail-table td{padding:10px 6px;font-size:14px;border-bottom:1px solid #ddd;vertical-align:top}
                    .bill-detail-table thead th{border:1px solid #333;text-align:left;font-size:14px}
                    .bill-detail-table thead th:last-child,.bill-detail-table tbody td:last-child{text-align:right}
                </style>
            </head>
            <body>${allHtml}</body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

function restoreBodyScroll() {
    const isAnyPopupOpen =
        document.getElementById('orderPopupOverlay').classList.contains('show') ||
        document.getElementById('billListPopupOverlay').classList.contains('show') ||
        document.getElementById('billDetailPopupOverlay').classList.contains('show');

    document.body.style.overflow = isAnyPopupOpen ? 'hidden' : 'auto';
}

document.getElementById('orderPopupOverlay')?.addEventListener('click', function (e) {
    if (e.target.id === 'orderPopupOverlay') closeOrderPopup();
});

document.getElementById('billListPopupOverlay')?.addEventListener('click', function (e) {
    if (e.target.id === 'billListPopupOverlay') closeBillListPopup();
});

document.getElementById('billDetailPopupOverlay')?.addEventListener('click', function (e) {
    if (e.target.id === 'billDetailPopupOverlay') closeBillDetailPopup();
});

document.addEventListener('click', function (e) {
    if (!e.target.closest('.action-dropdown')) hideAllActionMenus();
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeOrderPopup();
        closeBillListPopup();
        closeBillDetailPopup();
    }
});
</script>