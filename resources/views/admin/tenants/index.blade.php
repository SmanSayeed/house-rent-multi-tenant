@extends('admin.layouts.app')

@section('title', 'Tenant Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-people me-2"></i>Tenant Management
        </h4>
        <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-2"></i>Create New Tenant
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
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.tenants.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ $filters['search'] ?? '' }}" placeholder="Name, email, or contact">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="building_id" class="form-label">Building</label>
                    <select class="form-select" id="building_id" name="building_id">
                        <option value="">All Buildings</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}" {{ ($filters['building_id'] ?? '') == $building->id ? 'selected' : '' }}>
                                {{ $building->name }} - {{ $building->address }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="flat_id" class="form-label">Flat</label>
                    <select class="form-select" id="flat_id" name="flat_id">
                        <option value="">All Flats</option>
                        <!-- Flats will be populated via JavaScript based on building selection -->
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tenants Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-list me-2"></i>All Tenants ({{ $tenants->total() }})
            </h5>
        </div>
        <div class="card-body p-0">
            @if($tenants->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Current Assignment</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tenants as $tenant)
                                <tr>
                                    <td>{{ $tenant->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ substr($tenant->name, 0, 1) }}
                                            </div>
                                            {{ $tenant->name }}
                                        </div>
                                    </td>
                                    <td>{{ $tenant->email }}</td>
                                    <td>{{ $tenant->contact ?? 'N/A' }}</td>
                                    <td>
                                        @if($tenant->tenantAssignments->where('status', 'active')->count() > 0)
                                            @php
                                                $activeAssignment = $tenant->tenantAssignments->where('status', 'active')->first();
                                            @endphp
                                            <div class="text-sm">
                                                <strong>{{ $activeAssignment->flat->flat_number }}</strong><br>
                                                <small class="text-muted">{{ $activeAssignment->flat->building->name }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tenant->tenantAssignments->where('status', 'active')->count() > 0)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $tenant->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.tenants.show', $tenant) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.tenants.edit', $tenant) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if($tenant->tenantAssignments->where('status', 'active')->count() == 0)
                                                <form action="{{ route('admin.tenants.destroy', $tenant) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this tenant?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer">
                    {{ $tenants->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people display-1 text-muted"></i>
                    <h5 class="mt-3 text-muted">No tenants found</h5>
                    <p class="text-muted">Get started by creating a new tenant.</p>
                    <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>Create New Tenant
                    </a>
                </div>
            @endif
        </div>
    </div>

    <style>
        .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>

    <script>
        // Building-Flat relationship
        document.getElementById('building_id').addEventListener('change', function() {
            const buildingId = this.value;
            const flatSelect = document.getElementById('flat_id');
            
            // Clear existing options
            flatSelect.innerHTML = '<option value="">All Flats</option>';
            
            if (buildingId) {
                // Fetch flats for the selected building
                fetch(`/admin/buildings/${buildingId}/flats`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(flat => {
                            const option = document.createElement('option');
                            option.value = flat.id;
                            option.textContent = `${flat.flat_number} - ${flat.rent_amount ? 'Tk ' + flat.rent_amount : 'No rent set'}`;
                            flatSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching flats:', error));
            }
        });
    </script>
@endsection
