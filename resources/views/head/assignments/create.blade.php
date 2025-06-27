@extends('layouts.header')

@section('content')

<!-- Topbar -->
@include('layouts.topbar')
<!-- End of Topbar -->
        
<div class="container">
    <h2>Assign Reporting</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form method="POST" action="{{ route('head.assignments.store') }}">
        @csrf
        
        <div class="card mb-4">
            <div class="card-header">
                Assignment Details
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="outlet_id">Outlet</label>
                            <select id="outlet_id" name="outlet_id" class="form-control" required>
                                <option value="">-- Select Outlet --</option>
                                @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->id }}" {{ (old('outlet_id') == $outlet->id || (isset($selectedOutletId) && $selectedOutletId == $outlet->id)) ? 'selected' : '' }}>
                                        {{ $outlet->name }} ({{ $outlet->city }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="due_date">Due Date (Optional)</label>
                            <input type="date" id="due_date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                            <small class="text-muted">Leave blank if there's no deadline</small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="staff_id">Staff Member</label>
                            <select id="staff_id" name="staff_id" class="form-control" required disabled>
                                <option value="">-- Select Outlet First --</option>
                            </select>
                            <small id="no-staff-message" class="text-danger" style="display: none;">
                                No staff members assigned to this outlet. 
                                <a href="#" id="assign-staff-link">Assign staff members</a>
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="template_id">QA Template</label>
                            <select id="template_id" name="template_id" class="form-control" required disabled>
                                <option value="">-- Select Outlet First --</option>
                            </select>
                            <small id="no-template-message" class="text-danger" style="display: none;">
                                No templates available for this outlet. 
                                <a href="{{ route('head.qa-templates.create') }}">Create a template</a>
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="notes">Notes for Staff (Optional)</label>
                    <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Any additional instructions for the staff member...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
        
        <div class="text-right">
            <a href="{{ route('head.assignments.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary" id="submit-button" disabled>Assign Reporting</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const outletSelect = document.getElementById('outlet_id');
        const staffSelect = document.getElementById('staff_id');
        const templateSelect = document.getElementById('template_id');
        const submitButton = document.getElementById('submit-button');
        const noStaffMessage = document.getElementById('no-staff-message');
        const noTemplateMessage = document.getElementById('no-template-message');
        const assignStaffLink = document.getElementById('assign-staff-link');
        
        // Pre-load data if outlet is already selected
        if (outletSelect.value) {
            loadOutletData(outletSelect.value);
        }
        
        // When outlet is changed, load staff and templates
        outletSelect.addEventListener('change', function() {
            const outletId = this.value;
            
            if (outletId) {
                loadOutletData(outletId);
                // Update assign staff link
                assignStaffLink.href = `/head/outlets/${outletId}/assign-staff`;
            } else {
                // Reset and disable dependent dropdowns
                resetDependentSelects();
            }
        });
        
        function loadOutletData(outletId) {
            // Show loading state
            staffSelect.innerHTML = '<option value="">Loading staff members...</option>';
            templateSelect.innerHTML = '<option value="">Loading templates...</option>';
            staffSelect.disabled = false;
            templateSelect.disabled = false;
            
            // Get CSRF token from meta tag or form
            let token = '';
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                token = metaToken.getAttribute('content');
            } else {
                const csrfInput = document.querySelector('input[name="_token"]');
                if (csrfInput) {
                    token = csrfInput.value;
                }
            }
            
            if (!token) {
                console.error('CSRF token not found');
                alert('Security token not found. Please refresh the page.');
                return;
            }
            
            // Make AJAX request
            fetch("{{ route('head.get-outlet-data') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    outlet_id: outletId
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data); // Debug log
                
                // Populate staff dropdown
                if (data.staff && data.staff.length > 0) {
                    staffSelect.innerHTML = '<option value="">-- Select Staff Member --</option>';
                    data.staff.forEach(staff => {
                        const selectedStaffId = {{ $selectedStaffId ?? 'null' }};
                        const selected = staff.id == selectedStaffId ? 'selected' : '';
                        const role = staff.pivot && staff.pivot.role ? staff.pivot.role : 'Staff';
                        staffSelect.innerHTML += `<option value="${staff.id}" ${selected}>${staff.name} (${role})</option>`;
                    });
                    noStaffMessage.style.display = 'none';
                    staffSelect.disabled = false;
                } else {
                    staffSelect.innerHTML = '<option value="">No staff available</option>';
                    staffSelect.disabled = true;
                    noStaffMessage.style.display = 'block';
                }
                
                // Populate template dropdown
                if (data.templates && data.templates.length > 0) {
                    templateSelect.innerHTML = '<option value="">-- Select Template --</option>';
                    data.templates.forEach(template => {
                        const selectedTemplateId = {{ $selectedTemplateId ?? 'null' }};
                        const selected = template.id == selectedTemplateId ? 'selected' : '';
                        const categoryText = template.category ? ` (${template.category})` : '';
                        templateSelect.innerHTML += `<option value="${template.id}" ${selected}>${template.name}${categoryText}</option>`;
                    });
                    noTemplateMessage.style.display = 'none';
                    templateSelect.disabled = false;
                } else {
                    templateSelect.innerHTML = '<option value="">No templates available</option>';
                    templateSelect.disabled = true;
                    noTemplateMessage.style.display = 'block';
                }
                
                // Check if we can enable the submit button
                checkSubmitButton();
            })
            .catch(error => {
                console.error('Error:', error);
                resetDependentSelects();
                alert('Failed to load outlet data. Please try again. Error: ' + error.message);
            });
        }
        
        function resetDependentSelects() {
            // Reset and disable staff dropdown
            staffSelect.innerHTML = '<option value="">-- Select Outlet First --</option>';
            staffSelect.disabled = true;
            noStaffMessage.style.display = 'none';
            
            // Reset and disable template dropdown
            templateSelect.innerHTML = '<option value="">-- Select Outlet First --</option>';
            templateSelect.disabled = true;
            noTemplateMessage.style.display = 'none';
            
            // Disable submit button
            submitButton.disabled = true;
        }
        
        // Enable submit button only when all required fields are selected
        function checkSubmitButton() {
            if (outletSelect.value && staffSelect.value && templateSelect.value && 
                !staffSelect.disabled && !templateSelect.disabled) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        }
        
        // Listen for changes on all select elements to check submit button
        [outletSelect, staffSelect, templateSelect].forEach(select => {
            select.addEventListener('change', checkSubmitButton);
        });
    });
</script>
@endsection