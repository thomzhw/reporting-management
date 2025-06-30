@extends('layouts.header')

@section('content')
<!-- Topbar -->
@include('layouts.topbar')
<!-- End of Topbar -->
        
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Complete Report</h2>
        <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- Assignment Information Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Assignment Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Report:</strong> {{ $assignment->template->name }}</p>
                    <p><strong>Remote:</strong> {{ $assignment->outlet->name }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Assigned Date:</strong> {{ $assignment->created_at->format('M d, Y') }}</p>
                    <p>
                        <strong>Due Date:</strong> 
                        @if($assignment->due_date)
                            {{ $assignment->due_date->format('M d, Y') }}
                            @if($assignment->due_date->isPast())
                                <span class="text-danger">(Overdue)</span>
                            @endif
                        @else
                            <span class="text-muted">No deadline</span>
                        @endif
                    </p>
                </div>
            </div>
            
            @if($assignment->notes)
                <div class="mt-3">
                    <h6>Notes from Timhub:</h6>
                    <p class="p-2 bg-light rounded">{{ $assignment->notes }}</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- QA Report Form -->
    <form method="POST" action="{{ route('staff.qa-reports.store', $assignment) }}" enctype="multipart/form-data">
        @csrf
        
        <!-- Rules & Responses -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Report Checklist</h5>
            </div>
            <div class="card-body">
                @foreach($assignment->template->rules as $index => $rule)
                <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">{{ $index + 1 }}. {{ $rule->title }}</h6>
                        </div>
                        <div class="card-body">
                            @if($rule->description)
                                <p class="mb-3">{{ $rule->description }}</p>
                            @endif
                            
                            <!-- Example Photos -->
                            @if($rule->photos->count() > 0)
                                <div class="mb-3">
                                    <h6>Example Photos:</h6>
                                    <div class="row">
                                        @foreach($rule->photos as $photo)
                                            <div class="col-md-4 mb-2">
                                                <a href="{{ $photo->photoUrl }}" target="_blank" class="example-photo-link">
                                                    <img src="{{ $photo->photoUrl }}" class="img-thumbnail" style="max-height: 150px;">
                                                </a>
                                                @if($photo->caption)
                                                    <p class="small text-muted">{{ $photo->caption }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary toggle-examples">
                                            <i class="fas fa-images"></i> Show/Hide Example Photos
                                        </button>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="form-group">
                                <label><strong>Your Response:</strong></label>
                                <textarea name="responses[{{ $rule->id }}][response]" class="form-control" rows="3" required></textarea>
                            </div>
                            
                            @if($rule->requires_photo)
                                <div class="form-group">
                                    <label><strong>Photo Evidence:</strong> <span class="text-danger">*</span></label>
                                    <div class="photo-container" data-rule-index="{{ $rule->id }}">
                                        <div class="mb-3 photo-item">
                                            <div class="input-group">
                                                <input type="file" name="responses[{{ $rule->id }}][photos][]" class="form-control" accept="image/*" required>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary add-more-photos" data-rule-index="{{ $rule->id }}">+ Add Another Photo</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Please upload at least one photo as evidence for this item. (Required)</small>
                                </div>
                            @else
                                <div class="form-group">
                                        <label><strong>Photo Evidence:</strong> <small>(Optional)</small></label>
                                        <div class="photo-container" data-rule-index="{{ $rule->id }}">
                                            <div class="mb-3 photo-item">
                                                <div class="input-group">
                                                    <input type="file" name="responses[{{ $rule->id }}][photos][]" class="form-control" accept="image/*">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-secondary add-more-photos" data-rule-index="{{ $rule->id }}">+ Add Another Photo</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted">You may upload photos as additional evidence if needed.</small>
                                    </div>
                            @endif
                            
                            <!-- Display previous responses if this is a rejected report resubmission -->
                            @if($assignment->report && $assignment->report->status == 'rejected')
                                @php
                                    $prevResponse = $assignment->report->responses->where('rule_id', $rule->id)->first();
                                @endphp
                                
                                @if($prevResponse)
                                    <div class="alert alert-secondary mt-3">
                                        <h6><i class="fas fa-history mr-1"></i> Your Previous Response:</h6>
                                        <p class="mb-1">{{ $prevResponse->response }}</p>
                                        
                                        @if($prevResponse->photos->count() > 0)
                                            <div class="mt-2">
                                                <h6>Previous Photos:</h6>
                                                <div class="row">
                                                    @foreach($prevResponse->photos as $photo)
                                                        <div class="col-md-3 mb-2">
                                                            <img src="{{ $photo->photoUrl }}" class="img-thumbnail" style="max-height: 100px;">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @elseif($prevResponse->photo_path)
                                            <div class="mt-2">
                                                <h6>Previous Photo:</h6>
                                                <img src="{{ asset('storage/'.$prevResponse->photo_path) }}" class="img-thumbnail" style="max-height: 100px;">
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i> 
                    Please ensure all fields are filled out correctly. Items marked with <span class="text-danger">*</span> require photo evidence.
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane mr-1"></i> 
                        @if($assignment->report && $assignment->report->status == 'rejected')
                            Resubmit Report
                        @else
                            Submit Report
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Add lightbox functionality for example photos
    document.querySelectorAll('.example-photo-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Create a modal/lightbox effect
            const modal = document.createElement('div');
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100%';
            modal.style.height = '100%';
            modal.style.backgroundColor = 'rgba(0,0,0,0.8)';
            modal.style.display = 'flex';
            modal.style.justifyContent = 'center';
            modal.style.alignItems = 'center';
            modal.style.zIndex = '1000';
            
            const img = document.createElement('img');
            img.src = this.getAttribute('href');
            img.style.maxHeight = '90%';
            img.style.maxWidth = '90%';
            img.style.objectFit = 'contain';
            
            modal.appendChild(img);
            document.body.appendChild(modal);
            
            modal.addEventListener('click', function() {
                document.body.removeChild(modal);
            });
        });
    });
    
    // Add toggle functionality for example photos
    document.querySelectorAll('.toggle-examples').forEach(button => {
        button.addEventListener('click', function() {
            const photoContainer = this.closest('.mb-3').querySelector('.row');
            if (photoContainer.style.display === 'none') {
                photoContainer.style.display = 'flex';
                this.innerHTML = '<i class="fas fa-images"></i> Hide Example Photos';
            } else {
                photoContainer.style.display = 'none';
                this.innerHTML = '<i class="fas fa-images"></i> Show Example Photos';
            }
        });
    });

    // Add this to your existing script section
    document.addEventListener('click', (e) => {
        // Handle add more photos button
        if (e.target.classList.contains('add-more-photos')) {
            const ruleIndex = e.target.dataset.ruleIndex;
            const photoContainer = document.querySelector(`.photo-container[data-rule-index="${ruleIndex}"]`);
            
            const newPhotoItem = document.createElement('div');
            newPhotoItem.className = 'mb-3 photo-item';
            newPhotoItem.innerHTML = `
                <div class="input-group">
                    <input type="file" name="responses[${ruleIndex}][photos][]" class="form-control" accept="image/*">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-danger remove-photo">Remove</button>
                    </div>
                </div>
            `;
            
            photoContainer.appendChild(newPhotoItem);
        }
        
        // Handle remove photo button
        if (e.target.classList.contains('remove-photo')) {
            e.target.closest('.photo-item').remove();
        }
    });
</script>
@endsection