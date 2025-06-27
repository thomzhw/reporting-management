<!-- resources/views/staff/qa-templates/index.blade.php -->
@extends('layouts.header')

@section('content')
@include('layouts.topbar')

<div class="container">
    <h2>QA Templates</h2>
    
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Template Name</th>
                        <th>Description</th>
                        <th>Head</th>
                        <th>Rules Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                    <tr>
                        <td>{{ $template->name }}</td>
                        <td>{{ $template->description }}</td>
                        <td>{{ $template->head->name }}</td>
                        <td>{{ $template->rules->count() }}</td>
                        <td>
                            <a href="{{ route('staff.qa-reports.create', $template) }}" class="btn btn-sm btn-primary">
                                Start Report
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection