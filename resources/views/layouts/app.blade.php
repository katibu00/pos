<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="author" content="SemiColonWeb" />

    <!-- Stylesheets
 ============================================= -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="/css/bootstrap.css" type="text/css" />
    <link rel="stylesheet" href="/style.css" type="text/css" />

    <link rel="stylesheet" href="/css/dark.css" type="text/css" />
    {{-- <link rel="stylesheet" href="/css/font-icons.css" type="text/css" /> --}}
    <link rel="stylesheet" href="/css/animate.css" type="text/css" />
    <link rel="stylesheet" href="/css/magnific-popup.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css"
        integrity="sha512-Mo79lrQ4UecW8OCcRUZzf0ntfMNgpOFR46Acj2ZtWO8vKhBvD79VCp3VOKSzk6TovLg5evL3Xi3u475Q/jMu4g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/css/custom.css" type="text/css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/colors.php?color=0275d8" type="text/css" />
    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
    <!-- Bootstrap Select CSS -->
    <link rel="stylesheet" href="/css/components/bs-select.css" type="text/css" />

    <!-- Document Title
 ============================================= -->
    <title>@yield('PageTitle') | El-Habib Plumbing Material and Services Ltd</title>
    <link rel="stylesheet" href="/toastr/toastr.min.css">

</head>

<body class="stretched search-overlay">

    <!-- Document Wrapper
 ============================================= -->
    <div id="wrapper" class="clearfix">


        <!-- Header
  ============================================= -->
        <header id="header" class="full-header header-size-md" data-mobile-sticky="true">
            <div id="header-wrap">
                <div class="container">
                    <div class="header-row">

                        <!-- Logo
      ============================================= -->
                        <div id="logo">
                            <a href="#" class="standard-logo"><img src="/logo.jpg" alt="logo"></a>
                            <a href="#" class="retina-logo"><img src="/logo.jpg" alt="logo"></a>
                        </div><!-- #logo end -->

                        <div class="header-misc ms-0">

                            <!-- Top Account
       ============================================= -->
                            <div class="header-misc-icon">
                                <a href="#" id="notifylink" data-bs-toggle="dropdown" data-bs-offset="0,15"
                                    aria-haspopup="true" aria-expanded="false" data-offset="12,12"><i
                                        class="icon-line-bell notification-badge"></i></a>
                                <div class="dropdown-menu dropdown-menu-end py-0 m-0 overflow-auto"
                                    aria-labelledby="notifylink" style="width: 320px; max-height: 300px">
                                    <span
                                        class="dropdown-header border-bottom border-f5 fw-medium text-uppercase ls1">Notifications</span>
                                    <div class="list-group list-group-flush">

                                        <a href="#" class="d-flex list-group-item">
                                            {{-- <i class="icon-line-check badge-icon bg-success text-white me-3 mt-1"></i> --}}
                                            <div class="media-body">
                                                <h5 class="my-0 fw-normal text-muted"><span class="text-dark fw-bold">No
                                                        New Notification</h5>
                                                {{-- <small class="text-smaller">2 days ago</small> --}}
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Top Account
       ============================================= -->
                            <div class="header-misc-icon profile-image">
                                <a href="#" id="profilelink" data-bs-toggle="dropdown" data-bs-offset="0,15"
                                    aria-haspopup="true" aria-expanded="false" data-offset="12,12"><img
                                        class="rounded-circle" src="/default.png"
                                        alt="{{ auth()->user()->first_name }}"></a>
                                <div class="dropdown-menu dropdown-menu-end py-0 m-0" aria-labelledby="profilelink">
                                    <a class="dropdown-item" href="{{ route('logout') }}"><i
                                            class="icon-line-log-out me-2"></i>Sign Out</a>
                                </div>
                            </div>

                        </div>

                        <div id="primary-menu-trigger">
                            <svg class="svg-trigger" viewBox="0 0 100 100">
                                <path
                                    d="m 30,33 h 40 c 3.722839,0 7.5,3.126468 7.5,8.578427 0,5.451959 -2.727029,8.421573 -7.5,8.421573 h -20">
                                </path>
                                <path d="m 30,50 h 40"></path>
                                <path
                                    d="m 70,67 h -40 c 0,0 -7.5,-0.802118 -7.5,-8.365747 0,-7.563629 7.5,-8.634253 7.5,-8.634253 h 20">
                                </path>
                            </svg>
                        </div>

                        @php
                            $route = Route::current()->getName();
                        @endphp

                        <nav class="primary-menu">

                            <ul class="menu-container">
                                @if (auth()->user()->usertype == 'admin')
                                    <li class="menu-item {{ $route == 'admin.home' ? 'current' : '' }}"><a
                                            class="menu-link" href="{{ route('admin.home') }}">
                                            <div>Home</div>
                                        </a></li>
                                    <li
                                        class="menu-item {{ $route == 'report.index' ? 'current' : '' }} {{ $route == 'report.generate' ? 'current' : '' }}">
                                        <a class="menu-link" href="{{ route('report.index') }}">
                                            <div>Report</div>
                                        </a>
                                    </li>
                                    <li class="menu-item {{ $route == 'expense.index' ? 'current' : '' }} "><a
                                            class="menu-link" href="{{ route('expense.index') }}">
                                            <div>Expense</div>
                                        </a></li>

                                    <li class="menu-item {{ $route == 'purchase.index' ? 'current' : '' }} {{ $route == 'purchase.details' ? 'current' : '' }} {{ $route == 'shopping_list.index' ? 'current' : '' }}">
                                        <a class="menu-link" href="#">
                                            <div>Purchases</div>
                                        </a>
                                        <ul class="sub-menu-container">
                                            <li
                                                class="menu-item {{ $route == 'purchase.index' ? 'current' : '' }} {{ $route == 'purchase.details' ? 'current' : '' }}">
                                                <a class="menu-link" href="{{ route('purchase.index') }}">
                                                    <div><i class="icon-wpforms"></i>Purchases</div>
                                                </a>
                                            </li>
                                            <li
                                                class="menu-item {{ $route == 'shopping_list.index' ? 'current' : '' }}">
                                                <a class="menu-link" href="{{ route('shopping_list.index') }}">
                                                    <div>Low Stocks</div>
                                                </a>
                                            </li>
                                            <li
                                                class="menu-item">
                                                <a class="menu-link" href="#">
                                                    <div>Shopping List</div>
                                                </a>
                                            </li>

                                        </ul>
                                    </li>


                                    <li class="menu-item {{ $route == 'stock.index' ? 'current' : '' }}"><a
                                            class="menu-link" href="{{ route('stock.index') }}">
                                            <div>Inventory</div>
                                        </a></li>
                                   
                                    <li class="menu-item {{ $route == 'sales.index' ? 'current' : '' }} {{ $route == 'credit.index' ? 'current' : '' }}">
                                        <a class="menu-link" href="#">
                                            <div>Sales</div>
                                        </a>
                                        <ul class="sub-menu-container">
                                            <li
                                                class="menu-item {{ $route == 'sales.index' ? 'current' : '' }}">
                                                <a class="menu-link" href="{{ route('sales.index') }}">
                                                    <div><i class="icon-wpforms"></i>Sales</div>
                                                </a>
                                            </li>
                                            <li
                                                class="menu-item {{ $route == 'credit.index' ? 'current' : '' }}">
                                                <a class="menu-link" href="{{ route('credit.index') }}">
                                                    <div>Credit Sales</div>
                                                </a>
                                            </li>
                                            <li
                                                class="menu-item {{ $route == 'sales.all.index' ? 'current' : '' }}">
                                                <a class="menu-link" href="{{ route('sales.all.index') }}">
                                                    <div>All Sales</div>
                                                </a>
                                            </li>

                                        </ul>
                                    </li>
                                  

                                    <li class="menu-item {{ $route == 'users.index' ? 'current' : '' }} {{ $route == 'customers.profile' ? 'current' : '' }} {{ $route == 'customers.index' ? 'current' : '' }}">
                                        <a class="menu-link" href="#">
                                            <div>Users</div>
                                        </a>
                                        <ul class="sub-menu-container">
                                            <li
                                                class="menu-item {{ $route == 'users.index' ? 'current' : '' }} {{ $route == 'users.edit' ? 'current' : '' }}">
                                                <a class="menu-link" href="{{ route('users.index') }}">
                                                    <div><i class="icon-wpforms"></i>Staff</div>
                                                </a>
                                            </li>
                                            <li
                                                class="menu-item {{ $route == 'customers.index' ? 'current' : '' }} {{ $route == 'customers.profile' ? 'current' : '' }}">
                                                <a class="menu-link" href="{{ route('customers.index') }}">
                                                    <div>Customers</div>
                                                </a>
                                            </li>

                                        </ul>
                                    </li>

                                    <li class="menu-item {{ $route == 'estimate.index' ? 'current' : '' }} {{ $route == 'estimate.all.index' ? 'current' : '' }}">
                                        <a class="menu-link" href="#">
                                            <div>Estimate</div>
                                        </a>
                                        <ul class="sub-menu-container">
                                            <li
                                                class="menu-item {{ $route == 'estimate.index' ? 'current' : '' }}">
                                                <a class="menu-link" href="{{ route('estimate.index') }}">
                                                    <div><i class="icon-wpforms"></i>New</div>
                                                </a>
                                            </li>
                                            <li
                                                class="menu-item {{ $route == 'estimate.all.index' ? 'current' : '' }}">
                                                <a class="menu-link" href="{{ route('estimate.all.index') }}">
                                                    <div>All</div>
                                                </a>
                                            </li>

                                        </ul>
                                    </li>

                                    <li class="menu-item {{ $route == 'returns' ? 'current' : '' }}"><a
                                            class="menu-link" href="{{ route('returns') }}">
                                            <div>Returns</div>
                                        </a></li>
                                   
                                @endif

                                @if (auth()->user()->usertype == 'cashier')
                                    <li class="menu-item "><a class="menu-link" href="{{ route('cashier.home') }}">
                                            <div>Home</div>
                                        </a></li>
                                    <li class="menu-item {{ $route == 'sales.index' ? 'current' : '' }} {{ $route == 'credit.index' ? 'current' : '' }}">
                                        <a class="menu-link" href="#">
                                            <div>Sales</div>
                                        </a>
                                        <ul class="sub-menu-container">
                                            <li
                                                class="menu-item {{ $route == 'sales.index' ? 'current' : '' }}">
                                                <a class="menu-link" href="{{ route('sales.index') }}">
                                                    <div><i class="icon-wpforms"></i>Sales</div>
                                                </a>
                                            </li>
                                            <li
                                                class="menu-item {{ $route == 'credit.index' ? 'current' : '' }}">
                                                <a class="menu-link" href="{{ route('credit.index') }}">
                                                    <div>Credit Sales</div>
                                                </a>
                                            </li>

                                        </ul>
                                    </li>
                                    <li class="menu-item {{ $route == 'expense.index' ? 'current' : '' }} "><a
                                        class="menu-link" href="{{ route('expense.index') }}">
                                        <div>Expense</div>
                                    </a></li>
                                   
                                    <li class="menu-item {{ $route == 'returns' ? 'current' : '' }}"><a
                                            class="menu-link" href="{{ route('returns') }}">
                                            <div>Returns</div>
                                        </a></li>
                                    
                                    <li class="menu-item {{ $route == 'estimate.index' ? 'current' : '' }} {{ $route == 'estimate.all.index' ? 'current' : '' }}">
                                        <a class="menu-link" href="#">
                                            <div>Estimate</div>
                                        </a>
                                        <ul class="sub-menu-container">
                                            <li
                                                class="menu-item {{ $route == 'estimate.index' ? 'current' : '' }}">
                                                <a class="menu-link" href="{{ route('estimate.index') }}">
                                                    <div><i class="icon-wpforms"></i>New</div>
                                                </a>
                                            </li>
                                            <li
                                                class="menu-item {{ $route == 'estimate.all.index' ? 'current' : '' }}">
                                                <a class="menu-link" href="{{ route('estimate.all.index') }}">
                                                    <div>All</div>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="menu-item {{ $route == 'customers.index' ? 'current' : '' }} {{ $route == 'customers.profile' ? 'current' : '' }}"><a
                                        class="menu-link" href="{{ route('customers.index') }}">
                                        <div>Customers</div>
                                    </a></li>
    
                                @endif

                            </ul>

                        </nav>

                    </div>
                </div>
            </div>
            <div class="header-wrap-clone"></div>
        </header><!-- #header end -->


        <!-- Content
  ============================================= -->
        @yield('content')

        <!-- Footer
  ============================================= -->
        <footer id="footer" class="border-0" style="background-color: #F5F5F5;">


            <div class="line m-0"></div>

            <!-- Copyrights
   ============================================= -->
            <div id="copyrights" style="background-color: #FFF">
                <div class="container clearfix">

                    <div class="w-100 center m-0">
                        <span>Copyrights &copy; 2020 All Rights Reserved - El-Habib Plumbing Material and Services
                            Ltd.</span>
                    </div>

                </div>
            </div><!-- #copyrights end -->
        </footer><!-- #footer end -->

    </div><!-- #wrapper end -->

    <!-- Go To Top
 ============================================= -->
    <div id="gotoTop" class="icon-angle-up"></div>

    <!-- JavaScripts
 ============================================= -->
    <script src="/js/jquery.js"></script>
    <script src="/js/plugins.min.js"></script>

    <!-- TinyMCE Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js">
    </script>

    <!-- Bootstrap Select Plugin -->
    <script src="/js/components/bs-select.js"></script>

    <!-- Select Splitter Plugin -->
    <script src="/js/components/selectsplitter.js"></script>

    <!-- Footer Scripts
 ============================================= -->
    <script src="/js/functions.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="/toastr/toastr.min.js"></script>
    {!! Toastr::message() !!}

    @yield('js')
</body>

</html>
