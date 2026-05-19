@php
    $pageTitle = 'All Products';
    $currentPage = 'item__all_products';
@endphp

@include('layout.header')
@include('layout.sidebar')

<style>
    .product-shell-card {
        background: #fff;
        border-radius: 14px;
        padding: 18px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04), 0 12px 24px rgba(16, 24, 40, 0.06);
    }
    .product-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }
    .product-toolbar-title {
        font-size: 20px;
        font-weight: 700;
        color: #344054;
    }
    .product-toolbar-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .product-toolbar-actions select,
    .product-toolbar-actions input {
        height: 42px;
        border: 1px solid #d0d5dd;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 14px;
        color: #344054;
        background: #fff;
        min-width: 140px;
    }
    .product-search-wrap {
        position: relative;
    }
    .product-search-wrap input {
        min-width: 240px;
        padding-right: 40px;
    }
    .product-search-wrap i {
        position: absolute;
        top: 50%;
        right: 14px;
        transform: translateY(-50%);
        color: #98a2b3;
        font-size: 14px;
    }
    .product-btn {
        height: 42px;
        border: none;
        border-radius: 8px;
        padding: 0 14px;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
    }
    .product-btn-filter,
    .product-btn-create {
        background: #2e90fa;
        color: #fff;
    }
    .product-btn-refresh {
        width: 42px;
        padding: 0;
        background: #fff;
        border: 1px solid #d0d5dd;
        color: #475467;
    }
    .product-table-wrap {
        overflow-x: auto;
    }
    .product-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1300px;
    }
    .product-table thead th {
        background: #f9fafb;
        color: #667085;
        font-size: 13px;
        font-weight: 700;
        padding: 14px 12px;
        border-bottom: 1px solid #eaecf0;
        white-space: nowrap;
    }
    .product-table tbody td {
        padding: 14px 12px;
        border-bottom: 1px solid #eaecf0;
        color: #475467;
        font-size: 14px;
        vertical-align: middle;
    }
    .product-badge-active,
    .product-badge-inactive {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 78px;
        height: 28px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        padding: 0 10px;
    }
    .product-badge-active {
        background: #dcfae6;
        color: #16a34a;
    }
    .product-badge-inactive {
        background: #fee4e2;
        color: #ef4444;
    }
    .product-action-btn {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 8px;
        background: #f2f4f7;
        color: #475467;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .product-footer {
        margin-top: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .product-footer-left {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #667085;
        font-size: 14px;
        font-weight: 600;
    }
    .drawer-modal.modal.fade .modal-dialog {
        margin: 0 0 0 auto;
        max-width: 760px;
        width: 100%;
        height: 100vh;
        transform: translateX(100%);
        transition: transform 0.25s ease;
    }
    .drawer-modal.modal.show .modal-dialog {
        transform: translateX(0);
    }
    .drawer-modal .modal-content {
        height: 100vh;
        border: none;
        border-radius: 0;
        box-shadow: -12px 0 30px rgba(16, 24, 40, 0.16);
    }
    .drawer-modal .modal-header {
        padding: 22px 24px 16px;
        border-bottom: 1px solid #eaecf0;
        align-items: flex-start;
    }
    .drawer-modal .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: #344054;
    }
    .drawer-modal .modal-body {
        padding: 20px 24px 110px;
        overflow-y: auto;
        background: #fff;
    }
    .drawer-modal .modal-footer {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        background: #fff;
        border-top: 1px solid #eaecf0;
        padding: 16px 24px;
        justify-content: flex-end;
    }
    .drawer-block {
        margin-bottom: 18px;
    }
    .drawer-block-title {
        font-size: 13px;
        font-weight: 700;
        color: #667085;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eaecf0;
        text-align: center;
    }
    .drawer-label {
        font-size: 13px;
        font-weight: 600;
        color: #475467;
        margin-bottom: 6px;
        display: block;
    }
    .drawer-input,
    .drawer-select,
    .drawer-textarea {
        width: 100%;
        border: 1px solid #d0d5dd;
        border-radius: 8px;
        background: #fff;
        color: #344054;
        font-size: 14px;
    }
    .drawer-input,
    .drawer-select {
        height: 44px;
        padding: 0 12px;
    }
    .drawer-textarea {
        min-height: 86px;
        padding: 10px 12px;
        resize: vertical;
    }
    .drawer-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .drawer-save-btn {
        height: 44px;
        border: none;
        border-radius: 8px;
        background: #2e90fa;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        padding: 0 18px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
</style>

<main class="main">
    <div class="product-shell-card">
        @if(session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
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

        <div class="product-toolbar">
            <div class="product-toolbar-title">All Items : {{ $products->total() }}</div>

            <form method="GET" action="{{ route('products.index') }}" class="product-toolbar-actions">
                <select name="branch_id">
                    <option value="">Branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ (string) request('branch_id') === (string) $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>

                <select name="category_id">
                    <option value="">Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
                            {{ $category->title_en }}
                        </option>
                    @endforeach
                </select>

                <select name="status">
                    <option value="">Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <div class="product-search-wrap">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search...">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>

                <button type="submit" class="product-btn product-btn-filter">
                    <i class="fa-solid fa-filter"></i>
                    Filter
                </button>

                <button type="button" class="product-btn product-btn-create" data-bs-toggle="modal" data-bs-target="#createProductModal">
                    <i class="fa-solid fa-plus"></i>
                    CREATE NEW
                </button>

                <a href="{{ route('products.index') }}" class="product-btn product-btn-refresh">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </form>
        </div>

        <div class="product-table-wrap">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Branch</th>
                        <th>Code</th>
                        <th>Title (EN)</th>
                        <th>Title (KM)</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Cost</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th style="width: 70px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $product)
                        <tr>
                            <td>{{ $products->firstItem() + $index }}</td>
                            <td>{{ $product->branch_name ?? '-' }}</td>
                            <td>{{ $product->code ?? '-' }}</td>
                            <td>{{ $product->title_en ?? '-' }}</td>
                            <td>{{ $product->title_km ?? '-' }}</td>
                            <td>{{ $product->category_name ?? '-' }}</td>
                            <td>{{ $product->unit ?? '-' }}</td>
                            <td>{{ number_format((float) $product->cost, 2) }}</td>
                            <td>{{ number_format((float) $product->price, 2) }}</td>
                            <td>
                                @if(($product->status ?? '') === 'active')
                                    <span class="product-badge-active">Active</span>
                                @else
                                    <span class="product-badge-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button
                                    type="button"
                                    class="product-action-btn edit-product-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editProductModal"
                                    data-id="{{ $product->id }}"
                                    data-branch_id="{{ $product->branch_id }}"
                                    data-category_id="{{ $product->category_id }}"
                                    data-code="{{ $product->code }}"
                                    data-title_en="{{ $product->title_en }}"
                                    data-title_km="{{ $product->title_km }}"
                                    data-unit="{{ $product->unit }}"
                                    data-cost="{{ $product->cost }}"
                                    data-price="{{ $product->price }}"
                                    data-description_en="{{ $product->description_en }}"
                                    data-description_km="{{ $product->description_km }}"
                                    data-status="{{ $product->status }}"
                                    title="Edit"
                                >
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="product-footer">
            <div class="product-footer-left">
                <span>Total of : {{ $products->lastPage() }}</span>
                <span>Page {{ $products->currentPage() }} / {{ $products->lastPage() }}</span>
            </div>

            <div class="product-footer-right">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</main>

<div class="modal fade drawer-modal" id="createProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('products.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add New Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="drawer-block">
                        <label class="drawer-label">Branch</label>
                        <select name="branch_id" class="drawer-select" required>
                            <option value="">...</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="drawer-block">
                        <div class="drawer-grid">
                            <div>
                                <label class="drawer-label">Code *</label>
                                <input type="text" name="code" class="drawer-input" placeholder="IM001" required>
                            </div>
                            <div>
                                <label class="drawer-label">Status *</label>
                                <select name="status" class="drawer-select" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="drawer-block">
                        <div class="drawer-grid">
                            <div>
                                <label class="drawer-label">Title (EN)</label>
                                <input type="text" name="title_en" class="drawer-input" required>
                            </div>
                            <div>
                                <label class="drawer-label">Title (KM)</label>
                                <input type="text" name="title_km" class="drawer-input">
                            </div>
                        </div>
                    </div>

                    <div class="drawer-block">
                        <label class="drawer-label">Category</label>
                        <select name="category_id" class="drawer-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->title_en }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="drawer-block">
                        <div class="drawer-grid">
                            <div>
                                <label class="drawer-label">Description (EN)</label>
                                <textarea name="description_en" class="drawer-textarea"></textarea>
                            </div>
                            <div>
                                <label class="drawer-label">Description (KM)</label>
                                <textarea name="description_km" class="drawer-textarea"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="drawer-block">
                        <div class="drawer-block-title">Sale & Inventory Info</div>
                        <div class="drawer-grid">
                            <div>
                                <label class="drawer-label">Unit</label>
                                <input type="text" name="unit" class="drawer-input" placeholder="pcs" required>
                            </div>
                            <div>
                                <label class="drawer-label">Cost</label>
                                <input type="number" step="0.01" min="0" name="cost" class="drawer-input" value="0.00" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="drawer-label">Price</label>
                            <input type="number" step="0.01" min="0" name="price" class="drawer-input" value="0.00" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="drawer-save-btn">
                        <i class="fa-regular fa-floppy-disk"></i>
                        Save & Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade drawer-modal" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" id="editProductForm">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="editDrawerTitle">Update Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="drawer-block">
                        <label class="drawer-label">Branch</label>
                        <select name="branch_id" id="edit_branch_id" class="drawer-select" required>
                            <option value="">...</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="drawer-block">
                        <div class="drawer-grid">
                            <div>
                                <label class="drawer-label">Code *</label>
                                <input type="text" name="code" id="edit_code" class="drawer-input" required>
                            </div>
                            <div>
                                <label class="drawer-label">Status *</label>
                                <select name="status" id="edit_status" class="drawer-select" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="drawer-block">
                        <div class="drawer-grid">
                            <div>
                                <label class="drawer-label">Title (EN)</label>
                                <input type="text" name="title_en" id="edit_title_en" class="drawer-input" required>
                            </div>
                            <div>
                                <label class="drawer-label">Title (KM)</label>
                                <input type="text" name="title_km" id="edit_title_km" class="drawer-input">
                            </div>
                        </div>
                    </div>

                    <div class="drawer-block">
                        <label class="drawer-label">Category</label>
                        <select name="category_id" id="edit_category_id" class="drawer-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->title_en }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="drawer-block">
                        <div class="drawer-grid">
                            <div>
                                <label class="drawer-label">Description (EN)</label>
                                <textarea name="description_en" id="edit_description_en" class="drawer-textarea"></textarea>
                            </div>
                            <div>
                                <label class="drawer-label">Description (KM)</label>
                                <textarea name="description_km" id="edit_description_km" class="drawer-textarea"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="drawer-block">
                        <div class="drawer-block-title">Sale & Inventory Info</div>
                        <div class="drawer-grid">
                            <div>
                                <label class="drawer-label">Unit</label>
                                <input type="text" name="unit" id="edit_unit" class="drawer-input" required>
                            </div>
                            <div>
                                <label class="drawer-label">Cost</label>
                                <input type="number" step="0.01" min="0" name="cost" id="edit_cost" class="drawer-input" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="drawer-label">Price</label>
                            <input type="number" step="0.01" min="0" name="price" id="edit_price" class="drawer-input" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="drawer-save-btn">
                        <i class="fa-regular fa-floppy-disk"></i>
                        Save & Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editForm = document.getElementById('editProductForm');
    const editTitle = document.getElementById('editDrawerTitle');
    const editButtons = document.querySelectorAll('.edit-product-btn');

    editButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const productId = this.dataset.id || '';

            editForm.action = `/item_management/products/${productId}`;
            editTitle.textContent = `Update Item (${this.dataset.code || ''})`;

            document.getElementById('edit_branch_id').value = this.dataset.branch_id || '';
            document.getElementById('edit_category_id').value = this.dataset.category_id || '';
            document.getElementById('edit_code').value = this.dataset.code || '';
            document.getElementById('edit_title_en').value = this.dataset.title_en || '';
            document.getElementById('edit_title_km').value = this.dataset.title_km || '';
            document.getElementById('edit_unit').value = this.dataset.unit || '';
            document.getElementById('edit_cost').value = this.dataset.cost || '0.00';
            document.getElementById('edit_price').value = this.dataset.price || '0.00';
            document.getElementById('edit_status').value = this.dataset.status || 'active';
            document.getElementById('edit_description_en').value = this.dataset.description_en || '';
            document.getElementById('edit_description_km').value = this.dataset.description_km || '';
        });
    });
});
</script>

@include('layout.footer')