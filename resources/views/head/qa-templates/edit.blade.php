@extends('layouts.header')

@section('content')

<!-- Topbar -->
@include('layouts.topbar')
<!-- End of Topbar -->
    
<div class="container">
    <h2>Edit QA Template</h2>
    
    <form method="POST" action="{{ route('head.qa-templates.update', $template) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Template Name</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $template->name) }}">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="outlet_id">Outlet</label>
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
                        <div class="form-group">
                            <label>Rule Title</label>
                            <input type="text" name="rules[{{ $index }}][title]" class="form-control" required value="{{ old("rules.$index.title", $rule->title) }}">
                            <input type="hidden" name="rules[{{ $index }}][id]" value="{{ $rule->id }}">
                        </div>
                        
                        <div class="form-group">
                            <label>Rule Description</label>
                            <textarea name="rules[{{ $index }}][description]" class="form-control" rows="2">{{ old("rules.$index.description", $rule->description) }}</textarea>
                        </div>
                        
                        <div class="form-check">
                            <!-- Input hidden untuk nilai default false -->
                            <input type="hidden" name="rules[{{ $index }}][requires_photo]" value="0">
                            
                            <!-- Checkbox utama -->
                            <input type="checkbox" name="rules[{{ $index }}][requires_photo]" 
                                class="form-check-input"
                                value="1"
                                {{ old("rules.$index.requires_photo", $rule->requires_photo) ? 'checked' : '' }}>
                                
                            <label class="form-check-label">Require Photo Evidence</label>
                        </div>

                        <div class="form-group mt-2">
                            <label>Photo Example</label>
                            
                            @if($rule->photo_example_path)
                                <div class="mb-2">
                                    <img src="{{ $rule->photo_example_url }}" class="img-thumbnail" style="max-height: 150px;">
                                    
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remove_photo_{{ $index }}" name="rules[{{ $index }}][remove_photo]" value="1">
                                        <label class="form-check-label" for="remove_photo_{{ $index }}">Remove current photo</label>
                                    </div>
                                </div>
                            @endif
                            
                            <input type="file" name="rules[{{ $index }}][photo_example]" class="form-control-file">
                            <small class="text-muted">Leave empty to keep current image</small>
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
                </div>
                
                <div class="form-check">
                    <input type="hidden" name="rules[${ruleCount}][requires_photo]" value="0">
                    <input type="checkbox" name="rules[${ruleCount}][requires_photo]" 
                        class="form-check-input" 
                        value="1">
                    <label class="form-check-label">Require Photo Evidence</label>
                </div>

                <div class="form-group mt-2">
                    <label>Photo Example (Optional)</label>
                    <input type="file" name="rules[${ruleCount}][photo_example]" class="form-control-file">
                </div>
                
                <button type="button" class="btn btn-danger btn-sm mt-2 remove-rule">Remove Rule</button>
            </div>
        `;
        
        container.appendChild(newRule);
        ruleCount++;
    });
    
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-rule')) {
            if (document.querySelectorAll('.rule-item').length > 1) {
                e.target.closest('.rule-item').remove();
            } else {
                alert('You must have at least one rule in the template.');
            }
        }
    });
</script>
@endsection