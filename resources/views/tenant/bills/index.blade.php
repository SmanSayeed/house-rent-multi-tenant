@extends('tenant.layouts.app')

@section('title', 'My Bills')
@section('page-title', 'My Bills')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-receipt me-2"></i>My Bills
        </h4>
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
            <form method="GET" action="{{ route('tenant.bills') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Search by title or description...">
                </div>
                <div class="col-md-3">
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
                                            <a href="{{ route('tenant.bills.show', $bill) }}"
                                                class="btn btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if ($bill->status !== 'paid')
                                                <button class="btn btn-outline-success"
                                                    onclick="makePayment({{ $bill->id }})">
                                                    <i class="bi bi-credit-card"></i>
                                                </button>
                                            @endif
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
            <p class="text-muted mb-4">You don't have any bills assigned to you yet.</p>
        </div>
    @endif

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Make Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_banking">Mobile Banking</option>
                                <option value="credit_card">Credit Card</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="transaction_id" class="form-label">Transaction ID (Optional)</label>
                            <input type="text" class="form-control" id="transaction_id" name="transaction_id">
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="submitPayment()">Make Payment</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentBillId = null;

        function makePayment(billId) {
            currentBillId = billId;
            // In a real application, you would fetch the bill amount via AJAX
            document.getElementById('amount').value = '15000.00'; // Placeholder
            new bootstrap.Modal(document.getElementById('paymentModal')).show();
        }

        function submitPayment() {
            // In a real application, you would submit the payment via AJAX
            alert('Payment functionality would be implemented here with a payment gateway integration.');
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
        }
    </script>
@endsection
