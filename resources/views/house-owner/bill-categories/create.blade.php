@extends('house-owner.layouts.app')

@section('title', 'Create Bill Category')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="bi bi-plus-circle me-2"></i>Create New Bill Category
    </h4>
    <a href="{{ route('house-owner.bill-categories.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Categories
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-tag me-2"></i>Category Information
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('house-owner.bill-categories.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Category Name *</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="e.g., Electricity, Water, Maintenance" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">A unique name for this bill category</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                            <div class="form-text">Inactive categories won't appear in bill creation</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Optional description for this category">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="icon" class="form-label">Icon Class</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i id="icon-preview" class="bi bi-tag"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" 
                                       name="icon" 
                                       value="{{ old('icon') }}" 
                                       placeholder="e.g., bi-lightning, bi-droplet, bi-fire">
                            </div>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Bootstrap Icons class name (optional)</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="color" class="form-label">Color</label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       id="color" 
                                       name="color" 
                                       value="{{ old('color', '#6c757d') }}"
                                       title="Choose a color">
                                <input type="text" 
                                       class="form-control @error('color') is-invalid @enderror" 
                                       id="color-text" 
                                       value="{{ old('color', '#6c757d') }}"
                                       placeholder="#6c757d">
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Color for the category (optional)</div>
                        </div>
                    </div>

                    <!-- Icon Examples -->
                    <div class="mb-4">
                        <label class="form-label">Popular Icons</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="bi-lightning">
                                <i class="bi bi-lightning me-1"></i>Electricity
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="bi-droplet">
                                <i class="bi bi-droplet me-1"></i>Water
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="bi-fire">
                                <i class="bi bi-fire me-1"></i>Gas
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="bi-tools">
                                <i class="bi bi-tools me-1"></i>Maintenance
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="bi-wifi">
                                <i class="bi bi-wifi me-1"></i>Internet
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="bi-telephone">
                                <i class="bi bi-telephone me-1"></i>Phone
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="bi-trash">
                                <i class="bi bi-trash me-1"></i>Waste
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="bi-shield-check">
                                <i class="bi bi-shield-check me-1"></i>Security
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> Bill categories help you organize different types of bills. You can create categories for electricity, water, gas, maintenance, or any other type of bill you need to track.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i>Create Category
                        </button>
                        <a href="{{ route('house-owner.bill-categories.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Icon preview and selection
document.getElementById('icon').addEventListener('input', function() {
    const iconPreview = document.getElementById('icon-preview');
    const iconClass = this.value || 'bi-tag';
    iconPreview.className = iconClass;
});

// Icon button selection
document.querySelectorAll('.icon-btn').forEach(button => {
    button.addEventListener('click', function() {
        const icon = this.getAttribute('data-icon');
        document.getElementById('icon').value = icon;
        document.getElementById('icon-preview').className = icon;
    });
});

// Color picker synchronization
document.getElementById('color').addEventListener('input', function() {
    document.getElementById('color-text').value = this.value;
});

document.getElementById('color-text').addEventListener('input', function() {
    if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
        document.getElementById('color').value = this.value;
    }
});
</script>
@endsection
