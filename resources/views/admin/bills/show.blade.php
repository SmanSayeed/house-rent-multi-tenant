@extends('admin.layouts.app')

@section('title', 'Bill Details')
@section('page-title', 'Bill Details')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt me-2"></i>Bill #{{ $bill->id }}
                    </h5>
                    <a href="{{ route('admin.bills') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Bills
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Bill Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Bill ID:</strong></td>
                                    <td>#{{ $bill->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td>{{ $bill->category->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td>৳{{ number_format($bill->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if ($bill->status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-warning">Unpaid</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Due Date:</strong></td>
                                    <td>{{ $bill->due_date ? $bill->due_date->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Property Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Flat:</strong></td>
                                    <td>{{ $bill->flat->flat_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Building:</strong></td>
                                    <td>{{ $bill->flat->building->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Owner:</strong></td>
                                    <td>{{ $bill->flat->building->owner->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $bill->flat->building->address ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if ($bill->payments->count() > 0)
                        <hr>
                        <h6>Payment History</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Payment Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bill->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                            <td>৳{{ number_format($payment->paid_amount, 2) }}</td>
                                            <td>
                                                @if ($payment->status === 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
