{{-- resources/views/order/partials/popups.blade.php --}}

{{-- ==================== POPUP VIEW DETAILS ==================== --}}
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
                    <iframe id="popupMap" width="100%" height="230" style="border:0;border-radius:10px;" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" src=""></iframe>
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

{{-- ==================== BILL LIST POPUP ==================== --}}
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
                        <th>Print Status</th>
                        <th style="width:90px;">Action</th>
                    </tr>
                </thead>
                <tbody id="billListTableBody"></tbody>
            </table>
        </div>
    </div>
</div>

{{-- ==================== BILL DETAIL POPUP ==================== --}}
<div class="bill-detail-popup-overlay" id="billDetailPopupOverlay">
    <div class="bill-detail-popup-panel">
        <div class="bill-detail-header no-print">
            <div class="bill-detail-title">View Bill Detail</div>
            <button type="button" class="bill-close-x" onclick="closeBillDetailPopup()">✕</button>
        </div>
        <div class="bill-detail-scroll">
            <div class="bill-detail-content" id="billDetailPrintArea">
                <div class="bill-logo-wrap">
                    <img src="https://littleduckling.asia/wp-content/uploads/2024/03/Logo-Little-Duckling-2.png" alt="logo" class="bill-logo">
                </div>
                <div class="bill-main-title" id="detailTableOrder">Table/Order:-</div>
                <div class="bill-sub-title">Bill</div>
                <div class="bill-meta">
                    <div>Bill No: <span id="detailBillNo">-</span></div>
                    <div>Date: <span id="detailDate">-</span></div>
                    <div>Order Man: <span id="detailOrderMan">-</span></div>
                </div>
                <div class="bill-section-title">CUSTOMER INFORMATION</div>
                <div class="bill-line"></div>
                <div class="bill-customer-info">
                    <div>Customer Name: <span id="detailCustomerName">-</span></div>
                    <div>Tel: <span id="detailTel">-</span></div>
                    <div>Address: <span id="detailAddress">-</span></div>
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
        </div>
    </div>
</div>