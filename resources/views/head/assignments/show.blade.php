@extends('layouts.header')

@section('content')
<!-- Topbar -->
@include('layouts.topbar')
<!-- End of Topbar -->
        
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Assignment Details</h2>
        <a href="{{ route('head.assignments.export-pdf', $assignment) }}" class="btn btn-primary mr-2">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('head.assignments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Assignments
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <!-- Assignment Information Card -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Assignment Information</h5>
            <span class="badge 
                @if($assignment->status == 'pending') badge-warning
                @elseif($assignment->status == 'in_progress') badge-info
                @elseif($assignment->status == 'completed') badge-success
                @elseif($assignment->status == 'overdue') badge-danger
                @endif">
                {{ ucfirst($assignment->status) }}
            </span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Report:</strong> {{ $assignment->template->name }}</p>
                    <p><strong>Staff Member:</strong> {{ $assignment->staff->name }}</p>
                    <p><strong>Remote:</strong> {{ $assignment->outlet->name }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Assigned Date:</strong> {{ $assignment->created_at->format('M d, Y') }}</p>
                    <p>
                        <strong>Due Date:</strong> 
                        @if($assignment->due_date)
                            {{ $assignment->due_date->format('M d, Y') }}
                            @if($assignment->due_date->isPast() && $assignment->status != 'completed')
                                <span class="text-danger">(Overdue)</span>
                            @endif
                        @else
                            <span class="text-muted">No deadline</span>
                        @endif
                    </p>
                    <p><strong>Reference:</strong> {{ $assignment->assignment_reference ?: 'N/A' }}</p>
                </div>
            </div>
            
            @if($assignment->notes)
                <div class="mt-3">
                    <h6>Notes:</h6>
                    <p class="p-2 bg-light rounded">{{ $assignment->notes }}</p>
                </div>
            @endif
            
            @if($assignment->report)
                <div class="alert alert-success mt-3">
                    <i class="fas fa-check-circle"></i> Report completed on {{ $assignment->report->completed_at->format('M d, Y') }}
                </div>
            @endif
            
            @if(!$assignment->report && $assignment->status == 'pending')
                <div class="text-right mt-3">
                    <form action="{{ route('head.assignments.destroy', $assignment) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this assignment?')" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Assignment
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Template Rules Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Report Rules</h5>
        </div>
        <div class="card-body">
            <div class="accordion" id="rulesAccordion">
                @foreach($assignment->template->rules as $index => $rule)
                    <div class="card mb-2">
                        <div class="card-header" id="heading{{ $rule->id }}">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse{{ $rule->id }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $rule->id }}">
                                    <div class="d-flex justify-content-between">
                                        <span>{{ $rule->title }}</span>
                                        @if($rule->requires_photo)
                                            <span class="badge badge-info">Requires Photo</span>
                                        @endif
                                    </div>
                                </button>
                            </h2>
                        </div>

                        <div id="collapse{{ $rule->id }}" class="collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $rule->id }}" data-parent="#rulesAccordion">
                            <div class="card-body">
                                @if($rule->description)
                                    <p>{{ $rule->description }}</p>
                                @endif
                                
                                @if($rule->photo_example_path)
                                    <div class="mt-2">
                                        <h6>Example Photo:</h6>
                                        <img src="{{ asset('storage/'.$rule->photo_example_path) }}" class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Report Responses (If report exists) -->
    @if($assignment->report)
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Completed Report</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 40%">Rule</th>
                                <th style="width: 40%">Response</th>
                                <th style="width: 20%">Evidence</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignment->report->responses as $response)
                                <tr>
                                    <td>
                                        <strong>{{ $response->rule->title }}</strong>
                                        @if($response->rule->description)
                                            <p class="small text-muted mb-0">{{ $response->rule->description }}</p>
                                        @endif
                                    </td>
                                    <td>{{ $response->response }}</td>
                                    <td>
                                        @if($response->photos->count() > 0)
                                            <div class="d-flex flex-wrap">
                                                @foreach($response->photos as $photo)
                                                    <a href="{{ asset('storage/'.$photo->photo_path) }}" target="_blank" class="mr-2 mb-2">
                                                        <img src="{{ asset('storage/'.$photo->photo_path) }}" class="img-thumbnail" style="max-height: 100px;">
                                                    </a>
                                                @endforeach
                                            </div>
                                        @elseif($response->photo_path)
                                            <a href="{{ asset('storage/'.$response->photo_path) }}" target="_blank">
                                                <img src="{{ asset('storage/'.$response->photo_path) }}" class="img-thumbnail" style="max-height: 100px;">
                                            </a>
                                        @else
                                            <span class="text-muted">No photo</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection