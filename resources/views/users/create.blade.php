@php
    $pageTitle = 'Create User';
    $currentPage = 'users';
@endphp

@include('layout.header')
@include('layout.sidebar')

<main class="main">
    <div class="container p-3">
        <h4>Create User</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <input name="name" value="{{ old('name') }}" class="form-control mb-2" placeholder="Name" required>

            <input name="email" type="email" value="{{ old('email') }}" class="form-control mb-2" placeholder="Email">

            <input name="password" type="password" class="form-control mb-2" placeholder="Password" required>

            <input name="phone" value="{{ old('phone') }}" class="form-control mb-2" placeholder="Phone">

            <select name="role" class="form-control mb-2" required>
                <option value="">Select Role</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>Cashier</option>
            </select>

            <select name="status" class="form-control mb-3" required>
                <option value="ACTIVE" {{ old('status') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                <option value="INACTIVE" {{ old('status') === 'INACTIVE' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button class="btn btn-success">Save</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</main>