<script>
let currentBillGroups = [];

function openOrderPopup(button) {
    if (!button) return;

    const orderNo = button.dataset.order_no || '-';
    const qty = parseInt(button.dataset.qty) || 1;
    const price = parseFloat(button.dataset.price) || 0.00;
    const discount = parseFloat(button.dataset.discount) || 0.00;
    const subTotal = parseFloat(button.dataset.sub_total) || 0.00;
    const deliveryFee = parseFloat(button.dataset.delivery_fee) || 0.00;
    const grandTotal = parseFloat(button.dataset.grand_total) || 0.00;

    // បំពេញទិន្នន័យទូទៅ
    document.getElementById('popupOrderNo').innerText = orderNo;
    document.getElementById('popupOrderDate').innerText = button.dataset.order_date || '-';
    document.getElementById('popupScheduleDate').innerText = button.dataset.schedule_date || '-';
    document.getElementById('popupItemCode').innerText = orderNo;
    
    // 🛠️ FIX ត្រង់នេះ៖ បង្ហាញឈ្មោះទំនិញ និងរូបភាពពិតប្រាកដទៅក្នុង Popup
    document.getElementById('popupItemName').innerText = button.dataset.item_name || 'Order Item';
    document.getElementById('popupItemDesc').innerText = button.dataset.description || '-';
    document.getElementById('popupItemImage').src = button.dataset.image || 'https://cdn-icons-png.flaticon.com/512/1046/1046784.png';
    
    // គណនាតម្លៃ
    document.getElementById('popupQtyPrice').innerText = qty + ' × ' + price.toFixed(2);
    document.getElementById('popupDiscountText').innerText = discount > 0 ? '-' + discount.toFixed(2) : '0.00';
    document.getElementById('popupTotalText').innerText = (qty * price).toFixed(2);
    
    document.getElementById('popupSummaryQty').innerText = qty;
    document.getElementById('popupSubTotal').innerText = subTotal.toFixed(2);
    document.getElementById('popupDiscount').innerText = discount.toFixed(2);
    document.getElementById('popupDeliveryFee').innerText = deliveryFee.toFixed(2);
    document.getElementById('popupGrandTotal').innerText = grandTotal.toFixed(2);
    
    document.getElementById('popupPayment').innerText = button.dataset.payment || '-';
    document.getElementById('popupPaymentAmount').innerText = grandTotal.toFixed(2);
    document.getElementById('popupBranch').innerText = button.dataset.branch || '-';
    document.getElementById('popupRemark').innerText = button.dataset.remark || '-';
    document.getElementById('popupCustomerName').innerText = button.dataset.name || '-';
    document.getElementById('popupCustomerPhone').innerText = button.dataset.phone || '-';
    document.getElementById('popupCustomerOrderDate').innerText = button.dataset.order_date || '-';
    document.getElementById('popupCustomerScheduleDate').innerText = button.dataset.schedule_date || '-';
    document.getElementById('popupAddress').innerText = button.dataset.address || '-';

    // Google Maps Link & Embedded
    const lat = button.dataset.lat || '11.5564';
    const lng = button.dataset.lng || '104.9282';
    document.getElementById('popupMap').src = `https://maps.google.com/maps?q=${encodeURIComponent(lat)},${encodeURIComponent(lng)}&z=15&output=embed`;
    document.getElementById('popupViewLocation').href = `https://www.google.com/maps?q=${lat},${lng}`;
    document.getElementById('foodReadyForm').action = button.dataset.status_url || '';

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
    if (tr) {
        const btn = tr.querySelector('.view-btn');
        if (btn) openOrderPopup(btn);
    }
}

function toggleActionMenu(id, event) {
    if (event) event.stopPropagation();
    const menu = document.getElementById('actionMenu' + id);
    if (!menu) return;

    document.querySelectorAll('.action-menu').forEach(item => {
        if (item.id !== 'actionMenu' + id) item.style.display = 'none';
    });
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

function hideAllActionMenus() {
    document.querySelectorAll('.action-menu').forEach(menu => menu.style.display = 'none');
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

    currentBillGroups.forEach((group, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${escapeHtml(group.no ?? index + 1)}</td>
            <td>${escapeHtml(group.date ?? '-')}</td>
            <td>${escapeHtml(group.group ?? '-')}</td>
            <td>${escapeHtml(group.type ?? '-')}</td>
            <td>${escapeHtml(group.order_man ?? '-')}</td>
            <td>${escapeHtml(group.print_status ?? '-')}</td>
            <td>
                <button type="button" class="bill-row-btn" onclick="openBillDetailByIndex(${index})"><i class="fa-solid fa-eye"></i></button>
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

    document.getElementById('detailTableOrder').innerText = 'Table/Order:' + (group.table_order || '-');
    document.getElementById('detailBillNo').innerText = group.bill_no || '-';
    document.getElementById('detailDate').innerText = group.date || '-';
    document.getElementById('detailOrderMan').innerText = group.order_man || '-';
    document.getElementById('detailCustomerName').innerText = group.customer_name || '-';
    document.getElementById('detailTel').innerText = group.tel || '-';
    document.getElementById('detailAddress').innerText = group.address || '-';

    const itemsBody = document.getElementById('billDetailItemsBody');
    itemsBody.innerHTML = '';
    (group.items || []).forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${escapeHtml(item.description)}</td><td>${escapeHtml(item.qty)}</td>`;
        itemsBody.appendChild(tr);
    });

    document.getElementById('billDetailPopupOverlay').classList.add('show');
}

function closeBillDetailPopup() {
    document.getElementById('billDetailPopupOverlay').classList.remove('show');
    restoreBodyScroll();
}

function printBillDetail() {
    window.print();
}

function restoreBodyScroll() {
    const open = document.querySelector('.order-popup-overlay.show, .bill-list-popup-overlay.show');
    document.body.style.overflow = open ? 'hidden' : 'auto';
}

document.addEventListener('click', e => {
    if (!e.target.closest('.action-dropdown')) hideAllActionMenus();
});
</script>