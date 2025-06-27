@extends('layouts.header')

@section('content')
<div id="content">

    <!-- Topbar -->
    @include('layouts.topbar')
    <!-- End of Topbar -->
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Head Dashboard</div>

                    <div class="card-body">
                        <h4>Welcome to Head Dashboard</h4>
                        <p>This is your main dashboard where you can manage your team.</p>
                        
                        <div class="mt-4">
                            <a href="{{ route('head.qa-templates.create') }}" class="btn btn-primary">
                                Create Report
                            </a>
                            <a href="{{ route('head.qa-templates') }}" class="btn btn-info ml-2">
                                All Reports
                            </a>
                            <a href="{{ route('head.assignments.create') }}" class="btn btn-warning ml-2">
                                Report Assignment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection