@php
    $pageTitle = 'Edit User';
    $currentPage = 'users';
@endphp

@include('layout.header')
@include('layout.sidebar')

<main class="main">
    <div class="container p-3">
        <h4>Edit User</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <input name="name" value="{{ old('name', $user->name) }}" class="form-control mb-2" placeholder="Name" required>

            <input name="email" type="email" value="{{ old('email', $user->email) }}" class="form-control mb-2" placeholder="Email">

            <input name="password" type="password" class="form-control mb-2" placeholder="New Password (optional)">

            <input name="phone" value="{{ old('phone', $user->phone) }}" class="form-control mb-2" placeholder="Phone">

            <select name="role" class="form-control mb-2" required>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="cashier" {{ old('role', $user->role) === 'cashier' ? 'selected' : '' }}>Cashier</option>
            </select>

            <select name="status" class="form-control mb-3" required>
                <option value="ACTIVE" {{ old('status', $user->status) === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                <option value="INACTIVE" {{ old('status', $user->status) === 'INACTIVE' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button class="btn btn-primary">Update</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</main>