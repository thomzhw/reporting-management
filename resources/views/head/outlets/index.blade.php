@extends('layouts.header')

@section('content')
<div id="content">
    @include('layouts.topbar')

    <div class="container-fluid">
        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">My Outlets</h1>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if($outlets->count() > 0)
            <div class="row">
                @foreach($outlets as $outlet)
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            {{ ucfirst($outlet->type) }}
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $outlet->name }}</div>
                                        <div class="mt-2 text-sm text-gray-600">{{ $outlet->city }}</div>
                                        <div class="mt-3">
                                            <span class="badge badge-secondary">{{ $outlet->staffs_count }} Staff</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-store fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col">
                                        <a href="{{ route('head.outlets.show', $outlet) }}" class="btn btn-primary btn-sm btn-block">
                                            <i class="fas fa-eye fa-sm"></i> View Details
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="{{ route('head.outlets.assign-staff', $outlet) }}" class="btn btn-info btn-sm btn-block">
                                            <i class="fas fa-users fa-sm"></i> Manage Staff
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> You don't have any outlets assigned to you yet. Please contact a superuser to assign outlets.
            </div>
        @endif
    </div>
</div>
@endsection