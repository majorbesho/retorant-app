<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <title>{{ $restaurant->getTranslated('name') }} - {{ __('app.name') }}</title>
    <meta name="description" content="{{ Str::limit($restaurant->getTranslated('description'), 160) }}">
    
    <!-- Open Graph / SEO -->
    <meta property="og:title" content="{{ $restaurant->getTranslated('name') }}">
    <meta property="og:description" content="{{ Str::limit($restaurant->getTranslated('description'), 160) }}">
    <meta property="og:type" content="restaurant.restaurant">
    <meta property="og:url" content="{{ route('restaurant.show', $restaurant->slug) }}">
    @if($restaurant->cover_image)
    <meta property="og:image" content="{{ asset($restaurant->cover_image) }}">
    @endif

    <!-- Stylesheets -->
    <link href="{{ asset('ref/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('ref/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('ref/css/style-4.css') }}" rel="stylesheet">

    <link rel="shortcut icon" href="{{ asset('ref/images/favicon4.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('ref/images/favicon4.png') }}" type="image/x-icon">

    <!-- Responsive -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @if(app()->getLocale() == 'ar')
    <style>
        body { direction: rtl; text-align: right; }
        .main-header .nav-outer .main-menu .navigation > li { float: right; }
        .text-reveal-anim { text-align: right; }
        .me-2 { margin-left: 0.5rem !important; margin-right: 0 !important; }
        .ms-2 { margin-right: 0.5rem !important; margin-left: 0 !important; }
        /* Fix directional icons */
        .fa-angle-right:before { content: "\f104"; } 
        .fa-angle-left:before { content: "\f105"; }
    </style>
    @endif
</head>

<body>
    <div class="page-wrapper">

        <!-- Preloader start -->
        <div class="preloader">
            <svg viewbox="0 0 1000 1000" preserveaspectratio="none">
                <path id="preloaderSvg" d="M0,1005S175,995,500,995s500,5,500,5V0H0Z"></path>
            </svg>
            <div class="preloader-heading">
                <div class="load-text">
                    <span>L</span>
                    <span>o</span>
                    <span>a</span>
                    <span>d</span>
                    <span>i</span>
                    <span>n</span>
                    <span>g</span>
                </div>
            </div>
        </div>
        <!-- Preloader end -->

        <!-- Main Header-->
        <header class="main-header header-style-one">
            <div class="container">
                <div class="header-lower">
                    <div class="inner-container">
                        <!-- Main box -->
                        <div class="main-box">
                            <div class="logo-box">
                                <div class="logo">
                                    <a href="{{ route('home') }}"><img src="{{ asset('ref/images/logo4.png') }}"
                                            alt="Restaurant AI Logo"></a>
                                </div>
                            </div>

                            <!--Nav Box-->
                            <div class="nav-outer">
                                <nav class="nav main-menu">
                                    <ul class="navigation">
                                        <li><a href="{{ route('home') }}">Home</a></li>
                                        <li class="current"><a href="#">{{ $restaurant->getTranslated('name') }}</a></li>
                                    </ul>
                                </nav>
                            </div>

                            <!-- Outer Box -->
                            <div class="action-box">
                                <div class="header-btn" style="margin-right: 15px;">
                                    <a class="header-btn-main theme-btn" href="{{ route('locale.switch', app()->getLocale() == 'ar' ? 'en' : 'ar') }}">
                                        <span class="btn-text">{{ app()->getLocale() == 'ar' ? 'English' : 'عربي' }}</span>
                                    </a>
                                </div>
                                <div class="header-btn">
                                    <a class="header-btn-main theme-btn" href="{{ route('login') }}"><span
                                            class="btn-text">Sign In</span></a>
                                </div>
                                <div class="mobile-nav-toggler">
                                    <div class="shape-line-img"><i class="fas fa-bars"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Mobile Menu  -->
            <div class="mobile-menu">
                <div class="menu-backdrop"></div>
                <nav class="menu-box">
                    <div class="upper-box">
                        <div class="nav-logo">
                            <a href="{{ route('home') }}"><img src="{{ asset('ref/images/logo4.png') }}"
                                    alt=""></a>
                        </div>
                        <div class="close-btn"><i class="icon fa fa-times"></i></div>
                    </div>
                    <ul class="navigation clearfix">
                        <!--Menu will come through Javascript-->
                    </ul>
                </nav>
            </div>
            <!-- End Mobile Menu -->
        </header>
        <!--End Main Header -->

        <!-- Restaurant Banner -->
        <section class="banner-section-four" style="min-height: 400px; padding-top: 150px;">
            <div class="container">
                <div class="content-box">
                    <div class="inner-box text-center">
                        <h1 class="title" style="color: #292929;">{{ $restaurant->getTranslated('name') }}</h1>
                        <div class="text" style="color: #666; font-size: 18px;">
                            {{ $restaurant->getTranslated('description') }}
                        </div>
                        <div class="info-list mt-4">
                            <span class="badge bg-primary me-2">{{ $restaurant->cuisine_type }}</span>
                            <span class="badge bg-secondary"><i class="fas fa-map-marker-alt"></i> {{ $restaurant->city }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Menu Section -->
        <section class="features-section-two-h4 pb-0 pt-40" id="menu">
            <div class="outer-box">
                <div class="container">
                    <div class="sec-title home4 text-center">
                        <span class="sub-title">Our Menu</span>
                        <h2 class="title text-reveal-anim">Delicious Food</h2>
                    </div>
                    
                    <div class="row">
                        @foreach($restaurant->menus as $menu)
                            <div class="col-12 mb-5">
                                <h3 class="text-center mb-4">{{ $menu->getTranslated('name') }}</h3>
                                @foreach($menu->categories as $category)
                                    <div class="category-block mb-4">
                                        <h4 class="mb-3" style="color: #FF6600;">{{ $category->getTranslated('name') }}</h4>
                                        <div class="row">
                                            @foreach($category->products as $product)
                                            <div class="col-md-6 col-lg-4 mb-4">
                                                <div class="features-block-h4" style="height: 100%; align-items: flex-start; padding: 20px;">
                                                    <div class="content" style="width: 100%;">
                                                        <div class="d-flex justify-content-between">
                                                            <h5 class="title" style="font-size: 18px;">{{ $product->getTranslated('name') }}</h5>
                                                            <span class="price" style="font-weight: bold; color: #FF6600;">{{ $product->price }}</span>
                                                        </div>
                                                        <div class="text" style="margin-top: 5px; font-size: 14px;">{{ $product->getTranslated('description') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Footer -->
        <footer class="main-footer footer-style-four">
            <div class="container">
                <div class="footer-bottom4">
                    <p>
                        © Copyright {{ date('Y') }} by {{ $restaurant->getTranslated('name') }}. Powered by {{ __('app.name') }}.
                    </p>
                </div>
            </div>
        </footer>

    </div>
    <!-- End Page Wrapper -->

    <script src="{{ asset('ref/js/jquery.js') }}"></script>
    <script src="{{ asset('ref/js/popper.min.js') }}"></script>
    <script src="{{ asset('ref/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('ref/js/jquery.fancybox.js') }}"></script>
    <script src="{{ asset('ref/js/wow.js') }}"></script>
    <script src="{{ asset('ref/js/appear.js') }}"></script>
    <script src="{{ asset('ref/js/script.js') }}"></script>

</body>
</html>
