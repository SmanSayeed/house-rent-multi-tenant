@extends('admin.layouts.app')

@section('title', 'Create New Tenant')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="bi bi-person-plus me-2"></i>Create New Tenant
    </h4>
    <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Tenants
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-plus me-2"></i>Tenant Information
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tenants.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Enter tenant's full name" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="Enter tenant's email address" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter password (min 6 characters)" 
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password *</label>
                            <input type="password" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Confirm password" 
                                   required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="contact" class="form-label">Contact Number</label>
                        <input type="text" 
                               class="form-control @error('contact') is-invalid @enderror" 
                               id="contact" 
                               name="contact" 
                               value="{{ old('contact') }}" 
                               placeholder="Enter contact number (optional)">
                        @error('contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Optional: Phone number for communication</div>
                    </div>

                    <hr class="my-4">
                    <h6 class="text-muted mb-3">
                        <i class="bi bi-house me-2"></i>Flat Assignment (Optional)
                    </h6>
                    <p class="text-muted small mb-3">You can assign the tenant to a flat now or do it later from the tenant details page.</p>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="flat_id" class="form-label">Select Flat</label>
                            <select class="form-select @error('flat_id') is-invalid @enderror" 
                                    id="flat_id" 
                                    name="flat_id">
                                <option value="">Choose a flat (optional)</option>
                                @foreach($buildings as $building)
                                    <optgroup label="{{ $building->name }}">
                                        @foreach($building->flats as $flat)
                                            <option value="{{ $flat->id }}" 
                                                    {{ old('flat_id') == $flat->id ? 'selected' : '' }}
                                                    data-building="{{ $building->name }}"
                                                    data-rent="{{ $flat->rent_amount }}">
                                                {{ $flat->flat_number }} - {{ $building->name }}
                                                @if($flat->rent_amount)
                                                    (Tk {{ number_format($flat->rent_amount) }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('flat_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="monthly_rent" class="form-label">Monthly Rent</label>
                            <input type="number" 
                                   class="form-control @error('monthly_rent') is-invalid @enderror" 
                                   id="monthly_rent" 
                                   name="monthly_rent" 
                                   value="{{ old('monthly_rent') }}" 
                                   placeholder="0.00" 
                                   step="0.01" 
                                   min="0">
                            @error('monthly_rent')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ old('start_date', date('Y-m-d')) }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> If you don't assign a flat now, you can do it later from the tenant details page. The tenant will be created but won't have access to any flat until assigned.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Create Tenant
                        </button>
                        <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-fill monthly rent when flat is selected
document.getElementById('flat_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const rentAmount = selectedOption.getAttribute('data-rent');
    const monthlyRentInput = document.getElementById('monthly_rent');
    
    if (rentAmount && rentAmount !== 'null') {
        monthlyRentInput.value = rentAmount;
    } else {
        monthlyRentInput.value = '';
    }
});

// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strength = getPasswordStrength(password);
    
    // You can add a visual strength indicator here if needed
    console.log('Password strength:', strength);
});

function getPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    return strength;
}
</script>
@endsection
