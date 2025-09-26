@extends('house-owner.layouts.app')

@section('title', 'Bill Category Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="bi bi-tag me-2"></i>Bill Category Details
    </h4>
    <div class="d-flex gap-2">
        <a href="{{ route('house-owner.bill-categories.edit', $billCategory) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-2"></i>Edit Category
        </a>
        <a href="{{ route('house-owner.bill-categories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Categories
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <!-- Category Information -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header" 
                 style="background-color: {{ $billCategory->color ?? '#6c757d' }}20; border-left: 4px solid {{ $billCategory->color ?? '#6c757d' }};">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    @if($billCategory->icon)
                        <i class="{{ $billCategory->icon }} me-2" style="color: {{ $billCategory->color ?? '#6c757d' }};"></i>
                    @else
                        <i class="bi bi-tag me-2" style="color: {{ $billCategory->color ?? '#6c757d' }};"></i>
                    @endif
                    {{ $billCategory->name }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Description</label>
                        <p class="mb-0">{{ $billCategory->description ?: 'No description provided' }}</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Status</label>
                        <p class="mb-0">
                            @if($billCategory->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Icon</label>
                        <p class="mb-0">
                            @if($billCategory->icon)
                                <i class="{{ $billCategory->icon }} me-2"></i>{{ $billCategory->icon }}
                            @else
                                <i class="bi bi-tag me-2"></i>Default (bi-tag)
                            @endif
                        </p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Color</label>
                        <p class="mb-0">
                            <span class="badge" style="background-color: {{ $billCategory->color ?? '#6c757d' }};">
                                {{ $billCategory->color ?? '#6c757d' }}
                            </span>
                        </p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Created</label>
                        <p class="mb-0">{{ $billCategory->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Last Updated</label>
                        <p class="mb-0">{{ $billCategory->updated_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <a href="{{ route('house-owner.bill-categories.edit', $billCategory) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <form action="{{ route('house-owner.bill-categories.toggle-status', $billCategory) }}" 
                          method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $billCategory->is_active ? 'secondary' : 'success' }} btn-sm">
                            <i class="bi bi-{{ $billCategory->is_active ? 'pause' : 'play' }} me-1"></i>
                            {{ $billCategory->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    <form action="{{ route('house-owner.bill-categories.destroy', $billCategory) }}" 
                          method="POST" class="d-inline"
                          onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bills in this Category -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-receipt me-2"></i>Bills in this Category
                </h5>
                <a href="{{ route('house-owner.bills.create') }}?category={{ $billCategory->id }}" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-circle me-1"></i>Create Bill
                </a>
            </div>
            <div class="card-body">
                @if($billCategory->bills->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Bill ID</th>
                                    <th>Title</th>
                                    <th>Flat</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($billCategory->bills as $bill)
                                    <tr>
                                        <td>#{{ $bill->id }}</td>
                                        <td>{{ $bill->title }}</td>
                                        <td>
                                            <div class="text-sm">
                                                <strong>{{ $bill->flat->flat_number }}</strong><br>
                                                <small class="text-muted">{{ $bill->flat->building->name }}</small>
                                            </div>
                                        </td>
                                        <td>Tk {{ number_format($bill->amount, 2) }}</td>
                                        <td>
                                            @if($bill->status === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($bill->status === 'overdue')
                                                <span class="badge bg-danger">Overdue</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $bill->due_date->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('house-owner.bills.show', $bill) }}" 
                                                   class="btn btn-outline-primary btn-sm" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('house-owner.bills.edit', $bill) }}" 
                                                   class="btn btn-outline-warning btn-sm" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-receipt display-4 text-muted"></i>
                        <h5 class="mt-3 text-muted">No bills in this category</h5>
                        <p class="text-muted">Create your first bill using this category.</p>
                        <a href="{{ route('house-owner.bills.create') }}?category={{ $billCategory->id }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>Create First Bill
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Category Statistics -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>Category Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h4 class="text-primary">{{ $billCategory->bills->count() }}</h4>
                        <small class="text-muted">Total Bills</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-success">{{ $billCategory->bills->where('status', 'paid')->count() }}</h4>
                        <small class="text-muted">Paid Bills</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-warning">{{ $billCategory->bills->where('status', 'pending')->count() }}</h4>
                        <small class="text-muted">Pending Bills</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-danger">{{ $billCategory->bills->where('status', 'overdue')->count() }}</h4>
                        <small class="text-muted">Overdue Bills</small>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-12">
                        <h4 class="text-info">
                            Tk {{ number_format($billCategory->bills->sum('amount'), 2) }}
                        </h4>
                        <small class="text-muted">Total Amount</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
