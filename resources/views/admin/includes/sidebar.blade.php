<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link " href="{{  route('admin.dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @if(auth()->user()->hasRole(['Admin', 'Super Admin']))
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('admin.userList') }}">
                <i class="bi bi-person"></i>
                <span>User</span>
            </a>
        </li>

        @endif
        @if(auth()->user()->hasRole(['Admin', 'Super Admin']))
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-menu-button-wide"></i><span>Role / Permision</span><i
                    class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('admin.role-create') }}">
                        <i class="bi bi-circle"></i><span>Role</span>
                    </a>
                </li>


            </ul>
        </li>
        @endif



    </ul>

</aside>
