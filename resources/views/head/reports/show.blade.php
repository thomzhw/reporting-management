@extends('layouts.header')

@section('content')
<!-- Topbar -->
@include('layouts.topbar')
<!-- End of Topbar -->
        
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Review QA Report</h2>
        <a href="{{ route('head.reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <!-- Report Information Card -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5 class="mb-0">Report Information</h5>
            <span class="badge 
                @if($report->status == 'pending_review') badge-warning
                @elseif($report->status == 'approved') badge-success
                @elseif($report->status == 'rejected') badge-danger
                @endif">
                {{ ucfirst(str_replace('_', ' ', $report->status)) }}
            </span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Report:</strong> {{ $report->template->name }}</p>
                    <p><strong>Staff Member:</strong> {{ $report->staff->name }}</p>
                    <p><strong>Remote:</strong> {{ $report->assignment->outlet->name }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Submitted On:</strong> {{ $report->completed_at->format('M d, Y, h:i A') }}</p>
                    <p>
                        <strong>Due Date:</strong> 
                        @if($report->assignment->due_date)
                            {{ $report->assignment->due_date->format('M d, Y') }}
                            @if($report->assignment->due_date->isPast() && $report->completed_at > $report->assignment->due_date)
                                <span class="text-danger">(Submitted Late)</span>
                            @endif
                        @else
                            <span class="text-muted">No deadline set</span>
                        @endif
                    </p>
                </div>
            </div>
            
            @if($report->status != 'pending_review')
                <div class="alert {{ $report->status == 'approved' ? 'alert-success' : 'alert-danger' }} mt-3">
                    <p><strong>Reviewed On:</strong> {{ $report->reviewed_at->format('M d, Y, h:i A') }}</p>
                    @if($report->feedback)
                        <p><strong>Feedback:</strong> {{ $report->feedback }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
    
    <!-- Report Responses -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0">Report Responses</h5>
        </div>
        <div class="card-body">
        @foreach($report->responses as $response)
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">{{ $loop->iteration }}. {{ $response->rule->title }}</h6>
                </div>
                <div class="card-body">
                    <p><strong>Staff Response:</strong> {{ $response->response }}</p>
                    
                    @if($response->photos->count() > 0)
                        <div>
                            <h6>Photo Evidence:</h6>
                            <div class="row">
                                @foreach($response->photos as $photo)
                                    <div class="col-md-4 col-lg-3 mb-3">
                                        <a href="{{ $photo->photoUrl }}" target="_blank" class="d-block">
                                            <img src="{{ $photo->photoUrl }}" class="img-thumbnail" alt="Photo Evidence">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif($response->photo_path)
                        <!-- For backward compatibility with old single photo path -->
                        <div>
                            <h6>Photo Evidence:</h6>
                            <div class="row">
                                <div class="col-md-4 col-lg-3">
                                    <a href="{{ asset('storage/' . $response->photo_path) }}" target="_blank" class="d-block">
                                        <img src="{{ asset('storage/' . $response->photo_path) }}" class="img-thumbnail" alt="Photo Evidence">
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Head's evaluation fields would go here -->
                </div>
            </div>
        @endforeach
        </div>
    </div>
    
    <!-- Review Form (only show if report is pending review) -->
    @if($report->status == 'pending_review')
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Review Report</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('head.reports.review', $report) }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="feedback"><strong>Feedback to Staff (Optional):</strong></label>
                        <textarea name="feedback" id="feedback" rows="4" class="form-control"
                                  placeholder="Provide any feedback, comments, or notes for the staff member..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label><strong>Review Decision:</strong></label>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i> Please review all responses carefully before making your decision.
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        <button type="submit" name="status" value="rejected" class="btn btn-danger btn-lg mr-3">
                            <i class="fas fa-times-circle mr-1"></i> Reject Report
                        </button>
                        
                        <button type="submit" name="status" value="approved" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle mr-1"></i> Approve Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection