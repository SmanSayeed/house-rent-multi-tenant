@extends('tenant.layouts.app')

@section('title', 'Bill Details')
@section('page-title', 'Bill Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-receipt me-2"></i>Bill Details
        </h4>
        <a href="{{ route('tenant.bills') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Bills
        </a>
    </div>

    <div class="row">
        <!-- Bill Information -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt me-2"></i>Bill Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Bill ID</h6>
                            <p class="mb-0"><span class="badge bg-secondary">#{{ $bill->id }}</span></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Title</h6>
                            <p class="mb-0">{{ $bill->title }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Category</h6>
                            <span class="badge bg-primary">{{ $bill->category->name }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Amount</h6>
                            <p class="mb-0 text-success fw-bold fs-5">৳{{ number_format($bill->amount, 2) }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Due Date</h6>
                            <p class="mb-0">{{ $bill->due_date->format('M d, Y') }}</p>
                            @if ($bill->due_date->isPast() && $bill->status !== 'paid')
                                <small class="text-danger">Overdue</small>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Status</h6>
                            @if ($bill->status === 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($bill->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-danger">Overdue</span>
                            @endif
                        </div>
                    </div>

                    @if ($bill->description)
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Description</h6>
                            <p class="mb-0">{{ $bill->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Flat Information -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-house-door me-2"></i>Flat Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Flat Number</h6>
                        <p class="mb-0">{{ $bill->flat->flat_number }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Floor</h6>
                        <p class="mb-0">{{ $bill->flat->floor }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Building</h6>
                        <p class="mb-0">{{ $bill->flat->building->name }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Address</h6>
                        <p class="mb-0">{{ $bill->flat->building->address }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Actions -->
    @if ($bill->status !== 'paid')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-credit-card me-2"></i>Payment Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-success" onclick="makePayment({{ $bill->id }})">
                                <i class="bi bi-credit-card me-2"></i>Make Payment
                            </button>
                            <button class="btn btn-outline-info">
                                <i class="bi bi-download me-2"></i>Download Invoice
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
            document.getElementById('amount').value = '{{ $bill->amount }}';
            new bootstrap.Modal(document.getElementById('paymentModal')).show();
        }

        function submitPayment() {
            alert('Payment functionality would be implemented here with a payment gateway integration.');
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
        }
    </script>
@endsection
