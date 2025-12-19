<section class="sidebar position-relative">
    <div class="multinav">
        <div class="multinav-scroll" style="height: 100%;">
            <!-- sidebar menu-->
            <ul class="sidebar-menu" data-widget="tree">
                @php
                    $admin = auth()->guard('admin')->user();
                @endphp

                @if($admin && $admin->can('view dashboard'))
                <li class="{{ request()->routeIs('admin.backend.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.backend.dashboard') }}">
                        <i data-feather="monitor"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @endif

                @if($admin && $admin->can('view events'))
                <li class="{{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.events.index') }}">
                        <i data-feather="calendar"></i>
                        <span>Events</span>
                    </a>
                </li>
                @endif

                @if($admin && $admin->can('view markets'))
                <li class="{{ request()->routeIs('admin.market.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.market.index') }}">
                        <i data-feather="trending-up"></i>
                        <span>Markets</span>
                    </a>
                </li>
                @endif

                @if($admin && $admin->can('view users'))
                <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}">
                        <i data-feather="users"></i>
                        <span>Users</span>
                    </a>
                </li>
                @endif

                @if($admin && $admin->can('view deposits'))
                <li class="{{ request()->routeIs('admin.deposits.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.deposits.index') }}">
                        <i data-feather="credit-card"></i>
                        <span>Deposits</span>
                    </a>
                </li>
                @endif

                @if($admin && $admin->can('view withdrawals'))
                <li class="{{ request()->routeIs('admin.withdrawal.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.withdrawal.index') }}">
                        <i data-feather="dollar-sign"></i>
                        <span>Withdrawals</span>
                    </a>
                </li>
                @endif

                @if($admin && $admin->can('manage global settings'))
                <li class="{{ request()->routeIs('admin.setting') ? 'active' : '' }}">
                    <a href="{{ route('admin.setting') }}">
                        <i data-feather="settings"></i>
                        <span>Settings</span>
                    </a>
                </li>
                @endif

                @if($admin && $admin->can('manage payment settings'))
                <li class="{{ request()->routeIs('admin.payment.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.payment.settings') }}">
                        <i data-feather="credit-card"></i>
                        <span>Payment Settings</span>
                    </a>
                </li>
                @endif

                @if($admin && $admin->can('view roles'))
                <li class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.roles.index') }}">
                        <i data-feather="shield"></i>
                        <span>Roles</span>
                    </a>
                </li>
                @endif

                @if($admin && $admin->can('view permissions'))
                <li class="{{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.permissions.index') }}">
                        <i data-feather="key"></i>
                        <span>Permissions</span>
                    </a>
                </li>
                @endif

                @if($admin && $admin->can('manage roles'))
                <li class="{{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.admins.index') }}">
                        <i data-feather="user-check"></i>
                        <span>Admin Users</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</section>
