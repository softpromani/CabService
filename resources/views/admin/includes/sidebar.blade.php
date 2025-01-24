<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="index.html">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>
        @if(auth()->user()->hasAnyRole(['admin', 'Super Admin']))
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#components-nav-user" data-bs-toggle="collapse" href="#">
                <i class="bi bi-person"></i>
                <span>User</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav-user" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('admin.userList') }}">
                        <i class="bi bi-circle"></i><span>Admin</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.driver.index') }}">
                        <i class="bi bi-circle"></i><span>Driver</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.customer.index') }}">
                        <i class="bi bi-circle"></i><span>Customer</span>
                    </a>
                </li>


            </ul>
        </li>
         @endif
        @if(auth()->user()->hasAnyRole(['admin', 'Super Admin']))
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

        @if(auth()->user()->hasAnyRole(['admin', 'Super Admin']))
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#components1-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-menu-button-wide"></i><span>Master Setup</span><i
                    class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components1-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('admin.master.country') }}">
                        <i class="bi bi-circle"></i><span>Country</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.master.state') }}">
                        <i class="bi bi-circle"></i><span>State</span>
                  </a>
                </li>
                <li>
                    <a href="{{ route('admin.master.city') }}">
                        <i class="bi bi-circle"></i><span>City</span>
                   </a>
                </li>

                <li>
                    <a href="{{ route('admin.master.brand') }}">
                        <i class="bi bi-circle"></i><span>Brand</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.master.model') }}">
                        <i class="bi bi-circle"></i><span>Model</span>
                  </a>
                </li>


            </ul>
        </li>
        @endif

        @if(auth()->user()->hasAnyRole(['admin', 'Super Admin']))
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#components2-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-menu-button-wide"></i><span>Business Setup</span><i
                    class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components2-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li class="menu-item">
                    <a href="{{ route('admin.setting.business-setting.index') }}" class="menu-link">
                        <div data-i18n="List">Business Settings</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.setting.business-pages.index',['page' => 'about_us']) }}" class="menu-link">
                        <div data-i18n="List">Business Pages</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.setting.socialmedia.index') }}" class="menu-link">
                        <div data-i18n="List">Social Media Links</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.setting.thirdPartyApi','mail_config') }}" class="menu-link">
                        <div data-i18n="List">Third Party API</div>
                    </a>
                </li>

            </ul>
        </li>
        @endif


    </ul>

</aside>
