@extends('layouts.header')

@section('content')
<!-- Topbar -->
@include('layouts.topbar')
<!-- End of Topbar -->
        
<div class="container">
    <h2>QA Reports Management</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <!-- Tabs for filtering different report statuses -->
    <ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
    <li class="nav-item">
            <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending" role="tab"
               aria-controls="pending" aria-selected="true">
                <i class="fas fa-clock mr-1"></i> Pending Review
                <span class="badge badge-warning ml-1">{{ $pendingReports->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="approved-tab" data-toggle="tab" href="#approved" role="tab"
               aria-controls="approved" aria-selected="false">
                <i class="fas fa-check-circle mr-1"></i> Approved
                <span class="badge badge-success ml-1">{{ $approvedReports->total() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="rejected-tab" data-toggle="tab" href="#rejected" role="tab"
               aria-controls="rejected" aria-selected="false">
                <i class="fas fa-times-circle mr-1"></i> Rejected
                <span class="badge badge-danger ml-1">{{ $rejectedReports->total() }}</span>
            </a>
        </li>
    </ul>
    
    <div class="tab-content" id="reportsTabContent">
        <!-- Pending Review Tab -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Reports Pending Your Review</h6>
                </div>
                <div class="card-body">
                    @if($pendingReports->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i> There are no reports pending your review at this time.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="pendingReportsTable">
                                <thead>
                                    <tr>
                                        <th>Staff</th>
                                        <th>Report</th>
                                        <th>Remote</th>
                                        <th>Submitted</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingReports as $report)
                                        <tr>
                                            <td>{{ $report->staff->name }}</td>
                                            <td>{{ $report->template->name }}</td>
                                            <td>{{ $report->assignment->outlet->name }}</td>
                                            <td>{{ $report->completed_at->format('M d, Y') }}</td>
                                            <td>
                                                @if($report->assignment->due_date)
                                                    {{ $report->assignment->due_date->format('M d, Y') }}
                                                    @if($report->assignment->due_date->isPast() && $report->completed_at > $report->assignment->due_date)
                                                        <span class="badge badge-danger">Late</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No deadline</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('head.reports.show', $report) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-clipboard-check mr-1"></i> Review
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $pendingReports->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Approved Tab -->
        <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Approved Reports</h6>
                </div>
                <div class="card-body">
                    @if($approvedReports->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i> You haven't approved any reports yet.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="approvedReportsTable">
                                <thead>
                                    <tr>
                                        <th>Staff</th>
                                        <th>Report</th>
                                        <th>Remote</th>
                                        <th>Submitted</th>
                                        <th>Reviewed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvedReports as $report)
                                        <tr>
                                            <td>{{ $report->staff->name }}</td>
                                            <td>{{ $report->template->name }}</td>
                                            <td>{{ $report->assignment->outlet->name }}</td>
                                            <td>{{ $report->completed_at->format('M d, Y') }}</td>
                                            <td>{{ $report->reviewed_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('head.reports.show', $report) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye mr-1"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $approvedReports->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Rejected Tab -->
        <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Rejected Reports</h6>
                </div>
                <div class="card-body">
                    @if($rejectedReports->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i> You haven't rejected any reports yet.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="rejectedReportsTable">
                                <thead>
                                    <tr>
                                        <th>Staff</th>
                                        <th>Report</th>
                                        <th>Remote</th>
                                        <th>Submitted</th>
                                        <th>Reviewed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rejectedReports as $report)
                                        <tr>
                                            <td>{{ $report->staff->name }}</td>
                                            <td>{{ $report->template->name }}</td>
                                            <td>{{ $report->assignment->outlet->name }}</td>
                                            <td>{{ $report->completed_at->format('M d, Y') }}</td>
                                            <td>{{ $report->reviewed_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('head.reports.show', $report) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye mr-1"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $rejectedReports->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#pendingReportsTable').DataTable({
            "pageLength": 10,
            "order": [[3, "desc"]] // Sort by submission date, newest first
        });
        
        $('#approvedReportsTable').DataTable({
            "pageLength": 10,
            "order": [[4, "desc"]] // Sort by review date, newest first
        });
        
        $('#rejectedReportsTable').DataTable({
            "pageLength": 10,
            "order": [[4, "desc"]] // Sort by review date, newest first
        });
        
        // Keep the active tab after page reload
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('lastReportReviewTab', $(e.target).attr('href'));
        });
        
        // Go to the last active tab, if it exists
        var lastTab = localStorage.getItem('lastReportReviewTab');
        if (lastTab) {
            $(`#reportTabs a[href="${lastTab}"]`).tab('show');
        }
    });
</script>
@endsection
@endsection