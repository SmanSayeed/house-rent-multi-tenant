@extends('house-owner.layouts.app')

@section('title', 'Bill Categories')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-tags me-2"></i>Bill Categories
        </h4>
        <a href="{{ route('house-owner.bill-categories.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-2"></i>Create New Category
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
            <form method="GET" action="{{ route('house-owner.bill-categories.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ $filters['search'] ?? '' }}" placeholder="Category name or description">
                </div>
                <div class="col-md-3">
                    <label for="is_active" class="form-label">Status</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="">All Status</option>
                        <option value="1" {{ ($filters['is_active'] ?? '') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ ($filters['is_active'] ?? '') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('house-owner.bill-categories.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bill Categories Grid -->
    <div class="row">
        @if($billCategories->count() > 0)
            @foreach($billCategories as $category)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center" 
                             style="background-color: {{ $category->color ?? '#6c757d' }}20; border-left: 4px solid {{ $category->color ?? '#6c757d' }};">
                            <div class="d-flex align-items-center">
                                @if($category->icon)
                                    <i class="{{ $category->icon }} me-2" style="color: {{ $category->color ?? '#6c757d' }};"></i>
                                @else
                                    <i class="bi bi-tag me-2" style="color: {{ $category->color ?? '#6c757d' }};"></i>
                                @endif
                                <h6 class="mb-0">{{ $category->name }}</h6>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('house-owner.bill-categories.show', $category) }}">
                                            <i class="bi bi-eye me-2"></i>View Details
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('house-owner.bill-categories.edit', $category) }}">
                                            <i class="bi bi-pencil me-2"></i>Edit
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('house-owner.bill-categories.toggle-status', $category) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-{{ $category->is_active ? 'pause' : 'play' }} me-2"></i>
                                                {{ $category->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('house-owner.bill-categories.destroy', $category) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text text-muted">
                                {{ $category->description ? Str::limit($category->description, 100) : 'No description provided' }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($category->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    {{ $category->bills()->whereHas('flat.building', function($query) {
                                        $query->where('owner_id', Auth::id());
                                    })->count() }} bills
                                </small>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Created {{ $category->created_at->diffForHumans() }}
                                </small>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('house-owner.bill-categories.show', $category) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('house-owner.bill-categories.edit', $category) }}" 
                                       class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-tags display-1 text-muted"></i>
                    <h5 class="mt-3 text-muted">No bill categories found</h5>
                    <p class="text-muted">Get started by creating your first bill category.</p>
                    <a href="{{ route('house-owner.bill-categories.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create First Category
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($billCategories->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $billCategories->appends(request()->query())->links() }}
        </div>
    @endif

    <!-- Predefined Categories Info -->
    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-info-circle me-2"></i>Predefined Categories
            </h5>
        </div>
        <div class="card-body">
            <p class="mb-3">The system comes with these predefined bill categories that you can use:</p>
            <div class="row">
                <div class="col-md-3 mb-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-lightning text-warning me-2"></i>
                        <span>Electricity</span>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-fire text-danger me-2"></i>
                        <span>Gas Bill</span>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-droplet text-primary me-2"></i>
                        <span>Water Bill</span>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-tools text-secondary me-2"></i>
                        <span>Utility Charges</span>
                    </div>
                </div>
            </div>
            <p class="text-muted small mt-3 mb-0">
                <i class="bi bi-lightbulb me-1"></i>
                <strong>Tip:</strong> You can create custom categories for any type of bill you need to track.
            </p>
        </div>
    </div>
@endsection
