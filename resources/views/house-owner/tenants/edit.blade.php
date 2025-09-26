@extends('house-owner.layouts.app')

@section('title', 'Edit Tenant Assignment')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-pencil me-2"></i>Edit Tenant Assignment
        </h4>
        <a href="{{ route('house-owner.tenants.show', $tenant) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Tenant
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-check me-2"></i>{{ $tenant->tenant->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('house-owner.tenants.update', $tenant) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tenant_id" class="form-label">Tenant *</label>
                                <select class="form-control @error('tenant_id') is-invalid @enderror" id="tenant_id"
                                    name="tenant_id" required>
                                    <option value="{{ $tenant->tenant_id }}" selected>{{ $tenant->tenant->name }}
                                        ({{ $tenant->tenant->email }})</option>
                                </select>
                                @error('tenant_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="building_id" class="form-label">Building *</label>
                                <select class="form-control @error('building_id') is-invalid @enderror" id="building_id"
                                    name="building_id" required>
                                    <option value="">Select Building</option>
                                    @foreach ($buildings as $building)
                                        <option value="{{ $building->id }}"
                                            {{ old('building_id', $tenant->building_id) == $building->id ? 'selected' : '' }}>
                                            {{ $building->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('building_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="flat_id" class="form-label">Flat *</label>
                                <select class="form-control @error('flat_id') is-invalid @enderror" id="flat_id"
                                    name="flat_id" required>
                                    <option value="">Select Flat</option>
                                    @foreach ($flats as $flat)
                                        <option value="{{ $flat->id }}" data-building="{{ $flat->building_id }}"
                                            data-rent="{{ $flat->rent_amount }}"
                                            {{ old('flat_id', $tenant->flat_id) == $flat->id ? 'selected' : '' }}>
                                            {{ $flat->flat_number }} - Floor {{ $flat->floor }}
                                            (৳{{ number_format($flat->rent_amount, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('flat_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="monthly_rent" class="form-label">Monthly Rent *</label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" class="form-control @error('monthly_rent') is-invalid @enderror"
                                        id="monthly_rent" name="monthly_rent"
                                        value="{{ old('monthly_rent', $tenant->monthly_rent) }}" step="0.01"
                                        min="0" required>
                                    @error('monthly_rent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date *</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                    id="start_date" name="start_date"
                                    value="{{ old('start_date', $tenant->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                    id="end_date" name="end_date"
                                    value="{{ old('end_date', $tenant->end_date ? $tenant->end_date->format('Y-m-d') : '') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Leave empty for indefinite assignment</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status"
                                required>
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status', $tenant->status) == 'active' ? 'selected' : '' }}>
                                    Active</option>
                                <option value="inactive"
                                    {{ old('status', $tenant->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="terminated"
                                    {{ old('status', $tenant->status) == 'terminated' ? 'selected' : '' }}>Terminated
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>Update Assignment
                            </button>
                            <a href="{{ route('house-owner.tenants.show', $tenant) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buildingSelect = document.getElementById('building_id');
            const flatSelect = document.getElementById('flat_id');
            const monthlyRentInput = document.getElementById('monthly_rent');

            // Filter flats based on selected building
            buildingSelect.addEventListener('change', function() {
                const selectedBuildingId = this.value;
                const flatOptions = flatSelect.querySelectorAll('option');

                flatOptions.forEach(option => {
                    if (option.value === '') {
                        option.style.display = 'block';
                        return;
                    }

                    const buildingId = option.getAttribute('data-building');
                    if (buildingId === selectedBuildingId) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                });

                // Reset flat selection if it doesn't belong to selected building
                const selectedFlat = flatSelect.options[flatSelect.selectedIndex];
                if (selectedFlat.value && selectedFlat.getAttribute('data-building') !==
                    selectedBuildingId) {
                    flatSelect.value = '';
                    monthlyRentInput.value = '';
                }
            });

            // Auto-fill monthly rent when flat is selected
            flatSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value && selectedOption.getAttribute('data-rent')) {
                    monthlyRentInput.value = selectedOption.getAttribute('data-rent');
                }
            });

            // Set minimum date for start date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start_date').setAttribute('min', today);
        });
    </script>
@endsection
