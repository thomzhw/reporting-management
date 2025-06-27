@extends('layouts.header')

@section('content')

<!-- Topbar -->
@include('layouts.topbar')
<!-- End of Topbar -->
        
<div class="container">
    <h2>Reporting</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="mb-3">
        <a href="{{ route('head.qa-templates.create') }}" class="btn btn-primary">
            Create Report Template
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Outlet</th>
                        <th>Category</th>
                        <th>Rules Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                    <tr>
                        <td>{{ $template->name }}</td>
                        <td>
                            @if($template->outlet)
                                {{ $template->outlet->name }}
                            @else
                                <span class="text-muted">No outlet</span>
                            @endif
                        </td>
                        <td>
                            @if($template->category)
                                <span class="badge badge-info">{{ $template->category }}</span>
                            @else
                                <span class="text-muted">General</span>
                            @endif
                        </td>
                        <td>{{ $template->rules->count() }}</td>
                        <td>
                            <a href="{{ route('head.qa-templates.edit', $template) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            
                            <a href="{{ route('head.assignments.create', ['template_id' => $template->id, 'outlet_id' => $template->outlet_id]) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-tasks"></i> Assign
                            </a>
                            
                            <form action="{{ route('head.qa-templates.destroy', $template) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this template?')" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection