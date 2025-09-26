@extends('admin.layouts.app')

@section('title', 'Buildings Management')
@section('page-title', 'Buildings Management')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2"></i>All Buildings
                    </h5>
                </div>
                <div class="card-body">
                    @if ($buildings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Building Name</th>
                                        <th>Address</th>
                                        <th>Owner</th>
                                        <th>Flats Count</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($buildings as $building)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i class="bi bi-building"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $building->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $building->address ?? 'N/A' }}</td>
                                            <td>{{ $building->owner->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $building->flats->count() }}</span>
                                            </td>
                                            <td>{{ $building->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ url('/admin/buildings/' . $building->id) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                    <a href="{{ url('/admin/buildings/' . $building->id . '/flats') }}"
                                                        class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-house-door"></i> Flats
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $buildings->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No buildings found</h5>
                            <p class="text-muted">No buildings have been created yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
