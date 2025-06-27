@extends('layouts.header')

@section('content')
@include('layouts.topbar')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Assign Staff to Heads</h1>
        <a href="{{ route('users.manage') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Staff Name</th>
                        <th>Email</th>
                        <th>Current Head</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staffMembers as $staff)
                        <tr>
                            <td>{{ $staff->name }}</td>
                            <td>{{ $staff->email }}</td>
                            <td>
                                @if($staff->head)
                                    {{ $staff->head->name }}
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#assignModal{{ $staff->id }}">
                                    Assign Head
                                </button>

                                <!-- Modal for each staff -->
                                <div class="modal fade" id="assignModal{{ $staff->id }}" tabindex="-1" aria-labelledby="assignModalLabel{{ $staff->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="assignModalLabel{{ $staff->id }}">
                                                    Assign Head for {{ $staff->name }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('update.staff.assignment') }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <input type="hidden" name="staff_id" value="{{ $staff->id }}">
                                                    
                                                    <div class="mb-3">
                                                        <label for="head_id" class="form-label">Select Head</label>
                                                        <select name="head_id" id="head_id" class="form-control">
                                                            <option value="">-- Remove Head Assignment --</option>
                                                            @foreach($heads as $head)
                                                                <option value="{{ $head->id }}" @if($staff->head_id == $head->id) selected @endif>
                                                                    {{ $head->name }} ({{ $head->email }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save Assignment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection