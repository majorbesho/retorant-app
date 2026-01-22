<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

    <!-- خط تاجوال للعربية (اختياري لكن موصى به) -->
    @if(app()->getLocale() == 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
        <style>
            body, h1, h2, h3, h4, h5, h6, .nav-sidebar .nav-link, .main-header .nav-link {
                font-family: 'Tajawal', 'Source Sans Pro', sans-serif !important;
            }
            .content-wrapper { direction: rtl; text-align: right; }
        </style>
    @endif
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-user"></i> {{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="dropdown-item">
                            <i class="fas fa-sign-out-alt mr-2"></i> تسجيل الخروج
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('home') }}" class="brand-link">
                <span class="brand-text font-weight-light">لوحة التحكم</span>
            </a>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>الرئيسية</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('menus.index') }}" class="nav-link {{ request()->routeIs('menus.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-utensils"></i>
                                <p>المنيو</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list"></i>
                                <p>التصنيفات</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-box"></i>
                                <p>المنتجات</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('addon_groups.index') }}" class="nav-link {{ request()->routeIs('addon_groups.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-puzzle-piece"></i>
                                <p>الإضافات</p>
                            </a>
                        </li>


                         <li class="nav-item">
                            <a href="{{ route('variations.index') }}" class="nav-link {{ request()->routeIs('variations.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-puzzle-piece"></i>
                                <p>الخيارات</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('restaurant.settings') }}" class="nav-link {{ request()->routeIs('restaurant.settings') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>إعدادات المطعم</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.ai-agents.index') }}" class="nav-link {{ request()->routeIs('admin.ai-agents.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-robot"></i>
                                <p>وكلاء AI</p>
                            </a>
                        </li>

                        @can('manage-requests')
                        <li class="nav-item">
                            <a href="{{ route('admin.restaurant_requests.index') }}" class="nav-link {{ request()->routeIs('admin.restaurant_requests.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-envelope-open-text"></i>
                                <p>طلبات التسجيل</p>
                            </a>
                        </li>
                        @endcan

                        <li class="nav-item">
                            <a href="{{ route('admin.audit-logs.index') }}" class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-history"></i>
                                <p>سجل النشاطات</p>
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    @yield('content-header')
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>حقوق النشر © {{ date('Y') }} <a href="#">{{ config('app.name') }}</a>.</strong>
        </footer>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE JS -->
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
</body>
</html>