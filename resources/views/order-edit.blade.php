@php
    $pageTitle = "Update Order";
    $currentPage = "order";
@endphp

@include('layout.header')
@include('layout.sidebar')

<main class="main">
    <div class="edit-wrapper">

        @if(session('success'))
            <div class="alert-success-box">{{ session('success') }}</div>
        @endif

        <form action="{{ route('order.update', $order->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="edit-header">
                <h2>Update</h2>
                <a href="{{ url()->previous() }}" class="close-btn">✕</a>
            </div>

            <div class="edit-grid">

                <div class="edit-left">

                    <div class="form-grid two-col">
                        <div class="form-group">
                            <label>Branch <span>*</span></label>
                          <select name="branch_id" class="form-control" required>
    @foreach($branches as $branch)
        <option value="{{ $branch->id }}"
            {{ $order->branch_id == $branch->id ? 'selected' : '' }}>
            {{ $branch->name }}
        </option>
    @endforeach
</select>
                        </div>

                        <div class="form-group">
                            <label>Customer Type <span>*</span></label>
                            <select name="customer_type" class="form-control" required>
                                @foreach($customerTypes as $type)
                                    <option value="{{ $type }}" {{ $order->customer_type == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-grid two-col">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $order->name) }}">
                        </div>

                        <div class="form-group">
                            <label>Phone <span>*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $order->phone) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address <span>*</span></label>
                        <textarea name="address" class="form-control" rows="3" required>{{ old('address', $order->address) }}</textarea>
                    </div>

                    <div class="form-grid two-col">
                        <div class="form-group">
                            <label>Delivery Fee</label>
                            <input type="number" step="0.01" name="delivery_fee" id="delivery_fee" class="form-control" value="{{ old('delivery_fee', $order->delivery_fee) }}">
                        </div>

                        <div class="form-group">
                            <label>Payment Type <span>*</span></label>
                            <select name="payment_status" class="form-control" required>
                                @foreach($paymentTypes as $paymentType)
                                    <option value="{{ $paymentType }}" {{ $order->payment_status == $paymentType ? 'selected' : '' }}>
                                        {{ $paymentType }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-grid two-col">
                        <div class="form-group">
                            <label>Delivery Date <span>*</span></label>
                            <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date', $order->delivery_date) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Delivery Time <span>*</span></label>
                            <input type="time" name="delivery_time" class="form-control" value="{{ old('delivery_time', $order->delivery_time) }}" required>
                        </div>
                    </div>

                    <div class="form-grid two-col">
                        <div class="form-group">
                            <label>Order Source <span>*</span></label>
                            <select name="order_source" class="form-control" required>
                                @foreach($orderSources as $source)
                                    <option value="{{ $source }}" {{ $order->order_source == $source ? 'selected' : '' }}>
                                        {{ $source }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Customer category <span>*</span></label>
                            <select name="customer_category" class="form-control" required>
                                @foreach($customerCategories as $category)
                                    <option value="{{ $category }}" {{ $order->customer_category == $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-grid two-col">
                        <div class="form-group">
                            <label>latitude & longitude</label>
                            <input type="text" name="latitude" class="form-control" placeholder="Latitude" value="{{ old('latitude', $order->latitude) }}">
                        </div>

                        <div class="form-group">
                            <label>&nbsp;</label>
                            <input type="text" name="longitude" class="form-control" placeholder="Longitude" value="{{ old('longitude', $order->longitude) }}">
                        </div>
                    </div>

                    <div class="form-grid two-col">
                        <div class="form-group">
                            <label>ExtraInfo</label>
                            <input type="text" name="extra_info" class="form-control" value="{{ old('extra_info', $order->extra_info) }}">
                        </div>

                        <div class="form-group">
                            <label>Chef Group <span>*</span></label>
                            <input type="text" name="chef_group" class="form-control" value="{{ old('chef_group', $order->chef_group) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Noted</label>
                        <textarea name="remark" class="form-control" rows="2">{{ old('remark', $order->remark) }}</textarea>
                    </div>

                </div>

                <div class="edit-right">

                    <div class="section-title">
                        <span>Item</span>
                        <button type="button" class="add-btn" onclick="addItemRow()">+</button>
                    </div>

                    <div class="table-box">
                        <table class="custom-table" id="itemTable">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Discount(%)</th>
                                    <th>Total Price</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->items as $index => $item)
                                <tr>
                                    <td>
                                        <input type="text" name="items[{{ $index }}][item_name]" class="table-input" value="{{ $item->item_name }}">
                                    </td>
                                    <td>
                                        <textarea name="items[{{ $index }}][description]" class="table-input">{{ $item->description }}</textarea>
                                    </td>
                                    <td>
                                        <input type="number" step="1" name="items[{{ $index }}][qty]" class="table-input qty" value="{{ $item->qty }}" oninput="calculateTotals()">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" class="table-input unit_price" value="{{ $item->unit_price }}" oninput="calculateTotals()">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[{{ $index }}][discount]" class="table-input discount" value="{{ $item->discount }}" oninput="calculateTotals()">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[{{ $index }}][total_price]" class="table-input total_price" value="{{ $item->total_price }}" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="remove-btn" onclick="removeRow(this)">−</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td><input type="text" name="items[0][item_name]" class="table-input"></td>
                                    <td><textarea name="items[0][description]" class="table-input"></textarea></td>
                                    <td><input type="number" step="1" name="items[0][qty]" class="table-input qty" value="1" oninput="calculateTotals()"></td>
                                    <td><input type="number" step="0.01" name="items[0][unit_price]" class="table-input unit_price" value="0" oninput="calculateTotals()"></td>
                                    <td><input type="number" step="0.01" name="items[0][discount]" class="table-input discount" value="0" oninput="calculateTotals()"></td>
                                    <td><input type="number" step="0.01" name="items[0][total_price]" class="table-input total_price" value="0" readonly></td>
                                    <td><button type="button" class="remove-btn" onclick="removeRow(this)">−</button></td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right">Total</td>
                                    <td><input type="text" id="sub_total_show" class="table-input" readonly></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Discount (%)</td>
                                    <td><input type="text" id="discount_show" class="table-input" readonly></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Delivery Fee</td>
                                    <td><input type="text" id="delivery_show" class="table-input" readonly></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right"><strong>Grand Total</strong></td>
                                    <td><input type="text" id="grand_total_show" class="table-input" readonly></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="section-title mt20">
                        <span>Payment</span>
                        <button type="button" class="add-btn" onclick="addPaymentRow()">+</button>
                    </div>

                    <div class="table-box">
                        <table class="custom-table" id="paymentTable">
                            <thead>
                                <tr>
                                    <th>Payment method</th>
                                    <th>Remark</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->payments as $index => $payment)
                                <tr>
                                    <td>
                                        <select name="payments[{{ $index }}][payment_method]" class="table-input">
                                            @foreach($paymentMethods as $method)
                                                <option value="{{ $method }}" {{ $payment->payment_method == $method ? 'selected' : '' }}>
                                                    {{ $method }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <textarea name="payments[{{ $index }}][remark]" class="table-input">{{ $payment->remark }}</textarea>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="payments[{{ $index }}][amount]" class="table-input payment_amount" value="{{ $payment->amount }}">
                                    </td>
                                    <td>
                                        <button type="button" class="remove-btn" onclick="removeRow(this)">−</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td>
                                        <select name="payments[0][payment_method]" class="table-input">
                                            @foreach($paymentMethods as $method)
                                                <option value="{{ $method }}">{{ $method }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><textarea name="payments[0][remark]" class="table-input"></textarea></td>
                                    <td><input type="number" step="0.01" name="payments[0][amount]" class="table-input payment_amount" value="{{ $order->grand_total ?? 0 }}"></td>
                                    <td><button type="button" class="remove-btn" onclick="removeRow(this)">−</button></td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right"><strong>Grand Total</strong></td>
                                    <td><input type="text" id="payment_grand_total_show" class="table-input" readonly></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>

            <div class="bottom-action">
                <button type="submit" class="update-btn">
                    <i class="fa fa-save"></i> Update
                </button>
            </div>
        </form>

    </div>
</main>

<style>
.edit-wrapper{
    padding:24px;
    background:#f5f5f5;
    min-height:100vh;
}
.edit-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}
.edit-header h2{
    font-size:18px;
    font-weight:700;
    margin:0;
}
.close-btn{
    font-size:28px;
    color:#ff4d4f;
    text-decoration:none;
}
.edit-grid{
    display:grid;
    grid-template-columns:40% 60%;
    gap:14px;
}
.form-grid.two-col{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
}
.form-group{
    margin-bottom:12px;
}
.form-group label{
    display:block;
    font-weight:600;
    margin-bottom:6px;
    color:#4b5563;
}
.form-group label span{
    color:red;
}
.form-control{
    width:100%;
    height:42px;
    border:1px solid #d1d5db;
    border-radius:4px;
    padding:8px 12px;
    font-size:14px;
    background:#fff;
}
textarea.form-control{
    height:auto;
    resize:vertical;
}
.section-title{
    display:flex;
    justify-content:space-between;
    align-items:center;
    font-weight:700;
    margin-bottom:8px;
}
.table-box{
    overflow:auto;
    background:#fff;
}
.custom-table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
}
.custom-table th{
    background:#59a5d8;
    color:#fff;
    padding:10px;
    border:1px solid #cfd8e3;
    font-size:14px;
}
.custom-table td{
    border:1px solid #d8dee8;
    padding:0;
    vertical-align:middle;
}
.table-input{
    width:100%;
    border:none;
    padding:10px 12px;
    outline:none;
    font-size:14px;
    min-height:42px;
    background:#fff;
}
textarea.table-input{
    resize:vertical;
    min-height:60px;
}
.add-btn,
.remove-btn{
    width:28px;
    height:28px;
    border-radius:50%;
    border:1px solid #333;
    background:#fff;
    cursor:pointer;
    font-size:18px;
    line-height:1;
}
.text-right{
    text-align:right;
    padding-right:10px !important;
    font-weight:600;
}
.mt20{
    margin-top:18px;
}
.bottom-action{
    display:flex;
    justify-content:flex-end;
    margin-top:16px;
}
.update-btn{
    background:#59a5d8;
    color:#fff;
    border:none;
    padding:10px 20px;
    border-radius:4px;
    cursor:pointer;
    font-size:15px;
}
.alert-success-box{
    background:#dcfce7;
    color:#166534;
    padding:12px 16px;
    margin-bottom:16px;
    border-radius:6px;
}
@media(max-width:1200px){
    .edit-grid{
        grid-template-columns:1fr;
    }
    .form-grid.two-col{
        grid-template-columns:1fr;
    }
}
</style>

<script>
let itemIndex = {{ count($order->items) > 0 ? count($order->items) : 1 }};
let paymentIndex = {{ count($order->payments) > 0 ? count($order->payments) : 1 }};

function addItemRow(){
    let tbody = document.querySelector('#itemTable tbody');
    let row = document.createElement('tr');

    row.innerHTML = `
        <td><input type="text" name="items[${itemIndex}][item_name]" class="table-input"></td>
        <td><textarea name="items[${itemIndex}][description]" class="table-input"></textarea></td>
        <td><input type="number" step="1" name="items[${itemIndex}][qty]" class="table-input qty" value="1" oninput="calculateTotals()"></td>
        <td><input type="number" step="0.01" name="items[${itemIndex}][unit_price]" class="table-input unit_price" value="0" oninput="calculateTotals()"></td>
        <td><input type="number" step="0.01" name="items[${itemIndex}][discount]" class="table-input discount" value="0" oninput="calculateTotals()"></td>
        <td><input type="number" step="0.01" name="items[${itemIndex}][total_price]" class="table-input total_price" value="0" readonly></td>
        <td><button type="button" class="remove-btn" onclick="removeRow(this)">−</button></td>
    `;

    tbody.appendChild(row);
    itemIndex++;
    calculateTotals();
}

function addPaymentRow(){
    let tbody = document.querySelector('#paymentTable tbody');
    let row = document.createElement('tr');

    row.innerHTML = `
        <td>
            <select name="payments[${paymentIndex}][payment_method]" class="table-input">
                <option value="Cash">Cash</option>
                <option value="ABA">ABA</option>
                <option value="AMK">AMK</option>
                <option value="ACLEDA">ACLEDA</option>
            </select>
        </td>
        <td><textarea name="payments[${paymentIndex}][remark]" class="table-input"></textarea></td>
        <td><input type="number" step="0.01" name="payments[${paymentIndex}][amount]" class="table-input payment_amount" value="0"></td>
        <td><button type="button" class="remove-btn" onclick="removeRow(this)">−</button></td>
    `;

    tbody.appendChild(row);
    paymentIndex++;
}

function removeRow(button){
    button.closest('tr').remove();
    calculateTotals();
}

function calculateTotals(){
    let rows = document.querySelectorAll('#itemTable tbody tr');
    let subTotal = 0;
    let discountTotal = 0;

    rows.forEach(function(row){
        let qty = parseFloat(row.querySelector('.qty')?.value || 0);
        let unitPrice = parseFloat(row.querySelector('.unit_price')?.value || 0);
        let discount = parseFloat(row.querySelector('.discount')?.value || 0);

        let lineTotal = qty * unitPrice;
        let discountAmount = (lineTotal * discount) / 100;
        let finalTotal = lineTotal - discountAmount;

        let totalPriceInput = row.querySelector('.total_price');
        if(totalPriceInput){
            totalPriceInput.value = finalTotal.toFixed(2);
        }

        subTotal += lineTotal;
        discountTotal += discountAmount;
    });

    let deliveryFee = parseFloat(document.getElementById('delivery_fee')?.value || 0);
    let grandTotal = subTotal - discountTotal + deliveryFee;

    document.getElementById('sub_total_show').value = subTotal.toFixed(2);
    document.getElementById('discount_show').value = discountTotal.toFixed(2);
    document.getElementById('delivery_show').value = deliveryFee.toFixed(2);
    document.getElementById('grand_total_show').value = grandTotal.toFixed(2);
    document.getElementById('payment_grand_total_show').value = grandTotal.toFixed(2);

    let firstPayment = document.querySelector('.payment_amount');
    if(firstPayment){
        firstPayment.value = grandTotal.toFixed(2);
    }
}

document.getElementById('delivery_fee').addEventListener('input', calculateTotals);
window.addEventListener('load', calculateTotals);
</script>

@include('layout.footer')