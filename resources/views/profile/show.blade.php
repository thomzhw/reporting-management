<!-- resources/views/profile/show.blade.php -->
@extends('layouts.header')

@section('content')
<!-- Topbar -->
@include('layouts.topbar')
<!-- End of Topbar -->

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit fa-sm"></i> Edit Profile
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 font-weight-bold">Name:</div>
                        <div class="col-md-8">{{ $user->name }}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4 font-weight-bold">Email:</div>
                        <div class="col-md-8">{{ $user->email }}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4 font-weight-bold">Role:</div>
                        <div class="col-md-8">{{ $user->role->name ?? 'No role assigned' }}</div>
                    </div>
                    @if($user->head)
                        <hr>
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Reports to:</div>
                            <div class="col-md-8">{{ $user->head->name }}</div>
                        </div>
                    @endif
                    <hr>
                    <div class="row">
                        <div class="col-md-4 font-weight-bold">Account Created:</div>
                        <div class="col-md-8">{{ $user->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Security</h6>
                    <a href="{{ route('profile.change-password') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-key fa-sm"></i> Change Password
                    </a>
                </div>
                <div class="card-body">
                    <p>Your password was last changed: <strong>Not available</strong></p>
                    <p class="mb-0">It's recommended to change your password regularly to maintain account security.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection