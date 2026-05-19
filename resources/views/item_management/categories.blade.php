@php
    $pageTitle = 'Category Management';
    $currentPage = 'item__categories';
@endphp

@include('layout.header')
@include('layout.sidebar')

<style>
    .category-shell-card {
        background: #fff;
        border-radius: 14px;
        padding: 18px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04), 0 12px 24px rgba(16, 24, 40, 0.06);
    }

    .category-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .category-toolbar-title {
        font-size: 20px;
        font-weight: 700;
        color: #344054;
    }

    .category-toolbar-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .category-toolbar-actions select,
    .category-toolbar-actions input {
        height: 42px;
        border: 1px solid #d0d5dd;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 14px;
        color: #344054;
        background: #fff;
        min-width: 140px;
    }

    .category-search-wrap {
        position: relative;
    }

    .category-search-wrap input {
        min-width: 240px;
        padding-right: 40px;
    }

    .category-search-wrap i {
        position: absolute;
        top: 50%;
        right: 14px;
        transform: translateY(-50%);
        color: #98a2b3;
        font-size: 14px;
    }

    .category-btn {
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

    .category-btn-filter {
        background: #2e90fa;
        color: #fff;
    }

    .category-btn-create {
        background: #2e90fa;
        color: #fff;
    }

    .category-btn-refresh {
        width: 42px;
        padding: 0;
        background: #fff;
        border: 1px solid #d0d5dd;
        color: #475467;
    }

    .category-grid-wrap {
        overflow-x: auto;
    }

    .category-grid {
        width: 100%;
        border-collapse: collapse;
        min-width: 1100px;
    }

    .category-grid thead th {
        background: #f9fafb;
        color: #667085;
        font-size: 13px;
        font-weight: 700;
        padding: 14px 12px;
        border-bottom: 1px solid #eaecf0;
        white-space: nowrap;
    }

    .category-grid tbody td {
        padding: 14px 12px;
        border-bottom: 1px solid #eaecf0;
        color: #475467;
        font-size: 14px;
        vertical-align: middle;
    }

    .category-thumb {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #eaecf0;
        background: #f8fafc;
    }

    .category-badge-active,
    .category-badge-inactive {
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

    .category-badge-active {
        background: #dcfae6;
        color: #16a34a;
    }

    .category-badge-inactive {
        background: #fee4e2;
        color: #ef4444;
    }

    .category-action-btn {
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

    .category-footer {
        margin-top: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .category-footer-left {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #667085;
        font-size: 14px;
        font-weight: 600;
    }

    .category-footer-right nav {
        margin: 0;
    }

    .category-footer-right .pagination {
        margin: 0;
    }

    .drawer-modal.modal.fade .modal-dialog {
        margin: 0 0 0 auto;
        max-width: 720px;
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

    .drawer-modal .btn-close {
        background-size: 14px;
        opacity: 1;
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
        min-height: 90px;
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

    @media (max-width: 767.98px) {
        .drawer-modal.modal.fade .modal-dialog {
            max-width: 100%;
        }

        .drawer-grid {
            grid-template-columns: 1fr;
        }

        .category-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .category-toolbar-actions {
            width: 100%;
        }

        .category-toolbar-actions > * {
            flex: 1 1 100%;
        }

        .category-search-wrap input {
            min-width: 100%;
            width: 100%;
        }
    }
</style>

<main class="main">
    <div class="category-shell-card">
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

        <div class="category-toolbar">
            <div class="category-toolbar-title">Total : {{ $categories->total() }}</div>

            <form method="GET" action="{{ route('categories.index') }}" class="category-toolbar-actions">
                <select name="branch_id">
                    <option value="">Branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>

                <div class="category-search-wrap">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search...">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>

                <button type="submit" class="category-btn category-btn-filter">
                    <i class="fa-solid fa-filter"></i>
                    Filter
                </button>

                <button type="button" class="category-btn category-btn-create" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                    <i class="fa-solid fa-plus"></i>
                    CREATE NEW
                </button>

                <a href="{{ route('categories.index') }}" class="category-btn category-btn-refresh">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </form>
        </div>

        <div class="category-grid-wrap">
            <table class="category-grid">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Branch</th>
                        <th>Code</th>
                        <th>Image</th>
                        <th>Title (English)</th>
                        <th>Title (Khmer)</th>
                        <th>Total Items</th>
                        <th>Status</th>
                        <th style="width: 70px;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($categories as $index => $category)
                        <tr>
                            <td>{{ $categories->firstItem() + $index }}</td>
                            <td>{{ $category->branch_name ?? '-' }}</td>
                            <td>{{ $category->code ?? '-' }}</td>
                            <td>
                                @if($category->image)
                                    <img
                                        class="category-thumb"
                                        src="{{ asset('storage/' . $category->image) }}"
                                        alt="{{ $category->title_en }}"
                                    >
                                @else
                                    <span>-</span>
                                @endif
                            </td>
                            <td>{{ $category->title_en ?? '-' }}</td>
                            <td>{{ $category->title_km ?? '-' }}</td>
                            <td>{{ $category->total_items ?? 0 }}</td>
                            <td>
                                @if(($category->status ?? '') === 'active')
                                    <span class="category-badge-active">Active</span>
                                @else
                                    <span class="category-badge-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button
                                    type="button"
                                    class="category-action-btn edit-category-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editCategoryModal"
                                    data-id="{{ $category->id }}"
                                    data-branch_id="{{ $category->branch_id }}"
                                    data-code="{{ $category->code }}"
                                    data-title_en="{{ $category->title_en }}"
                                    data-title_km="{{ $category->title_km }}"
                                    data-status="{{ $category->status }}"
                                    title="Edit"
                                >
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="category-footer">
            <div class="category-footer-left">
                <span>Total of : {{ $categories->lastPage() }}</span>
                <span>Page {{ $categories->currentPage() }} / {{ $categories->lastPage() }}</span>
            </div>

            <div class="category-footer-right">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</main>

<div class="modal fade drawer-modal" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Create Category</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="drawer-block">
                        <div class="drawer-grid">
                            <div>
                                <label class="drawer-label">Branch *</label>
                                <select name="branch_id" class="drawer-select" required>
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
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
                                <label class="drawer-label">Code *</label>
                                <input type="text" name="code" class="drawer-input" required>
                            </div>
                            <div>
                                <label class="drawer-label">Image</label>
                                <input type="file" name="image" class="drawer-input" accept=".jpg,.jpeg,.png,.webp">
                            </div>
                        </div>
                    </div>

                    <div class="drawer-block">
                        <div class="drawer-grid">
                            <div>
                                <label class="drawer-label">Title (English) *</label>
                                <input type="text" name="title_en" class="drawer-input" required>
                            </div>
                            <div>
                                <label class="drawer-label">Title (Khmer)</label>
                                <input type="text" name="title_km" class="drawer-input">
                            </div>
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

<div class="modal fade drawer-modal" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" id="editCategoryForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="editCategoryTitle">Update Category</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="drawer-block">
                        <div class="drawer-grid">
                            <div>
                                <label class="drawer-label">Branch *</label>
                                <select name="branch_id" id="edit_branch_id" class="drawer-select" required>
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
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
                                <label class="drawer-label">Code *</label>
                                <input type="text" name="code" id="edit_code" class="drawer-input" required>
                            </div>
                            <div>
                                <label class="drawer-label">Image</label>
                                <input type="file" name="image" class="drawer-input" accept=".jpg,.jpeg,.png,.webp">
                            </div>
                        </div>
                    </div>

                    <div class="drawer-block">
                        <div class="drawer-grid">
                            <div>
                                <label class="drawer-label">Title (English) *</label>
                                <input type="text" name="title_en" id="edit_title_en" class="drawer-input" required>
                            </div>
                            <div>
                                <label class="drawer-label">Title (Khmer)</label>
                                <input type="text" name="title_km" id="edit_title_km" class="drawer-input">
                            </div>
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
    const editForm = document.getElementById('editCategoryForm');
    const editTitle = document.getElementById('editCategoryTitle');
    const editButtons = document.querySelectorAll('.edit-category-btn');

    editButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const categoryId = this.dataset.id || '';

            editForm.action = `/item_management/categories/${categoryId}`;
            editTitle.textContent = `Update Category (${this.dataset.code || ''})`;

            document.getElementById('edit_branch_id').value = this.dataset.branch_id || '';
            document.getElementById('edit_code').value = this.dataset.code || '';
            document.getElementById('edit_title_en').value = this.dataset.title_en || '';
            document.getElementById('edit_title_km').value = this.dataset.title_km || '';
            document.getElementById('edit_status').value = this.dataset.status || 'active';
        });
    });
});
</script>

@include('layout.footer')