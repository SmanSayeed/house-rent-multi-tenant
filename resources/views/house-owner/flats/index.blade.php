@extends('house-owner.layouts.app')

@section('title', 'My Flats')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="bi bi-house-door me-2"></i>My Flats
    </h4>
    <a href="{{ route('house-owner.flats.create') }}" class="btn btn-success">
        <i class="bi bi-plus-circle me-2"></i>Add New Flat
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('house-owner.flats.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="building_id" class="form-label">Building</label>
                <select class="form-select" id="building_id" name="building_id">
                    <option value="">All Buildings</option>
                    @foreach($buildings as $building)
                        <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                            {{ $building->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Search by flat number or description...">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($flats->count() > 0)
    <div class="row">
        @foreach($flats as $flat)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-house-door me-2"></i>{{ $flat->flat_number }}
                        </h5>
                        @if($flat->status === 'occupied')
                            <span class="badge bg-success">Occupied</span>
                        @elseif($flat->status === 'available')
                            <span class="badge bg-info">Available</span>
                        @else
                            <span class="badge bg-warning">Maintenance</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Building</h6>
                            <p class="mb-0">{{ $flat->building->name }}</p>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <h6 class="text-muted mb-2">Floor</h6>
                                <p class="mb-0">{{ $flat->floor }}</p>
                            </div>
                            <div class="col-6">
                                <h6 class="text-muted mb-2">Rent Amount</h6>
                                <p class="mb-0 text-success fw-bold">৳{{ number_format($flat->rent_amount, 2) }}</p>
                            </div>
                        </div>
                        
                        @if($flat->description)
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Description</h6>
                                <p class="text-muted small">{{ Str::limit($flat->description, 100) }}</p>
                            </div>
                        @endif

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Current Tenant</h6>
                            @if($flat->tenantAssignments->where('status', 'active')->first())
                                <p class="text-primary mb-0">
                                    {{ $flat->tenantAssignments->where('status', 'active')->first()->tenant->name }}
                                </p>
                            @else
                                <p class="text-muted mb-0">No tenant assigned</p>
                            @endif
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('house-owner.flats.show', $flat) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            <a href="{{ route('house-owner.flats.edit', $flat) }}" class="btn btn-outline-success btn-sm flex-fill">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                            <form action="{{ route('house-owner.flats.destroy', $flat) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this flat? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer text-muted small">
                        <i class="bi bi-calendar me-1"></i>Created {{ $flat->created_at->format('M d, Y') }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $flats->appends(request()->query())->links() }}
    </div>
@else
    <div class="text-center py-5">
        <div class="mb-4">
            <i class="bi bi-house-door" style="font-size: 4rem; color: #6c757d;"></i>
        </div>
        <h5 class="text-muted mb-3">No Flats Found</h5>
        <p class="text-muted mb-4">You haven't added any flats yet. Start by adding your first flat to manage your properties.</p>
        <a href="{{ route('house-owner.flats.create') }}" class="btn btn-success btn-lg">
            <i class="bi bi-plus-circle me-2"></i>Add Your First Flat
        </a>
    </div>
@endif
@endsection
