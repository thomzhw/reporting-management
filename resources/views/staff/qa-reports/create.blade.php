@extends('layouts.header')

@section('content')
<!-- Topbar -->
@include('layouts.topbar')
<!-- End of Topbar -->
        
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Complete QA Report</h2>
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
    
    <!-- Display Previous Rejection Feedback if Available -->
    @if($assignment->report && $assignment->report->status == 'rejected')
        <div class="alert alert-warning">
            <h5><i class="fas fa-exclamation-triangle mr-1"></i> This report was previously rejected</h5>
            @if($assignment->report->feedback)
                <hr>
                <p><strong>Feedback from Head:</strong> {{ $assignment->report->feedback }}</p>
                <p class="mb-0">Please address this feedback in your new submission.</p>
            @endif
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
                    <p><strong>Template:</strong> {{ $assignment->template->name }}</p>
                    <p><strong>Outlet:</strong> {{ $assignment->outlet->name }}</p>
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
                    <h6>Notes from Head:</h6>
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
                <h5 class="m-0 font-weight-bold text-primary">Quality Assurance Checklist</h5>
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
                            
                            @if($rule->photo_example_path)
                                <div class="mb-3">
                                    <h6>Example:</h6>
                                    <img src="{{ asset('storage/'.$rule->photo_example_path) }}" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            @endif
                            
                            <div class="form-group">
                                <label><strong>Your Response:</strong></label>
                                <textarea name="responses[{{ $rule->id }}][response]" class="form-control" rows="3" required></textarea>
                            </div>
                            
                            @if($rule->requires_photo)
                                <div class="form-group">
                                    <label><strong>Photo Evidence:</strong> <span class="text-danger">*</span></label>
                                    <input type="file" name="responses[{{ $rule->id }}][photo]" class="form-control-file" accept="image/*" required>
                                    <small class="text-muted">Please upload a photo as evidence for this item. (Required)</small>
                                </div>
                            @else
                                <div class="form-group">
                                    <label><strong>Photo Evidence:</strong> <small>(Optional)</small></label>
                                    <input type="file" name="responses[{{ $rule->id }}][photo]" class="form-control-file" accept="image/*">
                                    <small class="text-muted">You may upload a photo as additional evidence if needed.</small>
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
                                        
                                        @if($prevResponse->photo_path)
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
                            Resubmit QA Report
                        @else
                            Submit QA Report
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
                            