@extends('layouts.header')

@section('content')
<div id="content">
    @include('layouts.topbar')

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Assign Heads to {{ $outlet->name }}</h1>
            <a href="{{ route('outlets.show', $outlet) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Outlet
            </a>
        </div>

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
                <h6 class="m-0 font-weight-bold text-primary">Select Heads to Assign</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('outlets.update-heads', $outlet) }}">
                    @csrf
                    
                    <div class="form-group mb-4">
                        <label>Available Heads</label>
                        <div class="row">
                            @if($heads->count() > 0)
                                @foreach($heads as $head)
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" 
                                                class="custom-control-input" 
                                                id="head{{ $head->id }}" 
                                                name="head_ids[]" 
                                                value="{{ $head->id }}" 
                                                {{ $assignedHeads->contains($head->id) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="head{{ $head->id }}">
                                                {{ $head->name }} ({{ $head->email }})
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <p class="text-muted mb-0">No head users available. Please create users with the 'head' role first.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i> Assigned heads will be able to:
                        <ul class="mb-0 mt-1">
                            <li>Create QA templates for this outlet</li>
                            <li>Assign staff members to this outlet</li>
                            <li>Assign QA templates to staff members within this outlet</li>
                            <li>View reports submitted by staff members for this outlet</li>
                        </ul>
                    </div>

                    @if($heads->count() > 0)
                        <button type="submit" class="btn btn-primary">Update Head Assignments</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection