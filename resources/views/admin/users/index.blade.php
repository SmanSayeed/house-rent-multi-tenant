@extends('admin.layouts.app')

@section('title', 'Users Management')
@section('page-title', 'Users Management')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2"></i>All Users
                    </h5>
                    <div class="d-flex gap-2">
                        <form method="GET" class="d-flex">
                            <select name="role" class="form-select me-2" onchange="this.form.submit()">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="house_owner" {{ request('role') === 'house_owner' ? 'selected' : '' }}>House
                                    Owner</option>
                                <option value="tenant" {{ request('role') === 'tenant' ? 'selected' : '' }}>Tenant</option>
                            </select>
                        </form>
                        <form method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search users..."
                                value="{{ request('search') }}">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if ($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Contact</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar-sm bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'house_owner' ? 'success' : 'info') }} text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $user->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'house_owner' ? 'success' : 'info') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                                </span>
                                            </td>
                                            <td>{{ $user->contact ?? 'N/A' }}</td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ url('/admin/users/' . $user->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $users->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No users found</h5>
                            <p class="text-muted">No users match your current filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
