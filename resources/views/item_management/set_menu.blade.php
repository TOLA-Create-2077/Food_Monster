@php
    $pageTitle = 'Set Menu';
    $currentPage = 'item__set_menu';

    $orderItemJson = [];
    foreach ($orderItems as $orderItem) {
        $orderItemJson[] = [
            'product_id' => $orderItem->product_id,
            'name' => $orderItem->product_title ?: $orderItem->item_name,
            'unit_price' => $orderItem->unit_price,
        ];
    }

    $selectedItemsJson = $selectedItems ?? [];
@endphp

@include('layout.header')
@include('layout.sidebar')

<main class="main">
    <div class="category-card">
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

        <div class="category-topbar">
            <div class="category-title">Total : {{ $setMenus->total() }}</div>

            <form method="GET" action="{{ route('set_menus.index') }}" class="category-filters">
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

                <div class="search-box-category">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search...">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>

                <button type="submit" class="refresh-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>

                <button type="button" class="create-btn" data-bs-toggle="modal" data-bs-target="#createSetMenuModal">
                    <i class="fa-solid fa-plus"></i> CREATE NEW
                </button>

                <a href="{{ route('set_menus.index') }}" class="refresh-btn text-decoration-none d-inline-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </form>
        </div>

        <div class="table-wrap">
            <table class="category-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Branch</th>
                        <th>Category</th>
                        <th>Code</th>
                        <th>Title (English)</th>
                        <th>Title (Khmer)</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th style="width:70px;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($setMenus as $index => $setMenu)
                        <tr>
                            <td>{{ $setMenus->firstItem() + $index }}</td>
                            <td>{{ $setMenu->branch_name ?? '-' }}</td>
                            <td>{{ $setMenu->category_name ?? '-' }}</td>
                            <td>{{ $setMenu->code ?? '-' }}</td>
                            <td>{{ $setMenu->title_en ?? '-' }}</td>
                            <td>{{ $setMenu->title_km ?? '-' }}</td>
                            <td>{{ $setMenu->description ?? '-' }}</td>
                            <td>{{ number_format((float) $setMenu->price, 2) }}</td>
                            <td>
                                @if(($setMenu->status ?? '') === 'active')
                                    <span class="active-badge">Active</span>
                                @else
                                    <span class="inactive-badge">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @php $items = $selectedItems[$setMenu->id] ?? []; @endphp
                                @if(count($items))
                                    @foreach($items as $item)
                                        <div>{{ $item['product_name'] ?? ('Product #' . $item['product_id']) }} x {{ $item['qty'] }}</div>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary edit-set-menu-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editSetMenuModal"
                                    data-id="{{ $setMenu->id }}"
                                    data-branch_id="{{ $setMenu->branch_id }}"
                                    data-category_id="{{ $setMenu->category_id }}"
                                    data-code="{{ $setMenu->code }}"
                                    data-title_en="{{ $setMenu->title_en }}"
                                    data-title_km="{{ $setMenu->title_km }}"
                                    data-description="{{ $setMenu->description }}"
                                    data-price="{{ $setMenu->price }}"
                                    data-status="{{ $setMenu->status }}"
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No set menu found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div class="footer-left">
                <span>Total of : {{ $setMenus->lastPage() }}</span>
                <span>Page {{ $setMenus->currentPage() }} / {{ $setMenus->lastPage() }}</span>
            </div>

            <div class="footer-right">
                {{ $setMenus->links() }}
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="createSetMenuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('set_menus.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Create Set Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Branch</label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title_en }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Code</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" min="0" name="price" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Title (EN)</label>
                            <input type="text" name="title_en" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Title (KM)</label>
                            <input type="text" name="title_km" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="mb-3">Select Items From Order Items</h6>
                    <div id="create-items-wrapper"></div>
                    <button type="button" class="btn btn-outline-primary mt-2" id="add-create-item-btn">Add Item</button>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save & Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editSetMenuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" id="editSetMenuForm">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="editSetMenuTitle">Update Set Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Branch</label>
                            <select name="branch_id" id="edit_branch_id" class="form-select" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" id="edit_category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title_en }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Code</label>
                            <input type="text" name="code" id="edit_code" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" min="0" name="price" id="edit_price" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Title (EN)</label>
                            <input type="text" name="title_en" id="edit_title_en" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Title (KM)</label>
                            <input type="text" name="title_km" id="edit_title_km" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="mb-3">Select Items From Order Items</h6>
                    <div id="edit-items-wrapper"></div>
                    <button type="button" class="btn btn-outline-primary mt-2" id="add-edit-item-btn">Add Item</button>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update & Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sourceItems = @json($orderItemJson);
    const selectedItems = @json($selectedItemsJson);

    function itemOptions(selectedId = '') {
        let options = '<option value="">Select Item</option>';

        sourceItems.forEach(function (item) {
            const selected = String(selectedId) === String(item.product_id) ? 'selected' : '';
            const label = `${item.name} | ${item.unit_price}`;

            options += `<option value="${item.product_id}" ${selected}>${label}</option>`;
        });

        return options;
    }

    function rowHtml(index, item = null) {
        const productId = item && item.product_id ? item.product_id : '';
        const qty = item && item.qty ? item.qty : '1';

        return `
            <div class="row g-2 mb-2 item-row">
                <div class="col-md-8">
                    <select name="items[${index}][product_id]" class="form-select">
                        ${itemOptions(productId)}
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" min="0.01" name="items[${index}][qty]" class="form-control" value="${qty}" placeholder="Qty">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-item-btn">X</button>
                </div>
            </div>
        `;
    }

    function bindRemove(wrapper) {
        wrapper.querySelectorAll('.remove-item-btn').forEach(function (button) {
            button.onclick = function () {
                button.closest('.item-row').remove();
            };
        });
    }

    function addRow(wrapper, item = null) {
        const index = wrapper.querySelectorAll('.item-row').length;
        wrapper.insertAdjacentHTML('beforeend', rowHtml(index, item));
        bindRemove(wrapper);
    }

    const createWrapper = document.getElementById('create-items-wrapper');
    const editWrapper = document.getElementById('edit-items-wrapper');

    document.getElementById('add-create-item-btn').addEventListener('click', function () {
        addRow(createWrapper);
    });

    document.getElementById('add-edit-item-btn').addEventListener('click', function () {
        addRow(editWrapper);
    });

    addRow(createWrapper);

    document.querySelectorAll('.edit-set-menu-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = this.dataset.id || '';

            document.getElementById('editSetMenuForm').action = `/item_management/set-menu/${id}`;
            document.getElementById('edit_branch_id').value = this.dataset.branch_id || '';
            document.getElementById('edit_category_id').value = this.dataset.category_id || '';
            document.getElementById('edit_code').value = this.dataset.code || '';
            document.getElementById('edit_title_en').value = this.dataset.title_en || '';
            document.getElementById('edit_title_km').value = this.dataset.title_km || '';
            document.getElementById('edit_description').value = this.dataset.description || '';
            document.getElementById('edit_price').value = this.dataset.price || '0.00';
            document.getElementById('edit_status').value = this.dataset.status || 'active';

            editWrapper.innerHTML = '';

            const items = selectedItems[id] || [];
            if (items.length) {
                items.forEach(function (item) {
                    addRow(editWrapper, item);
                });
            } else {
                addRow(editWrapper);
            }
        });
    });
});
</script>

@include('layout.footer')