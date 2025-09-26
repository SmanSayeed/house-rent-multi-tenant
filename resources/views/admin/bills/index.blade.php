@extends('admin.layouts.app')

@section('title', 'Bills Management')
@section('page-title', 'Bills Management')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt me-2"></i>All Bills
                    </h5>
                </div>
                <div class="card-body">
                    @if ($bills->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Bill ID</th>
                                        <th>Flat</th>
                                        <th>Building</th>
                                        <th>Owner</th>
                                        <th>Category</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bills as $bill)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">#{{ $bill->id }}</span>
                                            </td>
                                            <td>{{ $bill->flat->flat_number ?? 'N/A' }}</td>
                                            <td>{{ $bill->flat->building->name ?? 'N/A' }}</td>
                                            <td>{{ $bill->flat->building->owner->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $bill->category->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>৳{{ number_format($bill->amount, 2) }}</td>
                                            <td>
                                                @if ($bill->status === 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @else
                                                    <span class="badge bg-warning">Unpaid</span>
                                                @endif
                                            </td>
                                            <td>{{ $bill->due_date ? $bill->due_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('admin.bills.show', $bill->id) }}"
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
                            {{ $bills->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No bills found</h5>
                            <p class="text-muted">No bills have been created yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
