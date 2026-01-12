<section class="sidebar position-relative">
    <div class="multinav">
        <div class="multinav-scroll" style="height: 100%;">
            <!-- sidebar menu-->
            <ul class="sidebar-menu" data-widget="tree">
                @php
                    $admin = auth()->guard('admin')->user();
                @endphp

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('view dashboard', 'admin')))
                <li class="{{ request()->routeIs('admin.backend.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.backend.dashboard') }}">
                        <iconify-icon icon="mdi:monitor" class="sidebar-icon"></iconify-icon>
                        <span>Dashboard</span>
                    </a>
                </li>
                @endif

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('view events', 'admin')))
                <li class="{{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.events.index') }}">
                        <iconify-icon icon="mdi:calendar" class="sidebar-icon"></iconify-icon>
                        <span>Events</span>
                    </a>
                </li>
                @endif

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('view markets', 'admin')))
                <li class="{{ request()->routeIs('admin.market.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.market.index') }}">
                        <iconify-icon icon="mdi:trending-up" class="sidebar-icon"></iconify-icon>
                        <span>Markets</span>
                    </a>
                </li>
                @endif

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('view users', 'admin')))
                <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}">
                        <iconify-icon icon="mdi:account-multiple" class="sidebar-icon"></iconify-icon>
                        <span>Users</span>
                    </a>
                </li>
                @endif

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('view deposits', 'admin')))
                <li class="{{ request()->routeIs('admin.deposits.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.deposits.index') }}">
                        <iconify-icon icon="mdi:credit-card" class="sidebar-icon"></iconify-icon>
                        <span>Deposits</span>
                    </a>
                </li>
                @endif

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('view withdrawals', 'admin')))
                <li class="{{ request()->routeIs('admin.withdrawal.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.withdrawal.index') }}">
                        <iconify-icon icon="mdi:currency-usd" class="sidebar-icon"></iconify-icon>
                        <span>Withdrawals</span>
                    </a>
                </li>
                @endif

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('view users', 'admin')))
                <li class="{{ request()->routeIs('admin.kyc.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.kyc.index') }}">
                        <iconify-icon icon="mdi:id-card" class="sidebar-icon"></iconify-icon>
                        <span>KYC Verifications</span>
                    </a>
                </li>
                @endif

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('manage global settings', 'admin')))
                    <li class="{{ request()->routeIs('admin.setting') ? 'active' : '' }}">
                        <a href="{{ route('admin.setting') }}">
                            <iconify-icon icon="mdi:cog" class="sidebar-icon"></iconify-icon>
                            <span>Global Settings</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.referral-settings.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.referral-settings.index') }}">
                            <iconify-icon icon="mdi:account-multiple" class="sidebar-icon"></iconify-icon>
                            <span>Referral Settings</span>
                        </a>
                    </li>
                @endif

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('manage pages', 'admin')))
                    <li class="{{ request()->routeIs('admin.pages.privacy-policy.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pages.privacy-policy.edit') }}">
                            <iconify-icon icon="mdi:shield" class="sidebar-icon"></iconify-icon>
                            <span>Privacy Policy</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.pages.terms-of-use.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pages.terms-of-use.edit') }}">
                            <iconify-icon icon="mdi:file-document" class="sidebar-icon"></iconify-icon>
                            <span>Terms of Use</span>
                        </a>
                    </li>
                @endif

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('manage payment settings', 'admin')))
                <li class="{{ request()->routeIs('admin.payment.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.payment.settings') }}">
                        <iconify-icon icon="mdi:credit-card" class="sidebar-icon"></iconify-icon>
                        <span>Payment Settings</span>
                    </a>
                </li>
                @endif

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('view roles', 'admin')))
                <li class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.roles.index') }}">
                        <iconify-icon icon="mdi:shield" class="sidebar-icon"></iconify-icon>
                        <span>Roles</span>
                    </a>
                </li>
                @endif

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('view permissions', 'admin')))
                <li class="{{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.permissions.index') }}">
                        <iconify-icon icon="mdi:key" class="sidebar-icon"></iconify-icon>
                        <span>Permissions</span>
                    </a>
                </li>
                @endif

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('manage roles', 'admin')))
                <li class="{{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.admins.index') }}">
                        <iconify-icon icon="mdi:account-check" class="sidebar-icon"></iconify-icon>
                        <span>Admin Users</span>
                    </a>
                </li>
                @endif

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('manage faqs', 'admin')))
                <li class="{{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.faqs.index') }}">
                        <iconify-icon icon="mdi:help-circle" class="sidebar-icon"></iconify-icon>
                        <span>FAQs</span>
                    </a>
                </li>
                @endif

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('manage contact', 'admin')))
                <li class="{{ request()->routeIs('admin.contact.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.contact.index') }}">
                        <iconify-icon icon="mdi:email" class="sidebar-icon"></iconify-icon>
                        <span>Contact Messages</span>
                    </a>
                </li>
                @endif

                @if($admin && ($admin->isSuperAdmin() || $admin->hasPermissionTo('manage social media', 'admin')))
                <li class="{{ request()->routeIs('admin.social-media.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.social-media.index') }}">
                        <iconify-icon icon="mdi:share-variant" class="sidebar-icon"></iconify-icon>
                        <span>Social Media</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</section>
