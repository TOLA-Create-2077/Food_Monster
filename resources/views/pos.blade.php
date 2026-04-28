{{-- resources/views/pos/index.blade.php --}}
@php
    $pageTitle = 'POS';
    $currentPage = 'pos';
@endphp

@include('layout.header')
@include('layout.sidebar')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<main class="main">
    <div class="page-shell">
        <div class="container-fluid page-card p-0">
            @if(session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('pos.store') }}">
                @csrf

                <div class="row grid-gap">
                    <div class="col-12 col-xl-5">
                        <div class="search-box">
                            <div class="section-title">Customer Info</div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label label-required">Branch</label>
                                    <select name="branch_id" class="form-select" required>
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label label-required">Customer Type</label>
                                    <select name="customer_type" class="form-select" required>
                                        <option value="General" {{ old('customer_type', 'General') == 'General' ? 'selected' : '' }}>General</option>
                                        <option value="VIP" {{ old('customer_type') == 'VIP' ? 'selected' : '' }}>VIP</option>
                                        <option value="Wholesale" {{ old('customer_type') == 'Wholesale' ? 'selected' : '' }}>Wholesale</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter name">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label label-required">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Enter phone number" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control" placeholder="Enter address">{{ old('address') }}</textarea>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Delivery Fee</label>
                                    <input type="number" step="0.01" min="0" name="delivery_fee" id="delivery_fee" class="form-control" value="{{ old('delivery_fee', 0) }}" placeholder="Enter delivery fee">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Delivery Date</label>
                                    <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date') }}">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Delivery Time</label>
                                    <input type="time" name="delivery_time" class="form-control" value="{{ old('delivery_time') }}">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Chef Group</label>
                                    <input type="text" name="chef_group" class="form-control" value="{{ old('chef_group') }}" placeholder="Chef Group">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Order Source</label>
                                    <select name="order_source" class="form-select">
                                        <option value="Facebook" {{ old('order_source', 'Facebook') == 'Facebook' ? 'selected' : '' }}>Facebook</option>
                                        <option value="Telegram" {{ old('order_source') == 'Telegram' ? 'selected' : '' }}>Telegram</option>
                                        <option value="Walk In" {{ old('order_source') == 'Walk In' ? 'selected' : '' }}>Walk In</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Customer Category</label>
                                    <select name="customer_category" class="form-select">
                                        <option value="New" {{ old('customer_category', 'New') == 'New' ? 'selected' : '' }}>New</option>
                                        <option value="Old" {{ old('customer_category') == 'Old' ? 'selected' : '' }}>Old</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Latitude</label>
                                    <input type="text" name="lat" class="form-control" value="{{ old('lat') }}" placeholder="Latitude">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Longitude</label>
                                    <input type="text" name="lng" class="form-control" value="{{ old('lng') }}" placeholder="Longitude">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Extra Info</label>
                                    <input type="text" name="extra_info" class="form-control" value="{{ old('extra_info') }}" placeholder="Enter extra info">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Note</label>
                                    <textarea name="note" class="form-control" placeholder="Enter note">{{ old('note') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-7">
                        <div class="table-panel">
                            <div class="table-panel-title d-flex justify-content-between align-items-center">
                                <span>Items</span>
                                <button class="circle-icon-btn" type="button" id="add-item-btn">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>

                            <div class="table-scroll">
                                <table class="table-like" id="items-table">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Description</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Discount(%)</th>
                                            <th>Total Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-body">
                                        <tr class="item-row">
                                            <td>
                                                <input type="hidden" name="items[0][product_id]" class="product-id-input" value="{{ old('items.0.product_id') }}">
                                                <button type="button" class="product-picker-trigger open-product-modal-btn">
                                                    <span class="{{ old('items.0.product_id') ? 'selected' : 'placeholder' }} product-picker-label">
                                                        {{ old('items.0.product_id') ? 'Selected product' : 'Select Product' }}
                                                    </span>
                                                    <i class="fa-solid fa-magnifying-glass"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <textarea name="items[0][description]" class="form-control description-input" placeholder="Enter description">{{ old('items.0.description') }}</textarea>
                                            </td>
                                            <td>
                                                <input name="items[0][qty]" type="number" step="0.01" min="1" value="{{ old('items.0.qty', 1) }}" class="form-control qty-input" required>
                                            </td>
                                            <td>
                                                <input name="items[0][price]" type="number" step="0.01" min="0" value="{{ old('items.0.price', 0) }}" class="form-control price-input" required>
                                            </td>
                                            <td>
                                                <input name="items[0][discount]" type="number" step="0.01" min="0" max="100" value="{{ old('items.0.discount', 0) }}" class="form-control discount-input">
                                            </td>
                                            <td>
                                                <input name="items[0][total]" type="number" step="0.01" min="0" value="{{ old('items.0.total', 0) }}" class="form-control total-input" readonly>
                                            </td>
                                            <td class="action-cell">
                                                <button class="circle-icon-btn remove-item-btn" type="button">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4"></td>
                                            <td class="summary-label">Subtotal</td>
                                            <td class="summary-value">
                                                <input type="number" step="0.01" min="0" name="subtotal" id="subtotal" class="form-control" value="{{ old('subtotal', 0) }}" readonly>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"></td>
                                            <td class="summary-label">Discount</td>
                                            <td class="summary-value">
                                                <input type="number" step="0.01" min="0" name="discount" id="discount_total" class="form-control" value="{{ old('discount', 0) }}" readonly>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"></td>
                                            <td class="summary-label">Delivery Fee</td>
                                            <td class="summary-value">
                                                <input type="number" step="0.01" min="0" id="delivery_fee_display" class="form-control" value="{{ old('delivery_fee', 0) }}" readonly>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"></td>
                                            <td class="summary-label">Grand Total</td>
                                            <td class="summary-value">
                                                <input type="number" step="0.01" min="0" name="grand_total" id="grand_total" class="form-control" value="{{ old('grand_total', 0) }}" readonly>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="table-panel mt-3">
                            <div class="table-panel-title d-flex justify-content-between align-items-center">
                                <span>Payment</span>
                                <button class="circle-icon-btn" type="button" id="add-payment-btn">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>

                            <div class="table-scroll">
                                <table class="table-like payment-table">
                                    <thead>
                                        <tr>
                                            <th>Payment method</th>
                                            <th>Remark</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="payments-body">
                                        <tr class="payment-row">
                                            <td>
                                                <select name="payments[0][method]" class="form-select">
                                                    <option value="">Select payment method</option>
                                                    <option value="Cash" {{ old('payments.0.method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                                    <option value="ABA" {{ old('payments.0.method') == 'ABA' ? 'selected' : '' }}>ABA</option>
                                                    <option value="Card" {{ old('payments.0.method') == 'Card' ? 'selected' : '' }}>Card</option>
                                                </select>
                                            </td>
                                            <td>
                                                <textarea name="payments[0][remark]" class="form-control" placeholder="Enter remark">{{ old('payments.0.remark') }}</textarea>
                                            </td>
                                            <td>
                                                <input name="payments[0][amount]" type="number" step="0.01" min="0" class="form-control" placeholder="Enter amount" value="{{ old('payments.0.amount') }}">
                                            </td>
                                            <td class="action-cell">
                                                <button class="circle-icon-btn remove-payment-btn" type="button">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="footer-actions mt-3">
                            <button type="submit" class="btn btn-primary-soft create-btn">
                                <i class="fa-regular fa-floppy-disk me-1"></i>
                                Create Order
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<div class="product-modal" id="productModal">
    <div class="product-modal-dialog">
        <div class="product-modal-header">
            <h3 class="product-modal-title">Product</h3>
            <button type="button" class="product-modal-close" id="closeProductModalBtn">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="product-modal-body">
            <div class="product-modal-filters">
                <input type="text" id="productSearchInput" class="form-control" placeholder="Search...">

                <select id="productCategoryFilter" class="form-select">
                    <option value="">Select category</option>
                    @php
                        $categories = collect($products)
                            ->map(fn ($product) => data_get($product, 'category.name', data_get($product, 'category_name', '')))
                            ->filter()
                            ->unique()
                            ->values();
                    @endphp
                    @foreach($categories as $categoryName)
                        <option value="{{ strtolower($categoryName) }}">{{ $categoryName }}</option>
                    @endforeach
                </select>

                <select id="productTypeFilter" class="form-select">
                    <option value="">All</option>
                    @php
                        $types = collect($products)
                            ->map(fn ($product) => data_get($product, 'type', ''))
                            ->filter()
                            ->unique()
                            ->values();
                    @endphp
                    @foreach($types as $typeName)
                        <option value="{{ strtolower($typeName) }}">{{ $typeName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="product-results">
                <div class="product-results-head">
                    <div>Image</div>
                    <div>Title</div>
                    <div>Type</div>
                    <div>Price</div>
                    <div>Description</div>
                </div>

                <div class="product-results-list" id="productResultsList">
                    @foreach($products as $product)
                        @php
                            $title = data_get($product, 'title_en')
                                ?? data_get($product, 'title')
                                ?? 'Untitled';

                            $description = data_get($product, 'description')
                                ?? data_get($product, 'description_en')
                                ?? $title;

                            $type = data_get($product, 'type', 'Normal');
                            $category = data_get($product, 'category.name')
                                ?? data_get($product, 'category_name')
                                ?? '';

                            $image = data_get($product, 'image')
                                ?? data_get($product, 'thumbnail')
                                ?? '';

                            $price = (float) data_get($product, 'price', 0);
                            $productId = data_get($product, 'id', '');
                        @endphp

                        <div
                            class="product-row-card"
                            data-id="{{ $productId }}"
                            data-name="{{ $title }}"
                            data-price="{{ $price }}"
                            data-description="{{ $description }}"
                            data-type="{{ strtolower($type) }}"
                            data-category="{{ strtolower($category) }}"
                            data-image="{{ $image }}"
                        >
                            <div class="product-image-box">
                                @if($image)
                                    <img src="{{ asset($image) }}" alt="{{ $title }}">
                                @else
                                    <i class="fa-regular fa-image"></i>
                                @endif
                            </div>

                            <div>
                                <div class="product-title-name">{{ $title }}</div>
                            </div>

                            <div class="product-type">{{ $type }}</div>
                            <div class="product-price">{{ number_format($price, 2) }}</div>
                            <div class="product-description">{{ $description }}</div>
                        </div>
                    @endforeach

                    <div id="productEmptyState" class="product-empty d-none">
                        No products found.
                    </div>
                </div>
            </div>

            <div class="product-modal-footer">
                <button type="button" class="product-unselect-btn" id="productUnselectBtn">Unselect</button>
            </div>
        </div>
    </div>
</div>

<script>
    let itemIndex = document.querySelectorAll('.item-row').length;
    let paymentIndex = document.querySelectorAll('.payment-row').length;
    let activeItemRow = null;

    const productModal = document.getElementById('productModal');
    const closeProductModalBtn = document.getElementById('closeProductModalBtn');
    const productSearchInput = document.getElementById('productSearchInput');
    const productCategoryFilter = document.getElementById('productCategoryFilter');
    const productTypeFilter = document.getElementById('productTypeFilter');
    const productResultsList = document.getElementById('productResultsList');
    const productEmptyState = document.getElementById('productEmptyState');
    const productUnselectBtn = document.getElementById('productUnselectBtn');
    const deliveryFeeInput = document.getElementById('delivery_fee');
    const addItemBtn = document.getElementById('add-item-btn');
    const addPaymentBtn = document.getElementById('add-payment-btn');
    const itemsBody = document.getElementById('items-body');
    const paymentsBody = document.getElementById('payments-body');

    function toNumber(value) {
        const number = parseFloat(value);
        return Number.isNaN(number) ? 0 : number;
    }

    function clampDiscount(value) {
        const number = toNumber(value);
        if (number < 0) return 0;
        if (number > 100) return 100;
        return number;
    }

    function recalculateRow(row) {
        if (!row) return;

        const qtyInput = row.querySelector('.qty-input');
        const priceInput = row.querySelector('.price-input');
        const discountInput = row.querySelector('.discount-input');
        const totalInput = row.querySelector('.total-input');

        if (!qtyInput || !priceInput || !discountInput || !totalInput) return;

        const qty = Math.max(1, toNumber(qtyInput.value));
        const price = Math.max(0, toNumber(priceInput.value));
        const discount = clampDiscount(discountInput.value);

        qtyInput.value = qty;
        priceInput.value = price.toFixed(2);
        discountInput.value = discount;

        const gross = qty * price;
        const discountAmount = gross * (discount / 100);
        const total = gross - discountAmount;

        totalInput.value = total.toFixed(2);
        recalculateSummary();
    }

    function recalculateSummary() {
        let subtotal = 0;
        let discountTotal = 0;

        document.querySelectorAll('.item-row').forEach(function (row) {
            const productId = row.querySelector('.product-id-input')?.value || '';
            if (!productId) return;

            const qty = Math.max(1, toNumber(row.querySelector('.qty-input')?.value));
            const price = Math.max(0, toNumber(row.querySelector('.price-input')?.value));
            const discount = clampDiscount(row.querySelector('.discount-input')?.value);

            const gross = qty * price;
            const discountAmount = gross * (discount / 100);

            subtotal += gross;
            discountTotal += discountAmount;
        });

        const deliveryFee = Math.max(0, toNumber(deliveryFeeInput?.value));

        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('discount_total').value = discountTotal.toFixed(2);
        document.getElementById('delivery_fee_display').value = deliveryFee.toFixed(2);
        document.getElementById('grand_total').value = (subtotal - discountTotal + deliveryFee).toFixed(2);
    }

    function setProductToRow(row, productData) {
        if (!row) return;

        const productIdInput = row.querySelector('.product-id-input');
        const productLabel = row.querySelector('.product-picker-label');
        const descriptionInput = row.querySelector('.description-input');
        const priceInput = row.querySelector('.price-input');

        if (productIdInput) productIdInput.value = productData.id || '';

        if (productLabel) {
            productLabel.textContent = productData.name || 'Select Product';
            productLabel.classList.remove('placeholder');
            productLabel.classList.add('selected');
        }

        if (priceInput) {
            priceInput.value = toNumber(productData.price).toFixed(2);
        }

        if (descriptionInput && !descriptionInput.value.trim()) {
            descriptionInput.value = productData.description || '';
        }

        recalculateRow(row);
    }

    function resetRow(row) {
        if (!row) return;

        const productIdInput = row.querySelector('.product-id-input');
        const productLabel = row.querySelector('.product-picker-label');
        const descriptionInput = row.querySelector('.description-input');
        const qtyInput = row.querySelector('.qty-input');
        const priceInput = row.querySelector('.price-input');
        const discountInput = row.querySelector('.discount-input');
        const totalInput = row.querySelector('.total-input');

        if (productIdInput) productIdInput.value = '';

        if (productLabel) {
            productLabel.textContent = 'Select Product';
            productLabel.classList.remove('selected');
            productLabel.classList.add('placeholder');
        }

        if (descriptionInput) descriptionInput.value = '';
        if (qtyInput) qtyInput.value = 1;
        if (priceInput) priceInput.value = '0.00';
        if (discountInput) discountInput.value = 0;
        if (totalInput) totalInput.value = '0.00';

        recalculateSummary();
    }

    function openProductModal() {
        if (!productModal) return;

        productModal.classList.add('show');
        document.body.style.overflow = 'hidden';
        highlightSelectedProduct();
        filterProducts();

        if (productSearchInput) {
            setTimeout(function () {
                productSearchInput.focus();
            }, 50);
        }
    }

    function closeProductModal() {
        if (!productModal) return;

        productModal.classList.remove('show');
        document.body.style.overflow = '';
        document.querySelectorAll('.product-row-card').forEach(function (card) {
            card.classList.remove('is-selected');
        });
        activeItemRow = null;
    }

    function highlightSelectedProduct() {
        if (!activeItemRow) return;

        const selectedId = activeItemRow.querySelector('.product-id-input')?.value || '';
        document.querySelectorAll('.product-row-card').forEach(function (card) {
            card.classList.toggle('is-selected', Boolean(selectedId) && card.dataset.id === selectedId);
        });
    }

    function filterProducts() {
        const keyword = (productSearchInput?.value || '').trim().toLowerCase();
        const category = (productCategoryFilter?.value || '').trim().toLowerCase();
        const type = (productTypeFilter?.value || '').trim().toLowerCase();

        let visibleCount = 0;

        document.querySelectorAll('.product-row-card').forEach(function (card) {
            const name = (card.dataset.name || '').toLowerCase();
            const description = (card.dataset.description || '').toLowerCase();
            const cardCategory = (card.dataset.category || '').toLowerCase();
            const cardType = (card.dataset.type || '').toLowerCase();

            const matchKeyword = !keyword || name.includes(keyword) || description.includes(keyword);
            const matchCategory = !category || cardCategory === category;
            const matchType = !type || cardType === type;

            const visible = matchKeyword && matchCategory && matchType;
            card.classList.toggle('d-none', !visible);

            if (visible) visibleCount += 1;
        });

        if (productEmptyState) {
            productEmptyState.classList.toggle('d-none', visibleCount !== 0);
        }

        highlightSelectedProduct();
    }

    function bindRowEvents(row) {
        if (!row || row.dataset.bound === 'true') return;
        row.dataset.bound = 'true';

        const openModalBtn = row.querySelector('.open-product-modal-btn');
        const qtyInput = row.querySelector('.qty-input');
        const priceInput = row.querySelector('.price-input');
        const discountInput = row.querySelector('.discount-input');
        const removeBtn = row.querySelector('.remove-item-btn');

        if (openModalBtn) {
            openModalBtn.addEventListener('click', function () {
                activeItemRow = row;
                openProductModal();
            });
        }

        if (qtyInput) qtyInput.addEventListener('input', function () { recalculateRow(row); });
        if (priceInput) priceInput.addEventListener('input', function () { recalculateRow(row); });
        if (discountInput) discountInput.addEventListener('input', function () { recalculateRow(row); });

        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                const rows = document.querySelectorAll('.item-row');

                if (rows.length > 1) {
                    row.remove();
                } else {
                    resetRow(row);
                }

                recalculateSummary();
            });
        }
    }

    function bindPaymentRemoveEvents(row) {
        if (!row || row.dataset.bound === 'true') return;
        row.dataset.bound = 'true';

        const removeBtn = row.querySelector('.remove-payment-btn');

        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                const rows = document.querySelectorAll('.payment-row');

                if (rows.length > 1) {
                    row.remove();
                } else {
                    const select = row.querySelector('select');
                    const textarea = row.querySelector('textarea');
                    const input = row.querySelector('input');

                    if (select) select.value = '';
                    if (textarea) textarea.value = '';
                    if (input) input.value = '';
                }
            });
        }
    }

    function createItemRowHtml(index) {
        return `
            <td>
                <input type="hidden" name="items[${index}][product_id]" class="product-id-input" value="">
                <button type="button" class="product-picker-trigger open-product-modal-btn">
                    <span class="placeholder product-picker-label">Select Product</span>
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </td>
            <td>
                <textarea name="items[${index}][description]" class="form-control description-input" placeholder="Enter description"></textarea>
            </td>
            <td>
                <input name="items[${index}][qty]" type="number" step="0.01" min="1" value="1" class="form-control qty-input" required>
            </td>
            <td>
                <input name="items[${index}][price]" type="number" step="0.01" min="0" value="0" class="form-control price-input" required>
            </td>
            <td>
                <input name="items[${index}][discount]" type="number" step="0.01" min="0" max="100" value="0" class="form-control discount-input">
            </td>
            <td>
                <input name="items[${index}][total]" type="number" step="0.01" min="0" value="0" class="form-control total-input" readonly>
            </td>
            <td class="action-cell">
                <button class="circle-icon-btn remove-item-btn" type="button">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        `;
    }

    function createPaymentRowHtml(index) {
        return `
            <td>
                <select name="payments[${index}][method]" class="form-select">
                    <option value="">Select payment method</option>
                    <option value="Cash">Cash</option>
                    <option value="ABA">ABA</option>
                    <option value="Card">Card</option>
                </select>
            </td>
            <td>
                <textarea name="payments[${index}][remark]" class="form-control" placeholder="Enter remark"></textarea>
            </td>
            <td>
                <input name="payments[${index}][amount]" type="number" step="0.01" min="0" class="form-control" placeholder="Enter amount">
            </td>
            <td class="action-cell">
                <button class="circle-icon-btn remove-payment-btn" type="button">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        `;
    }

    document.querySelectorAll('.item-row').forEach(function (row) {
        bindRowEvents(row);
        recalculateRow(row);
    });

    document.querySelectorAll('.payment-row').forEach(function (row) {
        bindPaymentRemoveEvents(row);
    });

    if (addItemBtn && itemsBody) {
        addItemBtn.addEventListener('click', function () {
            const tr = document.createElement('tr');
            tr.classList.add('item-row');
            tr.innerHTML = createItemRowHtml(itemIndex);

            itemsBody.appendChild(tr);
            bindRowEvents(tr);
            recalculateRow(tr);
            itemIndex += 1;
        });
    }

    if (addPaymentBtn && paymentsBody) {
        addPaymentBtn.addEventListener('click', function () {
            const tr = document.createElement('tr');
            tr.classList.add('payment-row');
            tr.innerHTML = createPaymentRowHtml(paymentIndex);

            paymentsBody.appendChild(tr);
            bindPaymentRemoveEvents(tr);
            paymentIndex += 1;
        });
    }

    if (deliveryFeeInput) {
        deliveryFeeInput.addEventListener('input', function () {
            this.value = Math.max(0, toNumber(this.value));
            recalculateSummary();
        });
    }

    if (closeProductModalBtn) {
        closeProductModalBtn.addEventListener('click', closeProductModal);
    }

    if (productModal) {
        productModal.addEventListener('click', function (event) {
            if (event.target === productModal) {
                closeProductModal();
            }
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && productModal?.classList.contains('show')) {
            closeProductModal();
        }
    });

    if (productSearchInput) {
        productSearchInput.addEventListener('input', filterProducts);
    }

    if (productCategoryFilter) {
        productCategoryFilter.addEventListener('change', filterProducts);
    }

    if (productTypeFilter) {
        productTypeFilter.addEventListener('change', filterProducts);
    }

    if (productUnselectBtn) {
        productUnselectBtn.addEventListener('click', function () {
            if (!activeItemRow) return;
            resetRow(activeItemRow);
            closeProductModal();
        });
    }

    if (productResultsList) {
        productResultsList.addEventListener('click', function (event) {
            const card = event.target.closest('.product-row-card');
            if (!card || !activeItemRow) return;

            setProductToRow(activeItemRow, {
                id: card.dataset.id || '',
                name: card.dataset.name || '',
                price: card.dataset.price || 0,
                description: card.dataset.description || ''
            });

            closeProductModal();
        });
    }

    recalculateSummary();
</script>

@include('layout.footer')