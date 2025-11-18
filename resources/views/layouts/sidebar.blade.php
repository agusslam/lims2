<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-flask"></i>
        </div>
        <div class="sidebar-brand-text mx-3">LIMS</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">Sample Management</div>

    <!-- Module 1: List New Sample -->
    @if(Auth::user()->hasPermission(1))
    <li class="nav-item {{ request()->routeIs('sample-requests.*', 'samples.index', 'samples.show') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('sample-requests.index') }}">
            <i class="fas fa-fw fa-list-alt"></i>
            <span>List New Sample</span>
        </a>
    </li>
    @endif

    <!-- Module 2: Codification -->
    @if(Auth::user()->hasPermission(2))
    <li class="nav-item {{ request()->routeIs('codification.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('samples.codification.index') }}">
            <i class="fas fa-fw fa-tags"></i>
            <span>Codification</span>
        </a>
    </li>
    @endif

    <!-- Module 3: Assignment -->
    @if(Auth::user()->hasPermission(3))
    <li class="nav-item {{ request()->routeIs('assignments.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('assignments.index') }}">
            <i class="fas fa-fw fa-tasks"></i>
            <span>Assignment</span>
        </a>
    </li>
    @endif

    <!-- Module 4: Testing -->
    @if(Auth::user()->hasPermission(4))
    <li class="nav-item {{ request()->routeIs('testing.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('testing.index') }}">
            <i class="fas fa-fw fa-microscope"></i>
            <span>Testing</span>
        </a>
    </li>
    @endif

    <!-- Module 5: Review -->
    @if(Auth::user()->hasPermission(5))
    <li class="nav-item {{ request()->routeIs('reviews.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('reviews.index') }}">
            <i class="fas fa-fw fa-check-double"></i>
            <span>Review</span>
        </a>
    </li>
    @endif

    <!-- Module 6: Certificates -->
    @if(Auth::user()->hasPermission(6))
    <li class="nav-item {{ request()->routeIs('certificates.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('certificates.index') }}">
            <i class="fas fa-fw fa-certificate"></i>
            <span>Certificates</span>
        </a>
    </li>
    @endif
    
    <!-- Module 7: Invoice -->
    @if(Auth::user()->hasPermission(7))
    <li class="nav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('invoices.index') }}">
            <i class="fas fa-fw fa-file-invoice"></i>
            <span>Invoice</span>
        </a>
    </li>
    @endif

    <!-- Module 8: Parameter Management -->
    @if(Auth::user()->hasPermission(8))
    <li class="nav-item {{ request()->routeIs('parameters.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('parameters.index') }}">
            <i class="fas fa-fw fa-cogs"></i>
            <span>Parameters</span>
        </a>
    </li>
    @endif

    <!-- Module 9: User Management -->
    @if(Auth::user()->hasPermission(9))
    <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('users.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>User Management</span>
        </a>
    </li>
    @endif

    <!-- Module 10: System Settings -->
    @if(Auth::user()->hasPermission(10))
    <li class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('settings.index') }}">
            <i class="fas fa-fw fa-cog"></i>
            <span>System Settings</span>
        </a>
    </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
