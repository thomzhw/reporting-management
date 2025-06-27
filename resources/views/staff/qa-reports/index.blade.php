@extends('layouts.header')

@section('content')
<!-- Topbar -->
@include('layouts.topbar')
<!-- End of Topbar -->
        
<div class="container">
    <h2 class="mb-4">My QA Reports</h2>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    <!-- Tabs for filtering -->
    <ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab" aria-controls="all" aria-selected="true">
                All Reports
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pending-tab" data-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="false">
                To Do
                @php
                    $pendingCount = $assignments->filter(function($a) { 
                        return !$a->report;
                    })->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="badge badge-warning">{{ $pendingCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="review-tab" data-toggle="tab" href="#review" role="tab" aria-controls="review" aria-selected="false">
                Pending Review
                @php
                    $reviewCount = $assignments->filter(function($a) { 
                        return $a->report && $a->report->status == 'pending_review';
                    })->count();
                @endphp
                @if($reviewCount > 0)
                    <span class="badge badge-info">{{ $reviewCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="approved-tab" data-toggle="tab" href="#approved" role="tab" aria-controls="approved" aria-selected="false">
                Approved
                @php
                    $approvedCount = $assignments->filter(function($a) { 
                        return $a->report && $a->report->status == 'approved';
                    })->count();
                @endphp
                @if($approvedCount > 0)
                    <span class="badge badge-success">{{ $approvedCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="rejected-tab" data-toggle="tab" href="#rejected" role="tab" aria-controls="rejected" aria-selected="false">
                Rejected
                @php
                    $rejectedCount = $assignments->filter(function($a) { 
                        return $a->report && $a->report->status == 'rejected';
                    })->count();
                @endphp
                @if($rejectedCount > 0)
                    <span class="badge badge-danger">{{ $rejectedCount }}</span>
                @endif
            </a>
        </li>
    </ul>
    
    <div class="tab-content" id="reportTabsContent">
        <!-- All Reports Tab -->
        <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
            @if($assignments->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i> You don't have any QA assignments yet.
                </div>
            @else
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="allAssignmentsTable">
                                <thead>
                                    <tr>
                                        <th>Template</th>
                                        <th>Outlet</th>
                                        <th>Assigned Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $assignment)
                                        <tr>
                                            <td>{{ $assignment->template->name }}</td>
                                            <td>{{ $assignment->outlet->name }}</td>
                                            <td>{{ $assignment->created_at->format('M d, Y') }}</td>
                                            <td>
                                                @if(!$assignment->report)
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif($assignment->report->status == 'pending_review')
                                                    <span class="badge badge-info">Pending Review</span>
                                                @elseif($assignment->report->status == 'approved')
                                                    <span class="badge badge-success">Approved</span>
                                                @elseif($assignment->report->status == 'rejected')
                                                    <span class="badge badge-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$assignment->report)
                                                    <a href="{{ route('staff.qa-reports.create', $assignment) }}" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-clipboard"></i> Complete
                                                    </a>
                                                @elseif($assignment->report->status == 'rejected')
                                                    <a href="{{ route('staff.qa-reports.show', $assignment->report) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('staff.qa-reports.create', $assignment) }}" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-redo"></i> Redo
                                                    </a>
                                                @else
                                                    <a href="{{ route('staff.qa-reports.show', $assignment->report) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $assignments->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- To Do Tab -->
        <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
            @php
                $pendingAssignments = $assignments->filter(function($a) { 
                    return !$a->report;
                });
            @endphp
            
            @if($pendingAssignments->isEmpty())
                <div class="alert alert-success">
                    <i class="fas fa-check-circle mr-1"></i> You don't have any pending QA tasks.
                </div>
            @else
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="pendingAssignmentsTable">
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
                                                    <i class="fas fa-clipboard"></i> Complete
                                                </a>
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
        
        <!-- Pending Review Tab -->
        <div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">
            @php
                $reviewAssignments = $assignments->filter(function($a) { 
                    return $a->report && $a->report->status == 'pending_review';
                });
            @endphp
            
            @if($reviewAssignments->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i> You don't have any reports pending review.
                </div>
            @else
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="reviewAssignmentsTable">
                                <thead>
                                    <tr>
                                        <th>Template</th>
                                        <th>Outlet</th>
                                        <th>Submitted Date</th>
                                        <th>Waiting For</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reviewAssignments as $assignment)
                                        <tr>
                                            <td>{{ $assignment->template->name }}</td>
                                            <td>{{ $assignment->outlet->name }}</td>
                                            <td>{{ $assignment->report->completed_at->format('M d, Y') }}</td>
                                            <td>
                                                {{ $assignment->report->completed_at->diffForHumans(['parts' => 2]) }}
                                            </td>
                                            <td>
                                                <a href="{{ route('staff.qa-reports.show', $assignment->report) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
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
        
        <!-- Approved Tab -->
        <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
            @php
                $approvedAssignments = $assignments->filter(function($a) { 
                    return $a->report && $a->report->status == 'approved';
                });
            @endphp
            
            @if($approvedAssignments->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i> You don't have any approved reports yet.
                </div>
            @else
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="approvedAssignmentsTable">
                                <thead>
                                    <tr>
                                        <th>Template</th>
                                        <th>Outlet</th>
                                        <th>Submitted On</th>
                                        <th>Approved On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvedAssignments as $assignment)
                                        <tr>
                                            <td>{{ $assignment->template->name }}</td>
                                            <td>{{ $assignment->outlet->name }}</td>
                                            <td>{{ $assignment->report->completed_at->format('M d, Y') }}</td>
                                            <td>{{ $assignment->report->reviewed_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('staff.qa-reports.show', $assignment->report) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
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
        
        <!-- Rejected Tab -->
        <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
            @php
                $rejectedAssignments = $assignments->filter(function($a) { 
                    return $a->report && $a->report->status == 'rejected';
                });
            @endphp
            
            @if($rejectedAssignments->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i> You don't have any rejected reports.
                </div>
            @else
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="rejectedAssignmentsTable">
                                <thead>
                                    <tr>
                                        <th>Template</th>
                                        <th>Outlet</th>
                                        <th>Submitted On</th>
                                        <th>Rejected On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rejectedAssignments as $assignment)
                                        <tr>
                                            <td>{{ $assignment->template->name }}</td>
                                            <td>{{ $assignment->outlet->name }}</td>
                                            <td>{{ $assignment->report->completed_at->format('M d, Y') }}</td>
                                            <td>{{ $assignment->report->reviewed_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('staff.qa-reports.show', $assignment->report) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('staff.qa-reports.create', $assignment) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-redo"></i> Submit New
                                                </a>
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
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#allAssignmentsTable').DataTable({
            "pageLength": 10,
            "order": [[2, "desc"]], // Sort by assigned date, newest first
            "responsive": true
        });
        
        $('#pendingAssignmentsTable').DataTable({
            "pageLength": 10,
            "order": [[3, "asc"]], // Sort by due date, soonest first
            "responsive": true
        });
        
        $('#reviewAssignmentsTable').DataTable({
            "pageLength": 10,
            "order": [[2, "desc"]], // Sort by submitted date, newest first
            "responsive": true
        });
        
        $('#approvedAssignmentsTable').DataTable({
            "pageLength": 10,
            "order": [[3, "desc"]], // Sort by approved date, newest first
            "responsive": true
        });
        
        $('#rejectedAssignmentsTable').DataTable({
            "pageLength": 10,
            "order": [[3, "desc"]], // Sort by rejected date, newest first
            "responsive": true
        });
        
        // Keep the active tab after page reload
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('lastReportTab', $(e.target).attr('href'));
        });
        
        // Go to the last active tab, if it exists
        var lastTab = localStorage.getItem('lastReportTab');
        if (lastTab) {
            $(`#reportTabs a[href="${lastTab}"]`).tab('show');
        }
    });
</script>
@endsection
@endsection