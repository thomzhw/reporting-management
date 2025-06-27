@extends('layouts.header')

@section('content')
<div id="content">

    <!-- Topbar -->
    @include('layouts.topbar')
    <!-- End of Topbar -->

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Manage Permissions for Role: <span class="text-primary">{{ $role->name }}</span></h1>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Assign Permissions</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('roles.permissions', $role) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="font-weight-bold">Select Permissions:</label>
                        <div class="row">
                            @foreach($permissions as $permission)
                            <div class="col-md-6 col-lg-4 mb-2"> {{-- Kolom untuk tata letak yang lebih baik --}}
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           name="permission_ids[]" 
                                           value="{{ $permission->id }}"
                                           id="permission-{{ $permission->id }}"
                                           {{ $role->permissions->contains($permission) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission-{{ $permission->id }}">
                                        {{ $permission->description }} (<code>{{ $permission->slug }}</code>)
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <hr>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Permissions
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-arrow-left"></i> Back to Roles
                    </a>
                </form>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->

</div>

@endsection