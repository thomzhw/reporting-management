@extends('layouts.header')

@section('content')

<!-- Topbar -->
@include('layouts.topbar')
<!-- End of Topbar -->

<style>
    /* Add consistent styling for photos */
    .photo-card {
        height: 100%;
    }
    
    .photo-card .card-img-top {
        height: 160px;
        object-fit: cover;
        width: 100%;
    }
    
    .photo-preview {
        height: 160px;
        object-fit: cover;
        width: 100%;
        border-radius: 4px;
        margin-top: 8px;
    }
    
    .photo-item {
        margin-bottom: 15px;
    }
    
    .photo-container {
        margin-bottom: 15px;
    }
</style>
    
<div class="container">
    <h2>Edit Report</h2>
    
    <form method="POST" action="{{ route('head.qa-templates.update', $template) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Report Name</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $template->name) }}">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="outlet_id">Remote</label>
                    <select name="outlet_id" class="form-control" required>
                        <option value="">-- Select Outlet --</option>
                        @foreach($outlets as $outletItem)
                            <option value="{{ $outletItem->id }}" {{ (old('outlet_id', $template->outlet_id) == $outletItem->id) ? 'selected' : '' }}>
                                {{ $outletItem->name }} ({{ $outletItem->city }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="category">Category (Optional)</label>
                    <input type="text" name="category" class="form-control" value="{{ old('category', $template->category) }}" placeholder="e.g., Cleanliness, Safety">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $template->description) }}</textarea>
                </div>
            </div>
        </div>
        
        <h4>Rules</h4>
        <div id="rules-container">
            @foreach($template->rules as $index => $rule)
                <div class="rule-item card mb-3">
                    <div class="card-body">
                        <input type="hidden" name="rules[{{ $index }}][id]" value="{{ $rule->id }}">
                        
                        <div class="form-group">
                            <label>Rule Title</label>
                            <input type="text" name="rules[{{ $index }}][title]" class="form-control" required value="{{ old("rules.$index.title", $rule->title) }}">
                        </div>
                        
                        <div class="form-group">
                            <label>Rule Description</label>
                            <textarea name="rules[{{ $index }}][description]" class="form-control" rows="2">{{ old("rules.$index.description", $rule->description) }}</textarea>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input type="hidden" name="rules[{{ $index }}][requires_photo]" value="0">
                            <input type="checkbox" name="rules[{{ $index }}][requires_photo]" 
                                class="form-check-input"
                                value="1"
                                {{ old("rules.$index.requires_photo", $rule->requires_photo) ? 'checked' : '' }}>
                            <label class="form-check-label">Require Photo Evidence</label>
                        </div>

                        <div class="form-group">
                            <label>Example Photos</label>
                            
                            <!-- Existing Photos -->
                            @if($rule->photos->count() > 0)
                                <div class="existing-photos mb-3">
                                    <div class="row">
                                        @foreach($rule->photos as $photo)
                                            <div class="col-md-4 col-lg-3 mb-3">
                                                <div class="card photo-card">
                                                    <img src="{{ $photo->photoUrl }}" class="card-img-top" alt="Example photo">
                                                    <div class="card-body p-2">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="rules[{{ $index }}][remove_photos][]" value="{{ $photo->id }}" class="form-check-input">
                                                            <label class="form-check-label">Remove</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Add New Photos -->
                            <div class="photo-container" data-rule-index="{{ $index }}">
                                <div class="mb-3 photo-item">
                                    <div class="input-group">
                                        <input type="file" name="rules[{{ $index }}][photos][]" class="form-control" accept="image/*">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary add-more-photos" data-rule-index="{{ $index }}">+ Add Another Photo</button>
                                        </div>
                                    </div>
                                    <div class="preview-area"></div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-danger btn-sm mt-2 remove-rule">Remove Rule</button>
                    </div>
                </div>
            @endforeach
        </div>
        
        <button type="button" id="add-rule" class="btn btn-secondary mb-3">+ Add Rule</button>
        
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Update Template</button>
            <a href="{{ route('head.qa-templates') }}" class="btn btn-secondary ml-2">Cancel</a>
        </div>
    </form>
</div>

<script>
    let ruleCount = {{ count($template->rules) }};
    
    document.getElementById('add-rule').addEventListener('click', () => {
        const container = document.getElementById('rules-container');
        const newRule = document.createElement('div');
        newRule.className = 'rule-item card mb-3';
        newRule.innerHTML = `
            <div class="card-body">
                <div class="form-group">
                    <label>Rule Title</label>
                    <input type="text" name="rules[${ruleCount}][title]" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Rule Description</label>
                    <textarea name="rules[${ruleCount}][description]" class="form-control" rows="2"></textarea>
                    <div class="form-group">
                    <label>Rule Description</label>
                    <textarea name="rules[${ruleCount}][description]" class="form-control" rows="2"></textarea>
                </div>
                
                <div class="form-check mb-3">
                    <input type="hidden" name="rules[${ruleCount}][requires_photo]" value="0">
                    <input type="checkbox" name="rules[${ruleCount}][requires_photo]" 
                        class="form-check-input" 
                        value="1">
                    <label class="form-check-label">Require Photo Evidence</label>
                </div>

                <div class="form-group">
                    <label>Example Photos (Optional)</label>
                    <div class="photo-container" data-rule-index="${ruleCount}">
                        <div class="mb-3 photo-item">
                            <div class="input-group">
                                <input type="file" name="rules[${ruleCount}][photos][]" class="form-control" accept="image/*">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary add-more-photos" data-rule-index="${ruleCount}">+ Add Another Photo</button>
                                </div>
                            </div>
                            <div class="preview-area"></div>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-danger btn-sm mt-2 remove-rule">Remove Rule</button>
            </div>
        `;
        
        container.appendChild(newRule);
        ruleCount++;
    });
    
    // Use event delegation for all dynamic buttons
    document.addEventListener('click', (e) => {
        // Handle remove rule button
        if (e.target.classList.contains('remove-rule')) {
            // Don't remove if it's the only rule left
            const ruleItems = document.querySelectorAll('.rule-item');
            if (ruleItems.length <= 1) {
                alert('You must have at least one rule!');
                return;
            }
            
            e.target.closest('.rule-item').remove();
        }
        
        // Handle add more photos button
        if (e.target.classList.contains('add-more-photos')) {
            const ruleIndex = e.target.dataset.ruleIndex;
            const photoContainer = document.querySelector(`.photo-container[data-rule-index="${ruleIndex}"]`);
            
            const newPhotoItem = document.createElement('div');
            newPhotoItem.className = 'mb-3 photo-item';
            newPhotoItem.innerHTML = `
                <div class="input-group">
                    <input type="file" name="rules[${ruleIndex}][photos][]" class="form-control" accept="image/*">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-danger remove-photo">Remove</button>
                    </div>
                </div>
                <div class="preview-area"></div>
            `;
            
            photoContainer.appendChild(newPhotoItem);
        }
        
        // Handle remove photo button
        if (e.target.classList.contains('remove-photo')) {
            e.target.closest('.photo-item').remove();
        }
    });
    
    // Add image preview functionality
    document.addEventListener('change', function(e) {
        if (e.target && e.target.type === 'file' && e.target.accept.includes('image')) {
            const fileInput = e.target;
            const previewArea = fileInput.closest('.photo-item').querySelector('.preview-area');
            
            // Clear any existing preview
            previewArea.innerHTML = '';

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewArea.innerHTML = `
                        <img src="${e.target.result}" class="photo-preview mt-2" alt="Image preview">
                    `;
                }
                
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    });
</script>
@endsection