@extends('layouts.header')

@section('content')
<!-- Topbar -->
@include('layouts.topbar')
<!-- End of Topbar -->
        
<div class="container">
    <h1>Staff Dashboard</h1>
    
    <div class="row">
        <!-- Pending QA Tasks Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending QA Tasks
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pendingAssignments->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Completed QA Reports -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed Reports
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $completedReports->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Assigned Outlets Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Assigned Outlets
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $assignedOutlets->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Overdue Tasks Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Overdue Tasks
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $overdueAssignments->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pending Assignments -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pending QA Tasks</h6>
        </div>
        <div class="card-body">
            @if($pendingAssignments->isEmpty())
                <div class="text-center py-4">
                    <p class="text-muted mb-0">No pending QA tasks at the moment.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Template</th>
                                <th>Outlet</th>
                                <th>Assigned Date</th>
                                <th>Due Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingAssignments as $assignment)
                                <tr>
                                    <td>{{ $assignment->template->name }}</td>
                                    <td>{{ $assignment->outlet->name }}</td>
                                    <td>{{ $assignment->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($assignment->due_date)
                                            {{ $assignment->due_date->format('M d, Y') }}
                                            @if($assignment->due_date->isPast())
                                                <span class="badge badge-danger">Overdue</span>
                                            @endif
                                        @else
                                            <span class="text-muted">No deadline</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('staff.qa-reports.create', $assignment) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-clipboard"></i> Complete Report
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Recent Completed Reports -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Recent Completed Reports</h6>
            <a href="{{ route('staff.qa-reports.index') }}" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body">
            @if($completedReports->isEmpty())
                <div class="text-center py-4">
                    <p class="text-muted mb-0">You haven't completed any QA reports yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Template</th>
                                <th>Outlet</th>
                                <th>Completed Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedReports->take(5) as $report)
                                <tr>
                                    <td>{{ $report->template->name }}</td>
                                    <td>{{ $report->assignment->outlet->name }}</td>
                                    <td>{{ $report->completed_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('staff.qa-reports.show', $report) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View Report
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Assigned Outlets -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">My Assigned Outlets</h6>
        </div>
        <div class="card-body">
            @if($assignedOutlets->isEmpty())
                <div class="text-center py-4">
                    <p class="text-muted mb-0">You are not assigned to any outlets yet.</p>
                </div>
            @else
                <div class="row">
                    @foreach($assignedOutlets as $outlet)
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <h5 class="font-weight-bold text-gray-800">{{ $outlet->name }}</h5>
                                            <p class="mb-1"><i class="fas fa-map-marker-alt text-gray-500 mr-1"></i> {{ $outlet->city }}</p>
                                            <p class="mb-1"><i class="fas fa-tag text-gray-500 mr-1"></i> {{ $outlet->type }}</p>
                                            <p class="mb-0"><i class="fas fa-user-tag text-gray-500 mr-1"></i> {{ $outlet->pivot->role }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection