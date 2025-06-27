@extends('layouts.header')

@section('content')
<div id="content">

    @include('layouts.topbar')

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Superuser Dashboard</h1>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
            </a>
        </div>

        <!-- Content Row - Overview Cards -->
        <div class="row">
            <!-- Total Outlets Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Outlets</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['outlets_count'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-store fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Users Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Users</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['users_count'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total QA Reports Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    QA Reports</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['reports_count'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Roles Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Active Roles</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['roles_count'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-tag fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row - User Distribution & Recent Activity -->
        <div class="row">
            <!-- User Distribution by Role -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">User Distribution by Role</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('users.manage') }}">View All Users</a>
                                <a class="dropdown-item" href="{{ route('roles.index') }}">Manage Roles</a>
                            </div>
                        </div>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="userRolesChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            <span class="mr-2">
                                <i class="fas fa-circle text-danger"></i> Superusers ({{ $roleData['superuserCount'] }})
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-primary"></i> Heads ({{ $roleData['headCount'] }})
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-success"></i> Staff ({{ $roleData['staffCount'] }})
                            </span>
                            @if($roleData['otherCount'] > 0)
                            <span class="mr-2">
                                <i class="fas fa-circle text-secondary"></i> Other ({{ $roleData['otherCount'] }})
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Card -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" id="activityDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="activityDropdown">
                                <a class="dropdown-item" href="#">View All Activity</a>
                                <a class="dropdown-item" href="#">Export Activity Log</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="activity-feed">
                            @foreach($activity['recentReports'] as $report)
                                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                    <div class="flex-shrink-0 activity-icon bg-info text-white rounded-circle p-2 mr-3">
                                        <i class="fas fa-clipboard"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">Report Submitted</div>
                                        <div>{{ $report->staff->name }} completed "{{ $report->template->name }}"</div>
                                        <small class="text-muted">{{ $report->completed_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach

                            @foreach($activity['recentUsers'] as $user)
                                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                    <div class="flex-shrink-0 activity-icon bg-primary text-white rounded-circle p-2 mr-3">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">New User</div>
                                        <div>{{ $user->name }} joined as {{ $user->role ? $user->role->name : 'user' }}</div>
                                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach

                            @if($activity['recentReports']->isEmpty() && $activity['recentUsers']->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    No recent activity to display.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row - Outlet & QA Status -->
        <div class="row">
                        <!-- Outlet Status -->
                        <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Outlet Status</h6>
                    </div>
                    <div class="card-body">
                        @php
                            // Calculate percentages
                            $outletsWithBothPercentage = $outletsData['totalOutlets'] > 0 ? 
                                round(($outletsData['outletsWithBothHeadStaff'] / $outletsData['totalOutlets']) * 100) : 0;
                                
                            $outletsWithHeadOnlyPercentage = $outletsData['totalOutlets'] > 0 ? 
                                round(($outletsData['outletsWithHeadOnly'] / $outletsData['totalOutlets']) * 100) : 0;
                                
                            $outletsWithStaffOnlyPercentage = $outletsData['totalOutlets'] > 0 ? 
                                round(($outletsData['outletsWithStaffOnly'] / $outletsData['totalOutlets']) * 100) : 0;
                                
                            $outletsWithNeitherPercentage = $outletsData['totalOutlets'] > 0 ? 
                                round(($outletsData['outletsWithNeither'] / $outletsData['totalOutlets']) * 100) : 0;
                        @endphp
                        
                        <!-- Outlets with Both Head and Staff -->
                        <h4 class="small font-weight-bold">Outlets with Head & Staff 
                            <span class="float-right">{{ $outletsWithBothPercentage }}%</span>
                        </h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $outletsWithBothPercentage }}%" 
                                aria-valuenow="{{ $outletsWithBothPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <!-- Outlets with Head only -->
                        <h4 class="small font-weight-bold">Outlets with Head only 
                            <span class="float-right">{{ $outletsWithHeadOnlyPercentage }}%</span>
                        </h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $outletsWithHeadOnlyPercentage }}%" 
                                aria-valuenow="{{ $outletsWithHeadOnlyPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <!-- Outlets with Staff only -->
                        <h4 class="small font-weight-bold">Outlets with Staff only 
                            <span class="float-right">{{ $outletsWithStaffOnlyPercentage }}%</span>
                        </h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $outletsWithStaffOnlyPercentage }}%" 
                                aria-valuenow="{{ $outletsWithStaffOnlyPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <!-- Outlets with neither -->
                        <h4 class="small font-weight-bold">Unassigned Outlets 
                            <span class="float-right">{{ $outletsWithNeitherPercentage }}%</span>
                        </h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $outletsWithNeitherPercentage }}%" 
                                aria-valuenow="{{ $outletsWithNeitherPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <p class="mb-0">{{ $outletsData['totalOutlets'] }} outlets total</p>
                            <a href="{{ route('outlets.index') }}" class="btn btn-sm btn-primary">Manage Outlets</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QA Reports Status -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">QA Reports Status</h6>
                    </div>
                    <div class="card-body">
                        @php
                            // Calculate percentages
                            $pendingReviewPercentage = $reportsData['totalReports'] > 0 ? 
                                round(($reportsData['pendingReviewReports'] / $reportsData['totalReports']) * 100) : 0;
                                
                            $approvedPercentage = $reportsData['totalReports'] > 0 ? 
                                round(($reportsData['approvedReports'] / $reportsData['totalReports']) * 100) : 0;
                                
                            $rejectedPercentage = $reportsData['totalReports'] > 0 ? 
                                round(($reportsData['rejectedReports'] / $reportsData['totalReports']) * 100) : 0;
                        @endphp
                        
                        <!-- Pending Review Reports -->
                        <h4 class="small font-weight-bold">Pending Review 
                            <span class="float-right">{{ $reportsData['pendingReviewReports'] }} ({{ $pendingReviewPercentage }}%)</span>
                        </h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pendingReviewPercentage }}%" 
                                aria-valuenow="{{ $pendingReviewPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <!-- Approved Reports -->
                        <h4 class="small font-weight-bold">Approved 
                            <span class="float-right">{{ $reportsData['approvedReports'] }} ({{ $approvedPercentage }}%)</span>
                        </h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $approvedPercentage }}%" 
                                aria-valuenow="{{ $approvedPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <!-- Rejected Reports -->
                        <h4 class="small font-weight-bold">Rejected 
                            <span class="float-right">{{ $reportsData['rejectedReports'] }} ({{ $rejectedPercentage }}%)</span>
                        </h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $rejectedPercentage }}%" 
                                aria-valuenow="{{ $rejectedPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <p class="mb-0">Total reports: {{ $reportsData['totalReports'] }}</p>
                            <a href="#" class="btn btn-sm btn-primary">View All Reports</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                <!-- Content Row - Latest Users and Outlets -->
                <div class="row">
            <!-- Latest Users -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Latest Users</h6>
                        <a href="{{ route('users.manage') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tableData['latestUsers'] as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->role)
                                                <span class="badge 
                                                    {{ $user->role->name == 'superuser' ? 'badge-danger' : 
                                                       ($user->role->name == 'head' ? 'badge-primary' : 
                                                       ($user->role->name == 'staff' ? 'badge-success' : 'badge-secondary')) }}">
                                                    {{ $user->role->name }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">No role</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                    
                                    @if($tableData['latestUsers']->isEmpty())
                                    <tr>
                                        <td colspan="4" class="text-center">No users found.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest Outlets -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Latest Outlets</h6>
                        <a href="{{ route('outlets.index') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>City</th>
                                        <th>Type</th>
                                        <th>Head Count</th>
                                        <th>Staff Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tableData['latestOutlets'] as $outlet)
                                    <tr>
                                        <td>{{ $outlet->name }}</td>
                                        <td>{{ $outlet->city }}</td>
                                        <td>
                                            <span class="badge 
                                                {{ $outlet->type == 'store' ? 'badge-primary' : 
                                                   ($outlet->type == 'restaurant' ? 'badge-success' : 
                                                   ($outlet->type == 'warehouse' ? 'badge-warning' : 'badge-info')) }}">
                                                {{ ucfirst($outlet->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $outlet->heads_count }}</td>
                                        <td>{{ $outlet->staffs_count }}</td>
                                    </tr>
                                    @endforeach
                                    
                                    @if($tableData['latestOutlets']->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center">No outlets found.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->

</div>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// User Roles Chart
var ctx = document.getElementById("userRolesChart");
var userRolesChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ["Superusers", "Heads", "Staff", "Other"],
        datasets: [{
            data: [
                {{ $roleData['superuserCount'] }}, 
                {{ $roleData['headCount'] }}, 
                {{ $roleData['staffCount'] }}, 
                {{ $roleData['otherCount'] }}
            ],
            backgroundColor: ['#e74a3b', '#4e73df', '#1cc88a', '#858796'],
            hoverBackgroundColor: ['#be3c2e', '#2e59d9', '#17a673', '#6e7075'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
        },
        legend: {
            display: false
        },
        cutoutPercentage: 80,
    },
});

// Add hover styling to activity items
document.querySelectorAll('.activity-feed > div').forEach(item => {
    item.addEventListener('mouseenter', function() {
        this.classList.add('bg-light');
    });
    item.addEventListener('mouseleave', function() {
        this.classList.remove('bg-light');
    });
});
</script>

<style>
.activity-feed > div {
    transition: background-color 0.2s ease;
    border-radius: 0.25rem;
    padding: 0.5rem;
}
.activity-feed > div:hover {
    cursor: pointer;
}
.activity-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection