@extends('layouts.header')

@section('content')
<div id="content">
    @include('layouts.topbar')

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Outlet Details</h1>
            <div>
                <a href="{{ route('outlets.index') }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-arrow-left"></i> Back to Outlets
                </a>
                <a href="{{ route('outlets.edit', $outlet) }}" class="btn btn-warning mr-2">
                    <i class="fas fa-edit"></i> Edit Outlet
                </a>
                <a href="{{ route('outlets.assign-heads', $outlet) }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Assign Heads
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
                        <h6 class="m-0 font-weight-bold text-primary">Outlet Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row no-gutters">
                            <div class="col mr-2">
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
                                
                                <div class="mb-3">
                                    <h6 class="font-weight-bold">Created:</h6>
                                    <p>{{ $outlet->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Assigned Heads -->
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Assigned Heads</h6>
                    </div>
                    <div class="card-body">
                        @if($outlet->heads->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Assigned Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($outlet->heads as $head)
                                            <tr>
                                                <td>{{ $head->name }}</td>
                                                <td>{{ $head->email }}</td>
                                                <td>{{ $head->pivot->created_at->format('M d, Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">No heads assigned to this outlet yet.</p>
                                <a href="{{ route('outlets.assign-heads', $outlet) }}" class="btn btn-primary btn-sm mt-3">
                                    <i class="fas fa-user-plus"></i> Assign Heads
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Staff Members -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Assigned Staff Members</h6>
            </div>
            <div class="card-body">
                @if($outlet->staffs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Assigned By</th>
                                    <th>Assigned Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($outlet->staffs as $staff)
                                    <tr>
                                        <td>{{ $staff->name }}</td>
                                        <td>{{ $staff->email }}</td>
                                        <td>{{ $staff->pivot->role }}</td>
                                        <td>{{ App\Models\User::find($staff->pivot->assigned_by)->name }}</td>
                                        <td>{{ $staff->pivot->created_at->format('M d, Y') }}</td>
                                    </tr>                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted">No staff members assigned to this outlet yet.</p>
                        <p class="small text-muted">Staff are assigned by the heads responsible for this outlet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- QA Templates -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">QA Templates</h6>
            </div>
            <div class="card-body">
                @if($outlet->qaTemplates->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Created By</th>
                                    <th>Category</th>
                                    <th>Created Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($outlet->qaTemplates as $template)
                                    <tr>
                                        <td>{{ $template->name }}</td>
                                        <td>{{ $template->head->name ?? 'N/A' }}</td>
                                        <td>{{ $template->category ?? 'General' }}</td>
                                        <td>{{ $template->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted">No QA templates created for this outlet yet.</p>
                        <p class="small text-muted">Templates are created by the heads responsible for this outlet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection