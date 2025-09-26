@extends('house-owner.layouts.app')

@section('title', 'My Tenants')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-people me-2"></i>My Tenants
        </h4>
        <a href="{{ route('house-owner.tenants.create') }}" class="btn btn-success">
            <i class="bi bi-person-plus me-2"></i>Assign Tenant
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('house-owner.tenants.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="building_id" class="form-label">Building</label>
                    <select class="form-select" id="building_id" name="building_id">
                        <option value="">All Buildings</option>
                        @foreach ($buildings as $building)
                            <option value="{{ $building->id }}"
                                {{ request('building_id') == $building->id ? 'selected' : '' }}>
                                {{ $building->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="flat_id" class="form-label">Flat</label>
                    <select class="form-select" id="flat_id" name="flat_id">
                        <option value="">All Flats</option>
                        @foreach ($flats as $flat)
                            <option value="{{ $flat->id }}" {{ request('flat_id') == $flat->id ? 'selected' : '' }}>
                                {{ $flat->flat_number }} - {{ $flat->building->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                        value="{{ request('search') }}" placeholder="Search by tenant name or email...">
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($tenants->count() > 0)
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tenant</th>
                                <th>Flat</th>
                                <th>Building</th>
                                <th>Monthly Rent</th>
                                <th>Start Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tenants as $tenant)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                {{ substr($tenant->tenant->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $tenant->tenant->name }}</h6>
                                                <small class="text-muted">{{ $tenant->tenant->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $tenant->flat->flat_number }}</span>
                                        <br>
                                        <small class="text-muted">Floor {{ $tenant->flat->floor }}</small>
                                    </td>
                                    <td>{{ $tenant->building->name }}</td>
                                    <td class="text-success fw-bold">৳{{ number_format($tenant->monthly_rent, 2) }}</td>
                                    <td>{{ $tenant->start_date->format('M d, Y') }}</td>
                                    <td>
                                        @if ($tenant->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($tenant->status === 'inactive')
                                            <span class="badge bg-warning">Inactive</span>
                                        @else
                                            <span class="badge bg-danger">Terminated</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('house-owner.tenants.show', $tenant) }}"
                                                class="btn btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('house-owner.tenants.edit', $tenant) }}"
                                                class="btn btn-outline-success">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if ($tenant->status === 'active')
                                                <form action="{{ route('house-owner.tenants.terminate', $tenant) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to terminate this tenant assignment?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-warning"
                                                        title="Terminate">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('house-owner.tenants.destroy', $tenant) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to remove this tenant assignment? This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
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

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $tenants->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-people" style="font-size: 4rem; color: #6c757d;"></i>
            </div>
            <h5 class="text-muted mb-3">No Tenants Found</h5>
            <p class="text-muted mb-4">You haven't assigned any tenants to your flats yet. Start by assigning your first
                tenant.</p>
            <a href="{{ route('house-owner.tenants.create') }}" class="btn btn-success btn-lg">
                <i class="bi bi-person-plus me-2"></i>Assign Your First Tenant
            </a>
        </div>
    @endif
@endsection

<style>
    .avatar-sm {
        width: 40px;
        height: 40px;
        font-size: 16px;
        font-weight: bold;
    }
</style>
