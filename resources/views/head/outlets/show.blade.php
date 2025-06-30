@extends('layouts.header')

@section('content')
<div id="content">
    @include('layouts.topbar')

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ $outlet->name }}</h1>
            <div>
                <a href="{{ route('head.outlets.index') }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-arrow-left"></i> Back to Remotes
                </a>
                <a href="{{ route('head.outlets.assign-staff', $outlet) }}" class="btn btn-primary">
                    <i class="fas fa-users"></i> Manage Staff
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <!-- Outlet Information -->
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Remote Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h5 class="font-weight-bold text-gray-800">{{ $outlet->name }}</h5>
                            <span class="badge badge-{{ $outlet->status == 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($outlet->status) }}
                            </span>
                            <span class="badge badge-info">{{ ucfirst($outlet->type) }}</span>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="font-weight-bold">Address:</h6>
                            <p>{{ $outlet->address }}<br>{{ $outlet->city }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="font-weight-bold">Contact:</h6>
                            <p>
                                @if($outlet->phone)
                                    <i class="fas fa-phone-alt mr-1"></i> {{ $outlet->phone }}<br>
                                @endif
                                @if($outlet->email)
                                    <i class="fas fa-envelope mr-1"></i> {{ $outlet->email }}
                                @endif
                            </p>
                        </div>
                        
                        @if($outlet->description)
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Description:</h6>
                                <p>{{ $outlet->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('head.qa-templates.create', ['outlet_id' => $outlet->id]) }}" class="btn btn-success btn-block py-3">
                                    <i class="fas fa-plus-circle mb-2 d-block fa-2x"></i>
                                    Create Report
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('head.assignments.create', ['outlet_id' => $outlet->id]) }}" class="btn btn-info btn-block py-3">
                                    <i class="fas fa-tasks mb-2 d-block fa-2x"></i>
                                    Assign Report
                                </a>
                            </div>
                            <div class="col-md-12 mb-3">
                                <a href="{{ route('head.outlets.assign-staff', $outlet) }}" class="btn btn-primary btn-block py-3">
                                    <i class="fas fa-user-plus mb-2 d-block fa-2x"></i>
                                    Manage Staff Members
                                </a>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff Members -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Staff Members</h6>
                <a href="{{ route('head.outlets.assign-staff', $outlet) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-plus"></i> Manage Staff
                </a>
            </div>
            <div class="card-body">
                @if($outlet->staffs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" id="staffTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Assigned Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($outlet->staffs as $staff)
                                    <tr>
                                        <td>{{ $staff->name }}</td>
                                        <td>{{ $staff->email }}</td>
                                        <td>{{ $staff->pivot->role }}</td>
                                        <td>{{ \Carbon\Carbon::parse($staff->pivot->created_at)->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('head.assignments.create', ['outlet_id' => $outlet->id, 'staff_id' => $staff->id]) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-tasks"></i> Assign Template
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted">No staff members assigned to this remote yet.</p>
                        <a href="{{ route('head.outlets.assign-staff', $outlet) }}" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Assign Staff
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- QA Templates -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Report</h6>
                <a href="{{ route('head.qa-templates.create', ['outlet_id' => $outlet->id]) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Create Template
                </a>
            </div>
            <div class="card-body">
                @if($outlet->qaTemplates->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" id="templatesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Created Date</th>
                                    <th>Rules</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($outlet->qaTemplates as $template)
                                    <tr>
                                        <td>{{ $template->name }}</td>
                                        <td>{{ $template->category ?? 'General' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($template->created_at)->format('M d, Y') }}</td>
                                        <td>{{ $template->rules->count() }}</td>
                                        <td>
                                            <a href="{{ route('head.qa-templates.edit', $template) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="{{ route('head.assignments.create', ['outlet_id' => $outlet->id, 'template_id' => $template->id]) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-tasks"></i> Assign
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted">No Report created for this outlet yet.</p>
                        <a href="{{ route('head.qa-templates.create', ['outlet_id' => $outlet->id]) }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create Report
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Assignments -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Report Assignments</h6>
            </div>
            <div class="card-body">
                @php
                    // Fetch assignments for this outlet
                    $assignments = \App\Models\QaTemplateAssignment::where('outlet_id', $outlet->id)
                        ->with(['staff', 'template'])
                        ->latest()
                        ->take(10)
                        ->get();
                @endphp

                @if($assignments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" id="assignmentsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Template</th>
                                    <th>Staff</th>
                                    <th>Assigned</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignments as $assignment)
                                    <tr>
                                        <td>{{ $assignment->template->name }}</td>
                                        <td>{{ $assignment->staff->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($assignment->created_at)->format('M d, Y') }}</td>
                                        <td>
                                            @if($assignment->due_date)
                                                {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">No deadline</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assignment->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($assignment->status == 'in_progress')
                                                <span class="badge badge-info">In Progress</span>
                                            @elseif($assignment->status == 'completed')
                                                <span class="badge badge-success">Completed</span>
                                            @elseif($assignment->status == 'overdue')
                                                <span class="badge badge-danger">Overdue</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('head.assignments.show', $assignment) }}" class="btn btn-info btn-sm">
                                            <a href="{{ route('head.assignments.show', $assignment) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('head.assignments.index', ['outlet_id' => $outlet->id]) }}" class="btn btn-link">
                            View all assignments <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted">No template assignments for this outlet yet.</p>
                        <a href="{{ route('head.assignments.create', ['outlet_id' => $outlet->id]) }}" class="btn btn-info">
                            <i class="fas fa-tasks"></i> Assign Template
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#staffTable').DataTable({
            "pageLength": 10,
            "order": []
        });
        $('#templatesTable').DataTable({
            "pageLength": 10,
            "order": []
        });
        $('#assignmentsTable').DataTable({
            "pageLength": 5,
            "order": []
        });
    });
</script>
@endsection