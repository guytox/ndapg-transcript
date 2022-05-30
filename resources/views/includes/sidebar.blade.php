        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">

            <div data-simplebar class="h-100">

                <!--- Sidemenu -->
                <div id="sidebar-menu">
                    <!-- Left Menu Start -->
                    <ul class="metismenu list-unstyled" id="side-menu">
                        <li class="menu-title">Menu</li>

                        <li>
                            <a href="#" class="@if (Request::is('dashboard')) active @endif waves-effect">
                                <i class="mdi mdi-view-dashboard"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        @include('includes.role-sidebar.applicant')

                        @include('includes.role-sidebar.student')

                        @include('includes.role-sidebar.admin')

                        <li>
                            <a href="#logout" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();" class=" waves-effect">
                                <i class="mdi mdi-logout font-size-16 align-middle mr-1"></i>
                                <span>Logout</span>
                            </a>
                        </li>





                    </ul>


                </div>
                <!-- Sidebar -->
            </div>`
        </div>
        <!-- Left Sidebar End -->
