@php
    $isEdit = !empty($category);
    $pageTitle = $isEdit ? 'Edit Category' : 'Create Category';
    $currentPage = 'item__categories';
@endphp

@include('layout.header')
@include('layout.sidebar')

<main class="main">
    <div class="category-card">
        <div class="category-topbar">
            <div class="category-title">{{ $isEdit ? 'Edit Category' : 'Create Category' }}</div>
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

        <form
            method="POST"
            action="{{ $isEdit ? route('categories.update', $category->id) : route('categories.store') }}"
            enctype="multipart/form-data"
            class="p-3"
        >
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Branch</label>
                    <select name="branch_id" class="form-select" required>
                        <option value="">Select Branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ (string) old('branch_id', $category->branch_id ?? '') === (string) $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Code</label>
                    <input
                        type="text"
                        name="code"
                        class="form-control"
                        value="{{ old('code', $category->code ?? '') }}"
                        required
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">Title (English)</label>
                    <input
                        type="text"
                        name="title_en"
                        class="form-control"
                        value="{{ old('title_en', $category->title_en ?? '') }}"
                        required
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">Title (Khmer)</label>
                    <input
                        type="text"
                        name="title_km"
                        class="form-control"
                        value="{{ old('title_km', $category->title_km ?? '') }}"
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="active" {{ old('status', $category->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $category->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                </div>

                @if($isEdit && !empty($category->image))
                    <div class="col-12">
                        <label class="form-label d-block">Current Image</label>
                        <img
                            src="{{ asset('storage/' . $category->image) }}"
                            alt="{{ $category->title_en }}"
                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"
                        >
                    </div>
                @endif
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    {{ $isEdit ? 'Update Category' : 'Create Category' }}
                </button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</main>

@include('layout.footer')
