@extends('layouts.header')

@section('content')
<!-- Topbar -->
@include('layouts.topbar')
<!-- End of Topbar -->
        
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>QA Report Details</h2>
        <a href="{{ route('staff.qa-reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    
    <!-- Report Status Alert -->
    @if($report->status == 'pending_review')
        <div class="alert alert-warning">
            <i class="fas fa-clock mr-1"></i> <strong>Report Status:</strong> This report is pending review by your head.
        </div>
    @elseif($report->status == 'approved')
        <div class="alert alert-success">
            <i class="fas fa-check-circle mr-1"></i> <strong>Report Status:</strong> This report has been approved by your head.
            @if($report->feedback)
                <hr>
                <strong>Feedback:</strong> {{ $report->feedback }}
            @endif
        </div>
    @elseif($report->status == 'rejected')
        <div class="alert alert-danger">
            <i class="fas fa-times-circle mr-1"></i> <strong>Report Status:</strong> This report has been rejected by your head.
            @if($report->feedback)
                <hr>
                <strong>Feedback:</strong> {{ $report->feedback }}
                <hr>
                <p>Please create a new report addressing the feedback.</p>
                <a href="{{ route('staff.qa-reports.create', $report->assignment) }}" class="btn btn-primary">
                    <i class="fas fa-redo mr-1"></i> Create New Report
                </a>
            @endif
        </div>
    @endif
    
    <!-- Report Information Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Report Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Template:</strong> {{ $report->template->name }}</p>
                    <p><strong>Outlet:</strong> {{ $report->assignment->outlet->name }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Completed Date:</strong> {{ $report->completed_at->format('M d, Y, h:i A') }}</p>
                    @if($report->reviewed_at)
                        <p><strong>Reviewed Date:</strong> {{ $report->reviewed_at->format('M d, Y, h:i A') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Report Responses -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">QA Checklist Responses</h5>
        </div>
        <div class="card-body">
            @foreach($report->responses as $index => $response)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">{{ $index + 1 }}. {{ $response->rule->title }}</h6>
                    </div>
                    <div class="card-body">
                        @if($response->rule->description)
                            <div class="alert alert-secondary">
                                {{ $response->rule->description }}
                            </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Your Response:</h6>
                                <p class="p-3 bg-light rounded">{{ $response->response }}</p>
                            </div>
                            
                            <div class="col-md-6">
                                @if($response->photo_path)
                                    <h6 class="font-weight-bold">Evidence Photo:</h6>
                                    <img src="{{ asset('storage/'.$response->photo_path) }}" class="img-fluid img-thumbnail" alt="Evidence photo">
                                    <a href="{{ asset('storage/'.$response->photo_path) }}" target="_blank" class="btn btn-sm btn-info mt-2">
                                        <i class="fas fa-external-link-alt mr-1"></i> View Full Size
                                    </a>
                                @elseif($response->rule->requires_photo)
                                    <div class="alert alert-danger">Missing required photo evidence</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <div class="card bg-light mb-4">
        <div class="card-body text-center">
            <p class="mb-0">
                <i class="fas fa-check-circle text-success mr-1"></i> 
                This report was submitted on {{ $report->completed_at->format('F d, Y') }} at {{ $report->completed_at->format('h:i A') }}.
                @if($report->status == 'approved')
                    <br><span class="text-success">Approved by head on {{ $report->reviewed_at->format('F d, Y') }}.</span>
                @elseif($report->status == 'rejected')
                    <br><span class="text-danger">Rejected by head on {{ $report->reviewed_at->format('F d, Y') }}.</span>
                @endif
            </p>
        </div>
    </div>
</div>
@endsection