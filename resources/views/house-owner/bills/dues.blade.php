@extends('house-owner.layouts.app')

@section('title', 'Dues Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>Dues Management
        </h4>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#carryForwardModal">
                <i class="bi bi-arrow-right-circle me-2"></i>Carry Forward Dues
            </button>
            <a href="{{ route('house-owner.bills.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Bills
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
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('house-owner.bills.dues') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ $filters['search'] ?? '' }}" placeholder="Bill title or description">
                </div>
                <div class="col-md-3">
                    <label for="flat_id" class="form-label">Flat</label>
                    <select class="form-select" id="flat_id" name="flat_id">
                        <option value="">All Flats</option>
                        @foreach($flats as $flat)
                            <option value="{{ $flat->id }}" {{ ($filters['flat_id'] ?? '') == $flat->id ? 'selected' : '' }}>
                                {{ $flat->flat_number }} - {{ $flat->building->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ ($filters['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('house-owner.bills.dues') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Dues Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">{{ $dues->total() }}</h5>
                    <p class="card-text">Total Dues</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">{{ $dues->where('status', 'overdue')->count() }}</h5>
                    <p class="card-text">Overdue Bills</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">{{ $dues->where('status', 'pending')->count() }}</h5>
                    <p class="card-text">Pending Bills</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Tk {{ number_format($dues->sum('amount'), 2) }}</h5>
                    <p class="card-text">Total Amount Due</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dues Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-list me-2"></i>Unpaid Bills ({{ $dues->total() }})
            </h5>
        </div>
        <div class="card-body p-0">
            @if($dues->count() > 0)
                <form id="carryForwardForm" action="{{ route('house-owner.bills.carry-forward-dues') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Bill ID</th>
                                    <th>Title</th>
                                    <th>Flat</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Days Overdue</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dues as $bill)
                                    <tr class="{{ $bill->status === 'overdue' ? 'table-danger' : '' }}">
                                        <td>
                                            <input type="checkbox" name="due_bill_ids[]" value="{{ $bill->id }}" 
                                                   class="form-check-input bill-checkbox">
                                        </td>
                                        <td>#{{ $bill->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($bill->category->icon)
                                                    <i class="{{ $bill->category->icon }} me-2" 
                                                       style="color: {{ $bill->category->color ?? '#6c757d' }};"></i>
                                                @endif
                                                {{ $bill->title }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-sm">
                                                <strong>{{ $bill->flat->flat_number }}</strong><br>
                                                <small class="text-muted">{{ $bill->flat->building->name }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge" 
                                                  style="background-color: {{ $bill->category->color ?? '#6c757d' }}20; color: {{ $bill->category->color ?? '#6c757d' }};">
                                                {{ $bill->category->name }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>Tk {{ number_format($bill->amount, 2) }}</strong>
                                        </td>
                                        <td>
                                            @if($bill->status === 'overdue')
                                                <span class="badge bg-danger">Overdue</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $bill->due_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($bill->due_date < now())
                                                <span class="text-danger">
                                                    {{ $bill->due_date->diffInDays(now()) }} days
                                                </span>
                                            @else
                                                <span class="text-muted">Not due yet</span>
                                            @endif
                                        </td>
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
                                                <form action="{{ route('house-owner.bills.mark-as-paid', $bill) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-success btn-sm" 
                                                            title="Mark as Paid"
                                                            onclick="return confirm('Mark this bill as paid?')">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>

                <!-- Pagination -->
                <div class="card-footer">
                    {{ $dues->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-check-circle display-1 text-success"></i>
                    <h5 class="mt-3 text-muted">No dues found</h5>
                    <p class="text-muted">All bills are paid up! Great job managing your properties.</p>
                    <a href="{{ route('house-owner.bills.index') }}" class="btn btn-primary">
                        <i class="bi bi-receipt me-2"></i>View All Bills
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Carry Forward Modal -->
    <div class="modal fade" id="carryForwardModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Carry Forward Dues</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('house-owner.bills.carry-forward-dues') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> Select the bills you want to carry forward and create a new combined bill.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="modal_flat_id" class="form-label">Select Flat *</label>
                                <select class="form-select" id="modal_flat_id" name="flat_id" required>
                                    <option value="">Choose a flat</option>
                                    @foreach($flats as $flat)
                                        <option value="{{ $flat->id }}">
                                            {{ $flat->flat_number }} - {{ $flat->building->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="modal_category_id" class="form-label">Select Category *</label>
                                <select class="form-select" id="modal_category_id" name="category_id" required>
                                    <option value="">Choose a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="modal_title" class="form-label">New Bill Title *</label>
                            <input type="text" class="form-control" id="modal_title" name="title" 
                                   placeholder="e.g., Combined Dues - January 2024" required>
                        </div>

                        <div class="mb-3">
                            <label for="modal_description" class="form-label">Description</label>
                            <textarea class="form-control" id="modal_description" name="description" 
                                      rows="3" placeholder="Optional description for the combined bill"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="modal_due_date" class="form-label">New Due Date *</label>
                            <input type="date" class="form-control" id="modal_due_date" name="due_date" 
                                   value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Selected Bills</label>
                            <div id="selectedBills" class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                <p class="text-muted text-center">No bills selected</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="carryForwardBtn" disabled>
                            <i class="bi bi-arrow-right-circle me-2"></i>Carry Forward Dues
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const billCheckboxes = document.querySelectorAll('.bill-checkbox');
    const selectedBillsDiv = document.getElementById('selectedBills');
    const carryForwardBtn = document.getElementById('carryForwardBtn');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        billCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedBills();
    });

    // Individual checkbox change
    billCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedBills();
            updateSelectAllState();
        });
    });

    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.bill-checkbox:checked');
        selectAllCheckbox.checked = checkedBoxes.length === billCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < billCheckboxes.length;
    }

    function updateSelectedBills() {
        const checkedBoxes = document.querySelectorAll('.bill-checkbox:checked');
        const selectedBills = Array.from(checkedBoxes).map(checkbox => {
            const row = checkbox.closest('tr');
            const billId = row.querySelector('td:nth-child(2)').textContent;
            const title = row.querySelector('td:nth-child(3)').textContent.trim();
            const amount = row.querySelector('td:nth-child(6)').textContent.trim();
            return { id: billId, title, amount };
        });

        if (selectedBills.length === 0) {
            selectedBillsDiv.innerHTML = '<p class="text-muted text-center">No bills selected</p>';
            carryForwardBtn.disabled = true;
        } else {
            const totalAmount = selectedBills.reduce((sum, bill) => {
                return sum + parseFloat(bill.amount.replace(/[^\d.-]/g, ''));
            }, 0);

            selectedBillsDiv.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Selected Bills (${selectedBills.length})</strong>
                    <strong class="text-success">Total: Tk ${totalAmount.toFixed(2)}</strong>
                </div>
                <div class="list-group list-group-flush">
                    ${selectedBills.map(bill => `
                        <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <div>
                                <strong>${bill.id}</strong> - ${bill.title}
                            </div>
                            <span class="badge bg-primary">${bill.amount}</span>
                        </div>
                    `).join('')}
                </div>
            `;
            carryForwardBtn.disabled = false;
        }
    }

    // Form submission
    document.getElementById('carryForwardForm').addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.bill-checkbox:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one bill to carry forward.');
            return;
        }

        // Add selected bill IDs to the form
        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'due_bill_ids[]';
            input.value = checkbox.value;
            this.appendChild(input);
        });
    });
});
</script>
@endsection
