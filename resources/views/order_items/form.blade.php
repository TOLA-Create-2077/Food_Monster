@php
    $isEdit = !empty($orderItem);
    $pageTitle = $isEdit ? 'Edit Order Item' : 'Create Order Item';
    $currentPage = 'order_items';
@endphp

@include('layout.header')
@include('layout.sidebar')

<main class="main">
    <div class="category-card">
        <div class="category-topbar">
            <div class="category-title">{{ $isEdit ? 'Edit Order Item' : 'Create Order Item' }}</div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger m-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $isEdit ? route('order_items.update', $orderItem->id) : route('order_items.store') }}" class="p-3">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Order</label>
                    <select name="order_id" class="form-select" required>
                        <option value="">Select Order</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}" {{ (string) old('order_id', $orderItem->order_id ?? '') === (string) $order->id ? 'selected' : '' }}>
                                {{ $order->order_no ?? ('Order #' . $order->id) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Item Type</label>
                    <input type="text" name="item_type" class="form-control" value="{{ old('item_type', $orderItem->item_type ?? 'product') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Product</label>
                    <select name="product_id" class="form-select">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ (string) old('product_id', $orderItem->product_id ?? '') === (string) $product->id ? 'selected' : '' }}>
                                {{ $product->title_en ?? ('Product #' . $product->id) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Set Menu ID</label>
                    <input type="number" name="set_menu_id" class="form-control" value="{{ old('set_menu_id', $orderItem->set_menu_id ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="item_name" class="form-control" value="{{ old('item_name', $orderItem->item_name ?? '') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" value="{{ old('description', $orderItem->description ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Qty</label>
                    <input type="number" step="0.01" min="0.01" name="qty" class="form-control" value="{{ old('qty', $orderItem->qty ?? 1) }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Unit Price</label>
                    <input type="number" step="0.01" min="0" name="unit_price" class="form-control" value="{{ old('unit_price', $orderItem->unit_price ?? 0) }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Discount %</label>
                    <input type="number" step="0.01" min="0" max="100" name="discount_percent" class="form-control" value="{{ old('discount_percent', $orderItem->discount_percent ?? 0) }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Preview Total</label>
                    <input type="number" step="0.01" min="0" class="form-control" value="{{ old('total_price', $orderItem->total_price ?? 0) }}" readonly>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    {{ $isEdit ? 'Update Order Item' : 'Create Order Item' }}
                </button>
                <a href="{{ route('order_items.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</main>

@include('layout.footer')