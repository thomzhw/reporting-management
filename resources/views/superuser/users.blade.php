@extends('layouts.header')

@section('content')

<!-- Topbar -->
@include('layouts.topbar')
    <!-- End of Topbar -->
    
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary">Manage Users</h5>
                    <a href="{{ route('users.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus mr-1"></i> Add New User
                    </a>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="usersTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th> {{-- Ditambahkan: Kolom status --}}
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <form action="{{ route('users.update.role', $user->id) }}" method="POST" class="d-flex align-items-center">
                                            @csrf
                                            @method('PUT')
                                            
                                            <select name="role_id" class="form-control form-control-sm mr-2">
                                                @foreach($roles as $role)
                                                <option value="{{ $role->id }}" 
                                                    {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-sync-alt"></i> Update
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        {{-- Ditambahkan: Menampilkan status pengguna (Active/Deleted) --}}
                                        @if ($user->deleted_at)
                                            <span class="badge badge-danger">Deleted</span>
                                        @else
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-start align-items-center">
                                            @if ($user->deleted_at)
                                                <form action="{{ route('users.restore', $user->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to restore this user?')">
                                                        <i class="fas fa-undo"></i> Restore
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm mr-1">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to soft-delete this user?')">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
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
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    #usersTable tbody tr td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.85em;
        font-weight: 500;
    }
    /* Menyesuaikan lebar select agar lebih rapi */
    .form-control-sm {
        min-width: 100px; /* Lebar minimum untuk dropdown role */
        max-width: 150px; /* Lebar maksimum jika perlu */
    }
    /* Pastikan tombol aksi tidak terlalu rapat */
    .d-flex .btn, .d-flex form {
        margin-right: 5px; /* Memberi sedikit jarak antar elemen aksi */
    }
    .d-flex .btn:last-child, .d-flex form:last-child {
        margin-right: 0;
    }
</style>
@endsection