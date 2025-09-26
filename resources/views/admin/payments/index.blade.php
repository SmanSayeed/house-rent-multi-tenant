@extends('admin.layouts.app')

@section('title', 'Payments Management')
@section('page-title', 'Payments Management')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-credit-card me-2"></i>All Payments
                    </h5>
                </div>
                <div class="card-body">
                    @if ($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Bill ID</th>
                                        <th>Flat</th>
                                        <th>Building</th>
                                        <th>Owner</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Payment Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $payment)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">#{{ $payment->id }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.bills.show', $payment->bill_id) }}"
                                                    class="text-decoration-none">
                                                    #{{ $payment->bill_id }}
                                                </a>
                                            </td>
                                            <td>{{ $payment->bill->flat->flat_number ?? 'N/A' }}</td>
                                            <td>{{ $payment->bill->flat->building->name ?? 'N/A' }}</td>
                                            <td>{{ $payment->bill->flat->building->owner->name ?? 'N/A' }}</td>
                                            <td>৳{{ number_format($payment->paid_amount, 2) }}</td>
                                            <td>
                                                @if ($payment->status === 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.payments.show', $payment->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $payments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-credit-card text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No payments found</h5>
                            <p class="text-muted">No payments have been recorded yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
