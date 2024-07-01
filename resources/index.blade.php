<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>{{ env('APP_NAME') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ env('APP_NAME') }}"/>
    <meta name="keywords" content="{{ env('APP_NAME') }}"/>
    <meta name="author" content="{{ env('APP_NAME') }}"/>
    <meta name="email" content="info@khtwah.com"/>
    <meta name="website" content="https://khtwah.com"/>
    <meta name="Version" content="v1.0.0"/>
    <!-- favicon -->
    <link rel="shortcut icon" href="{{ asset('/frontend/images/fav.png') }}">
    <!-- Bootstrap -->
    <link href="{{ asset('/frontend/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- Icons -->
    <link href="{{ asset('/frontend/css/materialdesignicons.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v2.1.9/css/unicons.css">
    <!-- Slider -->
    <link rel="stylesheet" href="{{ asset('/frontend/css/owl.carousel.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('/frontend/css/owl.theme.default.min.css') }}"/>
    <!-- Main Css -->
    <link href="{{ asset('/frontend/css/style-rtl.css') }}" rel="stylesheet" type="text/css" id="theme-opt"/>
    <link href="{{ asset('/frontend/css/colors/default.css') }}" rel="stylesheet" id="color-opt">

</head>

<body>
<!-- Loader -->
<div id="preloader">
    <div id="status">
        <div class="spinner">
            <div class="double-bounce1"></div>
            <div class="double-bounce2"></div>
        </div>
    </div>
</div>
<!-- Loader -->

<!-- Navbar STart -->
<header id="topnav" class="defaultscroll sticky">
    <div class="container">
        <!-- Logo container-->
        <div>
            <a class="logo" href="{{ url('/') }}">
                <img src="{{ asset('/frontend/images/logo.png') }}" height="24" alt=""> تطبيق إشارتي
            </a>
        </div>
        <div class="buy-button">
            <a href="#download" class="btn btn-primary">تحميل التطبيق</a>
        </div><!--end login button-->
        <!-- End Logo container-->
        <div class="menu-extras">
            <div class="menu-item">
                <!-- Mobile menu toggle-->
                <a class="navbar-toggle">
                    <div class="lines">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </a>
                <!-- End mobile menu toggle-->
            </div>
        </div>

    </div><!--end container-->
</header><!--end header-->
<!-- Navbar End -->

<!-- Hero Start -->
<section class="bg-half-170 d-table w-100" id="home">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-7">
                <div class="title-heading mt-4">
                    <h1 class="heading mb-3">تطبيق <span class="text-primary">إشارتي</span></h1>
                    <p class="para-desc text-muted">تطبيق هدفه تسهيل عملية التواصل مع الصم من خلال التدريب والترجمة</p>
                    <div class="mt-4">
                        <a href="https://apps.apple.com/sa/app/esharti-%D8%A5%D8%B4%D8%A7%D8%B1%D8%AA%D9%8A/id1539325578" class="btn btn-primary mt-2 mr-2"><i class="mdi mdi-apple"></i> آب ستور</a>
                        <a href="https://play.google.com/store/apps/details?id=com.esharty&hl=en&gl=US" class="btn btn-outline-primary mt-2"><i class="mdi mdi-google-play"></i> جوجل بلاي</a>
                    </div>
                </div>
            </div><!--end col-->

            <div class="col-lg-6 col-md-5 mt-4 pt-2 mt-sm-0 pt-sm-0">
                <div class="text-md-right text-center">
                    <img src="{{ asset('/frontend/images/mobile/Artboard – 3.png') }}" class="img-fluid" alt="">
                </div>
            </div>
        </div><!--end row-->
    </div><!--end container-->
</section><!--end section-->
<!-- Hero End -->

<!-- Shape Start -->
<div class="position-relative">
    <div class="shape overflow-hidden text-light">
        <svg viewBox="0 0 2880 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 48H1437.5H2880V0H2160C1442.5 52 720 0 720 0H0V48Z" fill="currentColor"></path>
        </svg>
    </div>
</div>
<!--Shape End-->

<!-- Features Start -->
<section class="section bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <div class="section-title mb-4 pb-2">
                    <h4 class="title mb-4">ميزات التطبيق</h4>
                </div>
            </div><!--end col-->
        </div><!--end row-->

        <div class="row justify-content-center align-items-center">
            <div class="col-lg-8 col-md-8">
                <div class="row mt-4 pt-2">
                    <div class="col-md-6 col-12">
                        <div class="media features pt-4 pb-4">
                            <div class="icon text-center rounded-circle text-primary mr-3 mt-2">
                                <i data-feather="monitor" class="fea icon-ex-md text-primary"></i>
                            </div>
                            <div class="media-body">
                                <h4 class="title"> قواميس الكترونية</h4>
                            </div>
                        </div>
                    </div><!--end col-->

                    <div class="col-md-6 col-12">
                        <div class="media features pt-4 pb-4">
                            <div class="icon text-center rounded-circle text-primary mr-3 mt-2">
                                <i data-feather="eye" class="fea icon-ex-md text-primary"></i>
                            </div>
                            <div class="media-body">
                                <h4 class="title">دورات تدريبية</h4>
                            </div>
                        </div>
                    </div><!--end col-->

                    <div class="col-md-6 col-12">
                        <div class="media features pt-4 pb-4">
                            <div class="icon text-center rounded-circle text-primary mr-3 mt-2">
                                <i data-feather="user-check" class="fea icon-ex-md text-primary"></i>
                            </div>
                            <div class="media-body">
                                <h4 class="title">تطبيق تفاعلي</h4>
                            </div>
                        </div>
                    </div><!--end col-->

                    <div class="col-md-6 col-12">
                        <div class="media features pt-4 pb-4">
                            <div class="icon text-center rounded-circle text-primary mr-3 mt-2">
                                <i data-feather="heart" class="fea icon-ex-md text-primary"></i>
                            </div>
                            <div class="media-body">
                                <h4 class="title">دورات متعددة</h4>
                            </div>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end col-->

            <div class="col-lg-4 col-md-4 col-12 mt-4 pt-2 text-center text-md-right">
                <img src="{{ asset('/frontend/images/mobile/Artboard – 5.png') }}" class="img-fluid" alt="">
            </div><!--end col-->
        </div><!--end row-->
    </div><!--end container-->
</section><!--end section-->
<!-- Feature End -->

<!-- Showcase Start -->
<section class="section pt-0 bg-light">
    <div class="container">

        <div class="row align-items-center">
            <div class="col-lg-5 col-md-6 mt-4 pt-2">
                <img src="{{ asset('/frontend/images/mobile/Artboard – 6.png') }}" class="img-fluid mx-auto d-block" alt="">
            </div><!--end col-->

            <div class="col-lg-7 col-md-6 mt-4 pt-2">
                <div class="section-title ml-lg-5">
                    <h4 class="title mb-4">دورات مختلفة و متنوعة</h4>
                    <p class="text-muted">التحق بالدورة ، و ادرس بأي وقت ومن أي مكان ،</p>
                    <p class="text-muted">دورات مسجلة بأعلى جودة في أي وقت .. ومن أي مكان ،</p>
                </div>
            </div><!--end col-->
        </div><!--end row-->
    </div><!--end container-->

    <div class="container mt-100 mt-60">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <div class="section-title mb-4 pb-2">
                    <h4 class="title mb-4">قاموس إرشادي لأهم المصطلحات الإشارية للتواصل مع الصم </h4>
                </div>
            </div><!--end col-->
        </div><!--end row-->

        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12 mt-4 pt-2 text-center">
                <ul class="nav nav-pills nav-justified flex-column flex-sm-row rounded" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link rounded active" id="pills-cloud-tab" data-toggle="pill" href="#pills-cloud" role="tab" aria-controls="pills-cloud" aria-selected="false">
                            <div class="text-center pt-1 pb-1">
                                <h4 class="title font-weight-normal mb-0">قائمة الدورات</h4>
                            </div>
                        </a><!--end nav link-->
                    </li><!--end nav item-->

                    <li class="nav-item">
                        <a class="nav-link rounded" id="pills-smart-tab" data-toggle="pill" href="#pills-smart" role="tab" aria-controls="pills-smart" aria-selected="false">
                            <div class="text-center pt-1 pb-1">
                                <h4 class="title font-weight-normal mb-0">قائمة الدروس</h4>
                            </div>
                        </a><!--end nav link-->
                    </li><!--end nav item-->

                    <li class="nav-item">
                        <a class="nav-link rounded" id="pills-apps-tab" data-toggle="pill" href="#pills-apps" role="tab" aria-controls="pills-apps" aria-selected="false">
                            <div class="text-center pt-1 pb-1">
                                <h4 class="title font-weight-normal mb-0">صفحة الدورة</h4>
                            </div>
                        </a><!--end nav link-->
                    </li><!--end nav item-->
                </ul><!--end nav pills-->
            </div>
        </div>

        <div class="row">
            <div class="col-12 mt-4 pt-2">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-cloud" role="tabpanel" aria-labelledby="pills-cloud-tab">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <img src="{{ asset('/frontend/images/mobile/Artboard – 7.png') }}" class="img-fluid mx-auto d-block" alt="">
                            </div><!--end col-->
                        </div><!--end row-->
                    </div><!--end teb pane-->

                    <div class="tab-pane fade" id="pills-smart" role="tabpanel" aria-labelledby="pills-smart-tab">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <img src="{{ asset('/frontend/images/mobile/Artboard – 8.png') }}" class="img-fluid mx-auto d-block" alt="">
                            </div><!--end col-->

                        </div>    <!--end row-->
                    </div><!--end teb pane-->

                    <div class="tab-pane fade" id="pills-apps" role="tabpanel" aria-labelledby="pills-apps-tab">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <img src="{{ asset('/frontend/images/mobile/Artboard – 9.png') }}" class="img-fluid mx-auto d-block" alt="">
                            </div><!--end col-->
                        </div>    <!--end row-->
                    </div><!--end teb pane-->
                </div><!--end tab content-->
            </div><!--end col-->
        </div><!--end row-->
    </div><!--end container-->
</section><!--end section-->
<!-- Showcase End -->

<!-- Shape Start -->
<div class="position-relative">
    <div class="shape overflow-hidden text-white">
        <svg viewBox="0 0 2880 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 48H1437.5H2880V0H2160C1442.5 52 720 0 720 0H0V48Z" fill="currentColor"></path>
        </svg>
    </div>
</div>
<!--Shape End-->

<!-- Testi n Download cta start -->
<section class="section pt-0">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div id="customer-testi" class="owl-carousel owl-theme">

                </div>
            </div><!--end col-->
        </div><!--end row-->

        <div class="row mt-md-5 pt-md-3 mt-4 pt-2 mt-sm-0 pt-sm-0 justify-content-center" id="download">
            <div class="col-12 text-center">
                <div class="section-title">
                    <h4 class="title mb-4">حمل التطبيق الآن!</h4>
                    <p class="text-muted para-desc mx-auto">متوفر على المتجرين <span class="text-primary font-weight-bold">جوجل بلاي</span> و<span class="text-primary font-weight-bold">آب ستور</span></p>
                    <div class="mt-4">
                        <a href="https://apps.apple.com/sa/app/esharti-%D8%A5%D8%B4%D8%A7%D8%B1%D8%AA%D9%8A/id1539325578" class="btn btn-primary mt-2 mr-2"><i class="mdi mdi-apple"></i> آب ستور</a>
                        <a href="https://play.google.com/store/apps/details?id=com.esharty&hl=ar&gl=US" class="btn btn-outline-primary mt-2"><i class="mdi mdi-google-play"></i> جوجل بلاي</a>
                    </div>
                </div>
            </div><!--end col-->
        </div><!--end row-->
    </div><!--end container-->
</section><!--end section-->
<!-- Testi n Download cta End -->

<!-- Shape Start -->
<div class="position-relative">
    <div class="shape overflow-hidden text-footer">
        <svg viewBox="0 0 2880 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 48H1437.5H2880V0H2160C1442.5 52 720 0 720 0H0V48Z" fill="currentColor"></path>
        </svg>
    </div>
</div>
<!--Shape End-->

<!-- Footer Start -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-12 mb-0 mb-md-4 pb-0 pb-md-2">
                <a href="#" class="logo-footer">
                    <img src="{{ asset('/frontend/images/logo-2.png') }}" height="24" alt=""> تطبيق إشارتي
                </a>
                <p class="mt-4">تطبيق هدفه تسهيل عملية التواصل مع الصم من خلال التدريب والترجمة
                </p>
                <ul class="list-unstyled social-icon social mb-0 mt-4">
                    <li class="list-inline-item"><a href="https://www.instagram.com/esharti.app" class="rounded"><i data-feather="instagram" class="fea icon-sm fea-social"></i></a></li>
                    <li class="list-inline-item"><a href="https://twitter.com/eshartiapp" class="rounded"><i data-feather="twitter" class="fea icon-sm fea-social"></i></a></li>
                    <li class="list-inline-item"><a href="https://www.youtube.com/eshartiapp" class="rounded"><i data-feather="youtube" class="fea icon-sm fea-social"></i></a></li>
                </ul><!--end icon-->
            </div><!--end col-->

        </div><!--end row-->
    </div><!--end container-->
</footer><!--end footer-->
<footer class="footer footer-bar">
    <div class="container text-center">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="text-sm-left">

                    <p class="mb-0"> حقوق النشر محفوظة لتطبيق إشارتي © {{ date('Y') }}  . بواسطة <i class="mdi mdi-heart text-danger"></i>  <a href="https://o2.com.sa/ar/" target="_blank" class="text-reset">أكسجين التقنية</a>.</p>
                </div>
            </div><!--end col-->

            <div class="col-sm-6 mt-4 mt-sm-0 pt-2 pt-sm-0">
                <ul class="list-unstyled payment-cards text-sm-right mb-0">
                    <li class="list-inline-item"><a href="javascript:void(0)"><img src="{{ asset('/frontend/images/payments/american-ex.png') }}" class="avatar avatar-ex-sm" title="American Express" alt=""></a></li>
                    <li class="list-inline-item"><a href="javascript:void(0)"><img src="{{ asset('/frontend/images/payments/discover.png') }}" class="avatar avatar-ex-sm" title="Discover" alt=""></a></li>
                    <li class="list-inline-item"><a href="javascript:void(0)"><img src="{{ asset('/frontend/images/payments/master-card.png') }}" class="avatar avatar-ex-sm" title="Master Card" alt=""></a></li>
                    <li class="list-inline-item"><a href="javascript:void(0)"><img src="{{ asset('/frontend/images/payments/paypal.png') }}" class="avatar avatar-ex-sm" title="Paypal" alt=""></a></li>
                    <li class="list-inline-item"><a href="javascript:void(0)"><img src="{{ asset('/frontend/images/payments/visa.png') }}" class="avatar avatar-ex-sm" title="Visa" alt=""></a></li>
                </ul>
            </div><!--end col-->
        </div><!--end row-->
    </div><!--end container-->
</footer><!--end footer-->
<!-- Footer End -->

<!-- Back to top -->
<a href="#" class="btn btn-icon btn-soft-primary back-to-top"><i data-feather="arrow-up" class="icons"></i></a>
<!-- Back to top -->

<!-- javascript -->
<script src="{{ asset('/frontend/js/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('/frontend/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('/frontend/js/jquery.easing.min.js') }}"></script>
<script src="{{ asset('/frontend/js/scrollspy.min.js') }}"></script>
<!-- SLIDER -->
<script src="{{ asset('/frontend/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('/frontend/js/owl.init.js') }}"></script>
<!-- Icons -->
<script src="{{ asset('/frontend/js/feather.min.js') }}"></script>
<script src="https://unicons.iconscout.com/release/v2.1.9/script/monochrome/bundle.js"></script>
<!-- Main Js -->
<script src="{{ asset('/frontend/js/app.js') }}"></script>
</body>
</html>
