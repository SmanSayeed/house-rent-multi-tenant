@extends('house-owner.layouts.app')

@section('title', 'My Buildings')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-building me-2"></i>My Buildings
        </h4>
        <a href="{{ route('house-owner.buildings.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-2"></i>Add New Building
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

    @if ($buildings->count() > 0)
        <div class="row">
            @foreach ($buildings as $building)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-building me-2"></i>{{ $building->name }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Address</h6>
                                <p class="mb-1">{{ $building->address }}</p>
                                <small class="text-muted">{{ $building->city }}, {{ $building->state }}
                                    {{ $building->postal_code }}</small>
                            </div>

                            @if ($building->description)
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Description</h6>
                                    <p class="text-muted small">{{ Str::limit($building->description, 100) }}</p>
                                </div>
                            @endif

                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <h4 class="text-success mb-0">{{ $building->flats->count() }}</h4>
                                    <small class="text-muted">Total Flats</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-info mb-0">{{ $building->flats->where('status', 'occupied')->count() }}
                                    </h4>
                                    <small class="text-muted">Occupied</small>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('house-owner.buildings.show', $building) }}"
                                    class="btn btn-outline-success btn-sm flex-fill">
                                    <i class="bi bi-eye me-1"></i>View
                                </a>
                                <a href="{{ route('house-owner.buildings.edit', $building) }}"
                                    class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                                <form action="{{ route('house-owner.buildings.destroy', $building) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this building? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-footer text-muted small">
                            <i class="bi bi-calendar me-1"></i>Created {{ $building->created_at->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $buildings->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-building" style="font-size: 4rem; color: #6c757d;"></i>
            </div>
            <h5 class="text-muted mb-3">No Buildings Found</h5>
            <p class="text-muted mb-4">You haven't added any buildings yet. Start by adding your first building to manage
                your properties.</p>
            <a href="{{ route('house-owner.buildings.create') }}" class="btn btn-success btn-lg">
                <i class="bi bi-plus-circle me-2"></i>Add Your First Building
            </a>
        </div>
    @endif
@endsection
