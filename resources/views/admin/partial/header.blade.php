<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">


<!-- Mirrored from themesbrand.com/velzon/html/master/dashboard-analytics.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 16 Jun 2025 07:06:57 GMT -->
<head>

    <meta charset="utf-8" />
    <title>Analytics | Velzon - Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('images/favicon.ico')}}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- plugin css -->
    <link href="{{asset('libs/jsvectormap/jsvectormap.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="{{asset('js/layout.js')}}"></script>
    <!-- Bootstrap Css -->
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{asset('css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{asset('css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{asset('css/custom.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('css/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />

    
    <link href="{{asset('libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
  <style>
    .cke_notifications_area {
    display: none !important;
}
</style>

</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index.html" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{asset('images/logo-sm.png')}}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{asset('images/logo-dark.png')}}" alt="" height="17">
                        </span>
                    </a>

                    <a href="index.html" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{asset('images/logo-sm.png')}}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{asset('images/logo-light.png')}}" alt="" height="17">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <!-- App Search-->
            </div>

            <div class="d-flex align-items-center">

               

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="{{asset('images/users/avatar-1.jpg')}}" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                @if(Auth::check())
                                    <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{Auth::user()->name}}</span>
                                    @if(Auth::user()->role_id =='1')
                                      <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Admin</span>
                                    @else
                                       <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Sale Manger</span>
                                    @endif
                                @else
                                    <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"></span>
                                    <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text"></span>
                                @endif
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        @if(Auth::check())
                        <h6 class="dropdown-header">Welcome {{Auth::user()->name}}!</h6>
                        @else
                         <h6 class="dropdown-header"></h6>
                        @endif
                        <a class="dropdown-item" href="{{route('logout')}}"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- removeNotificationModal -->
<!--<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">-->
<!--    <div class="modal-dialog modal-dialog-centered">-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-header">-->
<!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>-->
<!--            </div>-->
<!--            <div class="modal-body">-->
<!--                <div class="mt-2 text-center">-->
<!--                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>-->
<!--                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">-->
<!--                        <h4>Are you sure ?</h4>-->
<!--                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">-->
<!--                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>-->
<!--                    <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>-->
<!--                </div>-->
<!--            </div>-->

<!--        </div><!-- /.modal-content -->-->
<!--    </div><!-- /.modal-dialog -->-->
<!--</div><!-- /.modal -->-->
