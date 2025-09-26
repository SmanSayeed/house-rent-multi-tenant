@extends('house-owner.layouts.app')

@section('title', 'Edit Flat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="bi bi-pencil me-2"></i>Edit Flat
    </h4>
    <a href="{{ route('house-owner.flats.show', $flat) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Flat
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-house-door me-2"></i>{{ $flat->flat_number }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('house-owner.flats.update', $flat) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="building_id" class="form-label">Building *</label>
                            <select class="form-control @error('building_id') is-invalid @enderror" 
                                    id="building_id" 
                                    name="building_id" 
                                    required>
                                <option value="">Select Building</option>
                                @foreach($buildings as $building)
                                    <option value="{{ $building->id }}" 
                                            {{ old('building_id', $flat->building_id) == $building->id ? 'selected' : '' }}>
                                        {{ $building->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('building_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="flat_number" class="form-label">Flat Number *</label>
                            <input type="text" 
                                   class="form-control @error('flat_number') is-invalid @enderror" 
                                   id="flat_number" 
                                   name="flat_number" 
                                   value="{{ old('flat_number', $flat->flat_number) }}" 
                                   required 
                                   autofocus>
                            @error('flat_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="floor" class="form-label">Floor *</label>
                            <input type="number" 
                                   class="form-control @error('floor') is-invalid @enderror" 
                                   id="floor" 
                                   name="floor" 
                                   value="{{ old('floor', $flat->floor) }}" 
                                   min="0" 
                                   max="100" 
                                   required>
                            @error('floor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="rent_amount" class="form-label">Rent Amount *</label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number" 
                                       class="form-control @error('rent_amount') is-invalid @enderror" 
                                       id="rent_amount" 
                                       name="rent_amount" 
                                       value="{{ old('rent_amount', $flat->rent_amount) }}" 
                                       step="0.01" 
                                       min="0" 
                                       required>
                                @error('rent_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="">Select Status</option>
                                <option value="available" {{ old('status', $flat->status) == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="occupied" {{ old('status', $flat->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="maintenance" {{ old('status', $flat->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  placeholder="Optional description about the flat...">{{ old('description', $flat->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i>Update Flat
                        </button>
                        <a href="{{ route('house-owner.flats.show', $flat) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
