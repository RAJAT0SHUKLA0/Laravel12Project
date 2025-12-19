<div class="app-menu navbar-menu" style="background-color:#002b45;">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="{{route('dashboard')}}" class="logo logo-light" >
                    
                    <img src="{{asset('images/trudataa_logo.png')}}" width="100px"/>
                </a>
                <!-- Light Logo-->
                
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>
    
            <div class="dropdown sidebar-user m-1 rounded">
                <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="d-flex align-items-center gap-2">
                        <img class="rounded header-profile-user" src="{{asset('images/users/avatar-1.jpg')}}" alt="Header Avatar">
                        <span class="text-start">
                            @if(Auth::check())
                                <span class="d-block fw-medium sidebar-user-name-text">{{Auth::user()->name}}</span>
                            @else
                                <span class="d-block fw-medium sidebar-user-name-text"></span>
                            @endif
                            <span class="d-block fs-14 sidebar-user-name-sub-text"><i class="ri ri-circle-fill fs-10 text-success align-baseline"></i> <span class="align-middle">Online</span></span>
                        </span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    @if(Auth::check())
                        <h6 class="dropdown-header">Welcome {{Auth::user()->name}}!</h6>
                    @else
                        <h6 class="dropdown-header">Welcome {{Auth::user()->name}}!</h6>
                    @endif
                    <a class="dropdown-item" href="pages-profile.html"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Profile</span></a>
                    <a class="dropdown-item" href="apps-chat.html"><i class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Messages</span></a>
                    <a class="dropdown-item" href="apps-tasks-kanban.html"><i class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Taskboard</span></a>
                    <a class="dropdown-item" href="pages-faqs.html"><i class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Help</span></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="pages-profile.html"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>$5971.67</b></span></a>
                    <a class="dropdown-item" href="pages-profile-settings.html"><span class="badge bg-success-subtle text-success mt-1 float-end">New</span><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Settings</span></a>
                    <a class="dropdown-item" href="auth-lockscreen-basic.html"><i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Lock screen</span></a>
                    <a class="dropdown-item" href="auth-logout-basic.html"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
                </div>
            </div>
            <div id="scrollbar">
                <div class="container-fluid">

                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="ri-layout-3-line"></i> <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ Route::is('state', 'city', 'area', 'leaveType') ? '' : 'collapsed' }}" 
                                href="#sidebarDashboards" 
                                data-bs-toggle="collapse" 
                                role="button" 
                                aria-expanded="{{ Route::is('state', 'city', 'area', 'leaveType') ? 'true' : 'false' }}" 
                                aria-controls="sidebarDashboards">
                                <i class="ri-dashboard-2-line"></i> <span>Master</span>
                            </a>
                        
                            <div class="collapse menu-dropdown {{ Route::is('state', 'city', 'area', 'leaveType') ? 'show' : '' }}" id="sidebarDashboards">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('state') }}" class="nav-link {{ Route::is('state') ? 'active' : '' }}">State Management</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('city') }}" class="nav-link {{ Route::is('city') ? 'active' : '' }}">City Management</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('area') }}" class="nav-link {{ Route::is('area') ? 'active' : '' }}">Area Management</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('leaveType') }}" class="nav-link {{ Route::is('leaveType') ? 'active' : '' }}">Leave Type Management</a>
                                    </li>
                                      <li class="nav-item">
                                        <a href="{{ route('sellerType') }}" class="nav-link {{ Route::is('sellerType') ? 'active' : '' }}">Seller Type Management</a>
                                    </li>
                                   
                                </ul>
                            </div>
                        </li>
                        
                         <li class="nav-item">
                            <a class="nav-link menu-link" href="#category1" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-account-circle-line"></i> <span data-key="t-dashboards">Product Management</span>
                            </a>
                            <div class="collapse menu-dropdown" id="category1">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('categorylist')}}" class="nav-link" data-key="t-analytics"> CATEGORY LIST </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="collapse menu-dropdown" id="category1">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('subcategorylist')}}" class="nav-link" data-key="t-analytics"> SUBCATEGORY LIST </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="collapse menu-dropdown" id="category1">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('brandlist')}}" class="nav-link" data-key="t-analytics">BRAND LIST </a>
                                    </li>
                                </ul>
                            </div>
                             <div class="collapse menu-dropdown" id="category1">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('varientlist')}}" class="nav-link" data-key="t-analytics"> VARIENT LIST </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="collapse menu-dropdown" id="category1">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('Product')}}" class="nav-link" data-key="t-analytics"> PRODUCT LIST  </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                         <li class="nav-item">
                            <a class="nav-link menu-link" href="#order" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-store-2-fill me-1 align-bottom"></i> <span data-key="t-dashboards">Order Management</span>
                            </a>
                            <div class="collapse menu-dropdown" id="order">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('order')}}" class="nav-link" data-key="t-analytics"> Order List </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{route('orderAssign')}}" class="nav-link" data-key="t-analytics"> Order Assign </a>
                                    </li>
                                </ul>
                            </div>
                            
                            
                        </li>
                        


                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#Staff" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-account-circle-line"></i> <span data-key="t-dashboards">Staff Management</span>
                            </a>
                            <div class="collapse menu-dropdown" id="Staff">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('stafflist')}}" class="nav-link" data-key="t-analytics"> Staff List </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                         <li class="nav-item">
                            <a class="nav-link menu-link" href="#leave" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Leave Management</span>
                            </a>
                            <div class="collapse menu-dropdown" id="leave">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('leavelist')}}" class="nav-link" data-key="t-analytics"> Leave List </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                           <li class="nav-item">
                            <a class="nav-link menu-link" href="#expense" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Expense Management</span>
                            </a>
                            <div class="collapse menu-dropdown" id="expense">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('expenselist')}}" class="nav-link" data-key="t-analytics"> Expense List </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                         <li class="nav-item">
                            <a class="nav-link menu-link" href="#attendance" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Attendance </span>
                            </a>
                            <div class="collapse menu-dropdown" id="attendance">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('attendanceList')}}" class="nav-link" data-key="t-analytics"> Attendance List </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#location" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Location Management</span>
                            </a>
                            <div class="collapse menu-dropdown" id="location">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('locationList')}}" class="nav-link" data-key="t-analytics"> Location List </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                         <li class="nav-item">
                            <a class="nav-link menu-link" href="#regularize" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Regularize Management</span>
                            </a>
                            <div class="collapse menu-dropdown" id="regularize">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('regularizelist')}}" class="nav-link" data-key="t-analytics"> Regularize List </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                         <li class="nav-item">
                            <a class="nav-link menu-link" href="#seller" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Seller Management</span>
                            </a>
                            <div class="collapse menu-dropdown" id="seller">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('sellerlist')}}" class="nav-link" data-key="t-analytics"> Seller List </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link menu-link" href="#Bill" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class=" ri-bill-line me-1 align-bottom"></i> <span data-key="t-dashboards">Bill Management</span>
                            </a>
                            <div class="collapse menu-dropdown" id="Bill">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('billList')}}" class="nav-link" data-key="t-analytics"> Bill List </a>
                                    </li>
                                     <li class="nav-item">
                                        <a href="{{route('riderBillList')}}" class="nav-link" data-key="t-analytics"> Rider Bill settlement </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                         <li class="nav-item">
                            <a class="nav-link menu-link" href="#Report" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class=" ri-bill-line me-1 align-bottom"></i> <span data-key="t-dashboards">Report Management</span>
                            </a>
                            <div class="collapse menu-dropdown" id="Report">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('billTransactionReport')}}" class="nav-link" data-key="t-analytics"> Bill Report </a>
                                    </li>
                                      <li class="nav-item">
                                        <a href="{{route('orderReport')}}" class="nav-link" data-key="t-analytics"> Order Report </a>
                                    </li>
                                    
                                </ul>
                            </div>
                        </li>
                        
                         <li class="nav-item">
                            <a class="nav-link menu-link" href="#Settings" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-settings-4-line"></i> <span data-key="t-dashboards">Settings</span>
                            </a>
                            <div class="collapse menu-dropdown" id="Settings">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link menu-link" href="#menu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                            <i class="ri-apps-2-line"></i> <span data-key="t-dashboards">App Menu </span>
                                        </a>
                                        <div class="collapse menu-dropdown" id="menu">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="{{route('Menu')}}" class="nav-link" data-key="t-analytics"> Menu List </a>
                                                </li>
                                                 <li class="nav-item">
                                                    <a href="{{route('SubMenu')}}" class="nav-link" data-key="t-analytics"> Sub Menu List </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('SubMenuType')}}" class="nav-link" data-key="t-analytics"> Sub Menu Type List </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link" href="#permission" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                            <i class="mdi-apple-keyboard-control"></i> <span data-key="t-dashboards">App Permission </span>
                                        </a>
                                        <div class="collapse menu-dropdown" id="permission">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="{{route('Permission')}}" class="nav-link" data-key="t-analytics"> permission List </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                   
                                </ul>
                            </div>
                        </li>
                        
                        
                        
                     
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
