<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">


<!-- Mirrored from themesbrand.com/velzon/html/master/auth-signin-basic.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 16 Jun 2025 07:07:47 GMT -->
<head>

    <meta charset="utf-8" />
    <title>Sign In | TruDataa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('images/favicon.ico')}}">

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

</head>

<body>

    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <!-- end row -->
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4 " style="background-color:#002b45;">

                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <img src="{{asset('images/trudataa_logo.png')}}" width="100px"/>
                                    <h5 class="text-light ">Welcome Back !</h5>
                                    <p class="text-light">Sign in to continue to Trudataa.</p>
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-2 mt-4">
                                    <form action="{{route('loginSave')}}" method="post">
                                        @csrf
                                        @method('POST')
                                        <div class="mb-3">
                                            <label for="username" class="form-label text-light">Username</label>
                                            <input type="text" class="form-control" name="username" id="username" placeholder="Enter username" required maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label text-light" for="password-input">Password</label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" name="password" class="form-control pe-5 password-input" placeholder="Enter password" id="password-input" required>
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" type="submit">Sign In</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">        
                            &copy; {{ now()->year }} <strong>TruDataa</strong>. All rights reserved.

                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer> 
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="{{asset('libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('libs/node-waves/waves.min.js')}}"></script>
    <script src="{{asset('libs/feather-icons/feather.min.js')}}"></script>
    <script src="{{asset('js/pages/plugins/lord-icon-2.1.0.')}}"></script>
    <script src="{{asset('js/plugins.js')}}"></script>

    <!-- particles js -->
    <script src="{{asset('libs/particles.js/particles.js')}}"></script>
    <!-- particles app js -->
    <script src="{{asset('js/pages/particles.app.js')}}"></script>
    <!-- password-addon init -->
    <script src="{{asset('js/pages/password-addon.init.js')}}"></script>
</body>


<!-- Mirrored from themesbrand.com/velzon/html/master/auth-signin-basic.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 16 Jun 2025 07:07:48 GMT -->
</html>