<!-- resources/views/layouts/sidebar.blade.php -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">e-Task</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    @php
        $user = auth()->user();
    @endphp

    <!-- SUPERUSER SECTION -->
    @if($user->hasAccess('superuser.access'))
        <!-- Dashboard -->
        <li class="nav-item {{ request()->routeIs('superuser.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('superuser.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            Administration
        </div>

        @if($user->hasAccess('user.manage'))
            <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('users.manage') }}">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>Manage Users</span>
                </a>
            </li>
        @endif

        @if($user->hasAccess('role.manage'))
            <li class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('roles.index') }}">
                    <i class="fas fa-fw fa-user-tag"></i>
                    <span>Manage Roles</span>
                </a>
            </li>
        @endif

        @if($user->hasAccess('permission.manage'))
            <li class="nav-item {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('permissions.index') }}">
                    <i class="fas fa-fw fa-key"></i>
                    <span>Manage Permissions</span>
                </a>
            </li>
        @endif

        @if($user->hasAccess('superuser.outlet.manage'))
            <li class="nav-item {{ request()->routeIs('outlets.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('outlets.index') }}">
                    <i class="fas fa-fw fa-signal"></i>
                    <span>Manage Remotes</span>
                </a>
            </li>
        @endif

        <hr class="sidebar-divider">
    @endif

    <!-- HEAD SECTION -->
    @if($user->hasAccess('head.access'))
        <!-- Dashboard -->
        @if(!$user->hasAccess('superuser.access'))
            <li class="nav-item {{ request()->routeIs('head.dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('head.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">
        @endif

        <div class="sidebar-heading">
            Team Management
        </div>

        @if($user->hasAccess('head.qa.manage'))
            <li class="nav-item {{ request()->routeIs('head.qa-templates*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('head.qa-templates') }}">
                    <i class="fas fa-fw fa-clipboard-list"></i>
                    <span>Reports</span>
                </a>
            </li>
        @endif

        @if($user->hasAccess('head.qa.assign'))
            <!-- Outlets -->
            <li class="nav-item {{ request()->routeIs('head.outlets.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('head.outlets.index') }}">
                    <i class="fas fa-fw fa-building"></i>
                    <span>My Remotes</span>
                </a>
            </li>

            <!-- Assignments -->
            <li class="nav-item {{ request()->routeIs('head.assignments.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('head.assignments.index') }}">
                    <i class="fas fa-fw fa-tasks"></i>
                    <span>Report Assignments</span>
                </a>
            </li>
        @endif

        <!-- Reports -->
        <li class="nav-item {{ request()->routeIs('head.reports.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('head.reports.index') }}">
                <i class="fas fa-fw fa-clipboard-check"></i>
                <span>Review Reports</span>
                @php
                    $pendingReviews = App\Models\QaReport::whereHas('assignment', function($query) use ($user) {
                        $query->whereIn('outlet_id', $user->managedOutlets()->pluck('outlets.id'));
                    })->where('status', 'pending_review')->count();
                @endphp
                @if($pendingReviews > 0)
                    <span class="badge badge-warning ml-1">{{ $pendingReviews }}</span>
                @endif
            </a>
        </li>

        <hr class="sidebar-divider">
    @endif

    <!-- STAFF SECTION -->
    @if($user->hasAccess('staff.access'))
        <!-- Dashboard -->
        @if(!$user->hasAccess('superuser.access') && !$user->hasAccess('head.access'))
            <li class="nav-item {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('staff.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">
        @endif

        <div class="sidebar-heading">
            Staff Activities
        </div>

        <!-- QA Reports -->
        <li class="nav-item {{ request()->routeIs('staff.qa-reports.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('staff.qa-reports.index') }}">
                <i class="fas fa-fw fa-clipboard-check"></i>
                <span>My QA Reports</span>
                @php
                    // Only count rejected reports for actual staff (not superusers/heads viewing staff section)
                    $rejectedReports = 0;
                    if (!$user->hasAccess('superuser.access') && !$user->hasAccess('head.access')) {
                        $rejectedReports = App\Models\QaReport::where('staff_id', $user->id)
                            ->where('status', 'rejected')
                            ->count();
                    }
                @endphp
                @if($rejectedReports > 0)
                    <span class="badge badge-danger ml-1">{{ $rejectedReports }}</span>
                @endif
            </a>
        </li>

        <hr class="sidebar-divider">
    @endif

    <!-- Common features accessible to all -->
    <div class="sidebar-heading">
        Account
    </div>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('profile.show') }}">
            <i class="fas fa-fw fa-user-circle"></i>
            <span>My Profile</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>