@extends('layouts.header')

@section('content')
<div id="content">
    @include('layouts.topbar')

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Manage Staff for {{ $outlet->name }}</h1>
            <a href="{{ route('head.outlets.show', $outlet) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Remote
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Assign Staff Members</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('head.outlets.update-staff', $outlet) }}">
                    @csrf
                    
                    @if($availableStaff->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No staff members available. Please ask the superuser to create users with the 'staff' role.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered" id="staffTable">
                                <thead>
                                    <tr>
                                        <th>Staff Name</th>
                                        <th>Email</th>
                                        <th>Assign</th>
                                        <th>Role at Remote</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availableStaff as $staff)
                                        @php 
                                            $isAssigned = $assignedStaff->contains('id', $staff->id);
                                            $staffRole = $isAssigned 
                                                ? $assignedStaff->where('id', $staff->id)->first()->pivot->role 
                                                : '';
                                        @endphp
                                        <tr>
                                            <td>{{ $staff->name }}</td>
                                            <td>{{ $staff->email }}</td>
                                            <td>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" 
                                                        class="custom-control-input staff-toggle" 
                                                        id="staff{{ $staff->id }}" 
                                                        name="staff_assignments[{{ $staff->id }}][assign]"
                                                        value="1"
                                                        data-id="{{ $staff->id }}"
                                                        {{ $isAssigned ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="staff{{ $staff->id }}">
                                                        {{ $isAssigned ? 'Assigned' : 'Not Assigned' }}
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="role-input-container" style="{{ $isAssigned ? '' : 'display: none;' }}">
                                                    <input type="text" 
                                                        class="form-control role-input" 
                                                        name="staff_assignments[{{ $staff->id }}][role]" 
                                                        placeholder="e.g. Cashier, Manager, Cleaner"
                                                        value="{{ $staffRole }}"
                                                        {{ $isAssigned ? '' : 'disabled' }}>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-right mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Staff Assignments
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#staffTable').DataTable({
        "pageLength": 25,
        "order": [[0, "asc"]]
    });
    
    // Handle staff toggle
    $('.staff-toggle').change(function() {
        const staffId = $(this).data('id');
        const isChecked = $(this).prop('checked');
        const container = $(this).closest('tr').find('.role-input-container');
        const roleInput = container.find('.role-input');
        const label = $(this).next('label');
        
        if (isChecked) {
            // Show role input and enable it
            container.show();
            roleInput.prop('disabled', false);
            label.text('Assigned');
        } else {
            // Hide role input and disable it
            container.hide();
            roleInput.prop('disabled', true);
            roleInput.val(''); // Clear the role value
            label.text('Not Assigned');
        }
    });

    // Handle form submission
    $('form').submit(function(e) {
        // Enable all role inputs before submission so they get sent
        $('.role-input').prop('disabled', false);
        
        return true; // Allow form submission
    });
});
</script>
@endsection