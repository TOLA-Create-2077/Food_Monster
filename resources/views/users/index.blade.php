@php
    $pageTitle = "User Admin";
    $currentPage = "users";
@endphp

@include('layout.header')
@include('layout.sidebar')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<main class="main">
<div class="container-fluid p-3">

    <!-- TOP BAR -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div><strong>Total : {{ count($users) }}</strong></div>

        <div class="d-flex gap-2">
            <input type="text" class="form-control" placeholder="Search..." style="width:200px">

            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> CREATE NEW
            </a>

            <button class="btn btn-danger">Trash</button>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Create Date</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($users as $key => $user)
                    <tr>

                        <td>{{ $key+1 }}</td>

                        <!-- Avatar -->
                        <td>
                            <img src="https://i.pravatar.cc/40?img={{ $user->id }}" 
                                 style="width:40px;height:40px;border-radius:50%;">
                        </td>

                        <td>{{ $user->name }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>

                        <!-- Status -->
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>

                        <!-- Date -->
                        <td>
                            {{ $user->created_at ? $user->created_at->format('M d, Y') : '-' }}
                        </td>

                        <!-- ACTION -->
                        <td style="width:60px; position:relative;">
                            <button onclick="toggleMenu({{ $user->id }})" class="btn btn-light btn-sm">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>

                            <div class="action-menu" id="menu{{ $user->id }}">
                                <a href="{{ route('users.edit',$user->id) }}">Edit</a>

                                <form action="{{ route('users.destroy',$user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button>Delete</button>
                                </form>
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

</div>
</main>

<!-- STYLE -->
<style>
.card {
    border-radius: 10px;
    border: none;
}

.table th {
    font-size: 13px;
    color: #777;
}

.table td {
    font-size: 14px;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
}

/* Action menu */
.action-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 35px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 6px;
    min-width: 120px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    z-index: 99;
}

.action-menu a,
.action-menu button {
    display: block;
    padding: 8px 10px;
    text-decoration: none;
    color: #333;
    background: none;
    border: none;
    width: 100%;
    text-align: left;
}

.action-menu a:hover,
.action-menu button:hover {
    background: #f5f5f5;
}
</style>

<!-- SCRIPT -->
<script>
function toggleMenu(id) {
    document.querySelectorAll('.action-menu').forEach(el => el.style.display='none');
    let menu = document.getElementById('menu'+id);
    menu.style.display = 'block';
}
</script>