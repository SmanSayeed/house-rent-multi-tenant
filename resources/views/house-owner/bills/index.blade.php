@extends('house-owner.layouts.app')

@section('title', 'My Bills')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-receipt me-2"></i>My Bills
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('house-owner.bills.dues') }}" class="btn btn-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>Manage Dues
            </a>
            <a href="{{ route('house-owner.bills.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle me-2"></i>Create New Bill
            </a>
        </div>
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
            <form method="GET" action="{{ route('house-owner.bills.index') }}" class="row g-3">
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
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                        value="{{ request('search') }}" placeholder="Search by title or description...">
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

    @if ($bills->count() > 0)
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Bill ID</th>
                                <th>Title</th>
                                <th>Flat</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bills as $bill)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">#{{ $bill->id }}</span>
                                    </td>
                                    <td>
                                        <h6 class="mb-0">{{ $bill->title }}</h6>
                                        @if ($bill->description)
                                            <small class="text-muted">{{ Str::limit($bill->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $bill->flat->flat_number }}</span>
                                        <br>
                                        <small class="text-muted">{{ $bill->flat->building->name }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $bill->category->name }}</span>
                                    </td>
                                    <td class="text-success fw-bold">৳{{ number_format($bill->amount, 2) }}</td>
                                    <td>
                                        {{ $bill->due_date->format('M d, Y') }}
                                        @if ($bill->due_date->isPast() && $bill->status !== 'paid')
                                            <br><small class="text-danger">Overdue</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($bill->status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($bill->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Overdue</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('house-owner.bills.show', $bill) }}"
                                                class="btn btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('house-owner.bills.edit', $bill) }}"
                                                class="btn btn-outline-success">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('house-owner.bills.invoice', $bill) }}"
                                                class="btn btn-outline-info" title="Download Invoice">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            @if ($bill->status !== 'paid')
                                                <form action="{{ route('house-owner.bills.mark-as-paid', $bill) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to mark this bill as paid?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-success"
                                                        title="Mark as Paid">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('house-owner.bills.destroy', $bill) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this bill? This action cannot be undone.')">
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
            {{ $bills->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-receipt" style="font-size: 4rem; color: #6c757d;"></i>
            </div>
            <h5 class="text-muted mb-3">No Bills Found</h5>
            <p class="text-muted mb-4">You haven't created any bills yet. Start by creating your first bill for your
                tenants.</p>
            <a href="{{ route('house-owner.bills.create') }}" class="btn btn-success btn-lg">
                <i class="bi bi-plus-circle me-2"></i>Create Your First Bill
            </a>
        </div>
    @endif
@endsection
