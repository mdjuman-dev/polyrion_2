<section class="sidebar position-relative">
    <div class="multinav">
        <div class="multinav-scroll" style="height: 100%;">
            <!-- sidebar menu-->
            <ul class="sidebar-menu" data-widget="tree">
                <li>
                    <a href="{{ route('admin.backend.dashboard') }}">
                        <i data-feather="monitor"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="">
                    <a href="{{ route('admin.events.index') }}">
                        <i data-feather="calendar"></i>
                        <span>Events</span>
                    </a>
                </li>

                <li class="">
                    <a href="{{ route('admin.withdrawal.index') }}">
                        <i data-feather="dollar-sign"></i>
                        <span>Withdrawals</span>
                    </a>
                </li>

                <li class="">
                    <a href="{{ route('admin.setting') }}">
                        <i data-feather="settings"></i>
                        <span>Settings</span>
                    </a>
                </li>

                <li class="treeview">
                    <a href="index.html#">
                        <i data-feather="shield"></i>
                        <span>Roles & Permissions</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-right pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('admin.roles.index') }}">
                                <i data-feather="shield"></i>
                                <span>Roles</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.permissions.index') }}">
                                <i data-feather="key"></i>
                                <span>Permissions</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="treeview">
                    <a href="index.html#">
                        <i data-feather="inbox"></i>
                        <span>Forms & Tables</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-right pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="treeview">
                            <a href="index.html#">
                                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Forms
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="forms_advanced.html"><i class="icon-Commit"><span
                                                class="path1"></span><span class="path2"></span></i>Form Elements</a>
                                </li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="index.html#">
                                <i class="icon-Commit"><span class="path1"></span><span
                                        class="path2"></span></i>Tables
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="tables_simple.html"><i class="icon-Commit"><span
                                                class="path1"></span><span class="path2"></span></i>Simple tables</a>
                                </li>
                                <li><a href="tables_data.html"><i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i>Data tables</a></li>
                                <li><a href="tables_editable.html"><i class="icon-Commit"><span
                                                class="path1"></span><span class="path2"></span></i>Editable
                                        Tables</a></li>
                                <li><a href="tables_color.html"><i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i>Table Color</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</section>
