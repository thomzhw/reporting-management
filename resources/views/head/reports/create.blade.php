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
                    <p><strong>Report:</strong> {{ $report->template->name }}</p>
                    <p><strong>Remote:</strong> {{ $report->assignment->outlet->name }}</p>
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
    
    <!-- Report Responses (existing code) -->
    <div class="card shadow mb-4">
        <!-- Existing response code stays the same -->
    </div>
</div>
@endsection