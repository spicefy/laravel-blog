@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Category</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Categories
                        </a>
                    </div>
                </div>
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <h5><i class="icon fas fa-ban"></i> Validation Errors!</h5>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Enter category name"
                                   required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="slug">Slug</label>
                            <input type="text" 
                                   class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" 
                                   name="slug" 
                                   value="{{ old('slug') }}" 
                                   placeholder="auto-generated-from-name">
                            <small class="form-text text-muted">Leave empty to auto-generate from name</small>
                            @error('slug')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="icon">Icon</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" 
                                       name="icon" 
                                       placeholder="fas fa-tag"
                                       value="{{ old('icon', 'fas fa-tag') }}">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="{{ old('icon', 'fas fa-tag') }}"></i>
                                    </span>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Font Awesome icon class. Examples: 
                                <code>fas fa-dollar-sign</code>, 
                                <code>fas fa-tag</code>, 
                                <code>fas fa-newspaper</code>
                            </small>
                            @error('icon')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="css_suffix">CSS Suffix (Color)</label>
                            <select class="form-control @error('css_suffix') is-invalid @enderror" id="css_suffix" name="css_suffix">
                                <option value="purple-600" {{ old('css_suffix') == 'purple-600' ? 'selected' : '' }}>Purple</option>
                                <option value="blue-600" {{ old('css_suffix') == 'blue-600' ? 'selected' : '' }}>Blue</option>
                                <option value="green-600" {{ old('css_suffix') == 'green-600' ? 'selected' : '' }}>Green</option>
                                <option value="red-600" {{ old('css_suffix') == 'red-600' ? 'selected' : '' }}>Red</option>
                                <option value="orange-600" {{ old('css_suffix') == 'orange-600' ? 'selected' : '' }}>Orange</option>
                                <option value="pink-600" {{ old('css_suffix') == 'pink-600' ? 'selected' : '' }}>Pink</option>
                                <option value="indigo-600" {{ old('css_suffix') == 'indigo-600' ? 'selected' : '' }}>Indigo</option>
                                <option value="teal-600" {{ old('css_suffix') == 'teal-600' ? 'selected' : '' }}>Teal</option>
                                <option value="cyan-600" {{ old('css_suffix') == 'cyan-600' ? 'selected' : '' }}>Cyan</option>
                                <option value="gray-600" {{ old('css_suffix') == 'gray-600' ? 'selected' : '' }}>Gray</option>
                            </select>
                            <small class="form-text text-muted">Color scheme for category styling on the frontend</small>
                            @error('css_suffix')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
                                      placeholder="Enter category description (optional)">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                            <small class="form-text text-muted">Inactive categories won't be displayed on the frontend</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Category
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    nameInput.addEventListener('blur', function() {
        if (!slugInput.value || slugInput.value === '') {
            let slug = this.value.toLowerCase()
                .replace(/[^\w\s-]/g, '')  // Remove special characters
                .replace(/\s+/g, '-')       // Replace spaces with hyphens
                .replace(/--+/g, '-')       // Replace multiple hyphens
                .trim();
            slugInput.value = slug;
        }
    });

    // Live icon preview
    const iconInput = document.getElementById('icon');
    const iconPreview = document.querySelector('#icon + .input-group-append .input-group-text i');
    
    iconInput.addEventListener('input', function() {
        let iconClass = this.value.trim();
        if (iconClass) {
            iconPreview.className = iconClass;
        } else {
            iconPreview.className = 'fas fa-tag';
        }
    });
</script>
@endpush