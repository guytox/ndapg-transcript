
<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Maintenance | Apaxy - Responsive Bootstrap 4 Admin Dashboard</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesdesign" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- Bootstrap Css -->
        <link href="{{ asset('admin/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('admin/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    </head>

    <body>

        <div class="container">
            <nav class="navbar navbar-light bg-light justify-content-between">
                <img src="{{ asset('assets/img/logo/logo.jpg') }}" height="50" alt="logo">
                <a  class="btn btn-outline-success btn-lg" href="{{ route('login') }}">Login</a>

              </nav></div>


        <div class="account-pages my-5 pt-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-7">
                        <div class="text-center mb-5">
                            <div class="mb-5">
                                <h1>Nigerian Defence Academy </h1>
                            </div>
                            <h4 class="mt-4">Postgraduate School</h4>
                            <p>The Nigerian Defence Academy Postgraduate School Admission into various Posgraduate Programmes for the 2022/2023 Academic Session as Approved By the Commandant, NDA is here by released</p>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('register') }}" class="btn btn-primary">Create Account for Fresh Application</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                        <a href="{{ route('login') }}" class="btn btn-success">Login to continue Application</a>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <div class="row pt-4 align-items-center justify-content-center">
                    <div class="col-sm-5">
                        <div class="">
                            <img src="{{ asset('admin/assets/images/bean.jpg') }}" alt="" class="img-fluid mx-auto d-block">
                        </div>
                    </div>
                    <div class="col-lg-6 ml-lg-auto">
                        <div class="mt-5 mt-lg-0">
                            <div class="card maintenance-box">
                                <div class="card-body p-4">
                                    <div class="media">
                                        <div class="avatar-xs mr-3">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                01
                                            </span>
                                        </div>
                                        <div class="media-body">
                                            <h5 class="font-size-15 text-uppercase">Applications Guidelines</h5>

                                            <p class="text-muted mb-0"><a
                                                href="{{ asset('assets/NDA_SPGS_OnlineApplicationGuide.pdf') }}"
                                                class="text-decoration-underline">Download Application Guidelines</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card maintenance-box">
                                <div class="card-body p-4">
                                    <div class="media">
                                        <div class="avatar-xs mr-3">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                02
                                            </span>
                                        </div>
                                        <div class="media-body">
                                            <h5 class="font-size-15 text-uppercase">
                                                Advert for Academic Programs</h5>
                                                <p class="text-muted mb-0"><a
                                                    href="{{ asset('assets/PGADVERTSEMENTFULLTIME-PROGRAMMES-2022.pdf') }}"
                                                    class="text-decoration-underline">Download Advert for Academic Programmes</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card maintenance-box">
                                <div class="card-body p-4">
                                    <div class="media">
                                        <div class="avatar-xs mr-3">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                03
                                            </span>
                                        </div>
                                        <div class="media-body">
                                            <h5 class="font-size-15 text-uppercase">
                                                Advert For professional programs</h5>
                                            <p class="text-muted mb-0"><a
                                                href="{{ asset('assets/PG-Professionals-22.pdf') }}"
                                                        class="text-decoration-underline">Download Advert For Professional Programmes</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <!-- end Account pages -->

        <!-- JAVASCRIPT -->
        <script src="{{ asset('admin/assets/libs/jquery/jquery.min.js')}}"></script>
        <script src="{{ asset('admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{ asset('admin/assets/libs/metismenu/metisMenu.min.js')}}"></script>
        <script src="{{ asset('admin/assets/libs/simplebar/simplebar.min.js')}}"></script>
        <script src="{{ asset('admin/assets/libs/node-waves/waves.min.js')}}"></script>

        <script src="{{ asset('admin/assets/js/app.js') }}"></script>

    </body>
</html>