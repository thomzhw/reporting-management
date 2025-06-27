@extends('layouts.header')

@section('content')

<!-- Topbar -->
@include('layouts.topbar')
<!-- End of Topbar -->

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Reporting Assignments</h1>
        <a href="{{ route('head.assignments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Assign New Template
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($assignments->isEmpty())
        <div class="alert alert-info">
            You haven't assigned any templates yet. Click "Assign New Template" to get started.
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Template</th>
                            <th>Staff Member</th>
                            <th>Assigned Date</th>
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
                                <td>{{ $assignment->created_at->format('d M Y') }}</td>
                                <td>
                                    @if($assignment->due_date)
                                        {{ $assignment->due_date->format('d M Y') }}
                                    @else
                                        <span class="text-muted">No deadline</span>
                                    @endif
                                </td>
                                <td>
                                    @if($assignment->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($assignment->status == 'in_progress')
                                        <span class="badge bg-info">In Progress</span>
                                    @elseif($assignment->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($assignment->status == 'overdue')
                                        <span class="badge bg-danger">Overdue</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('head.assignments.show', $assignment) }}" 
                                           class="btn btn-sm btn-info">
                                            Details
                                        </a>
                                        
                                        @if($assignment->status == 'pending' && !$assignment->report)
                                            <form action="{{ route('head.assignments.destroy', $assignment) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-3">
            {{ $assignments->links() }}
        </div>
    @endif
</div>
@endsection