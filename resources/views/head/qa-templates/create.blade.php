@extends('layouts.header')

@section('content')

<!-- Topbar -->
@include('layouts.topbar')
<!-- End of Topbar -->
    
<div class="container">
    <h2>Create Report</h2>
    
    <form method="POST" action="{{ route('head.qa-templates.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Template Name</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="outlet_id">Outlet</label>
                    <select name="outlet_id" class="form-control" required>
                        <option value="">-- Select Outlet --</option>
                        @foreach($outlets as $outletItem)
                            <option value="{{ $outletItem->id }}" {{ (old('outlet_id') == $outletItem->id || (isset($outlet) && $outlet->id == $outletItem->id)) ? 'selected' : '' }}>
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
                    <input type="text" name="category" class="form-control" value="{{ old('category') }}" placeholder="e.g., Cleanliness, Safety">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>
        
        <h4>Rules</h4>
        <div id="rules-container">
            <div class="rule-item card mb-3">
                <div class="card-body">
                    <div class="form-group">
                        <label>Rule Title</label>
                        <input type="text" name="rules[0][title]" class="form-control" required value="{{ old('rules.0.title') }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Rule Description</label>
                        <textarea name="rules[0][description]" class="form-control" rows="2">{{ old('rules.0.description') }}</textarea>
                    </div>
                    
                    <div class="form-check">
                        <!-- Input hidden untuk nilai default false -->
                        <input type="hidden" name="rules[0][requires_photo]" value="0">
                        
                        <!-- Checkbox utama -->
                        <input type="checkbox" name="rules[0][requires_photo]" 
                            class="form-check-input"
                            value="1"
                            {{ old('rules.0.requires_photo', false) ? 'checked' : '' }}>
                            
                        <label class="form-check-label">Require Photo Evidence</label>
                    </div>

                    <div class="form-group mt-2">
                        <label>Photo Example (Optional)</label>
                        <input type="file" name="rules[0][photo_example]" class="form-control-file">
                    </div>
                    
                    <button type="button" class="btn btn-danger btn-sm mt-2 remove-rule">Remove Rule</button>
                </div>
            </div>
        </div>
        
        <button type="button" id="add-rule" class="btn btn-secondary mb-3">+ Add Rule</button>
        
        <button type="submit" class="btn btn-primary">Create Template</button>
    </form>
</div>

<script>
    let ruleCount = 1;
    
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
            e.target.closest('.rule-item').remove();
        }
    });
</script>
@endsection