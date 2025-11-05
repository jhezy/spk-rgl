<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700&display=swap" rel="stylesheet">

    <!-- Bootstrap + Vite -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8fafc;
        }

        .navbar {
            background-color: #ffffff !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            font-weight: 700;
            color: #0d6efd !important;
            letter-spacing: -0.3px;
        }

        .nav-link {
            color: #555 !important;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-link:hover {
            color: #0d6efd !important;
        }

        .dropdown-menu {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        main {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        /* Responsif */
        @media (max-width: 767px) {
            .navbar-brand {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>
    <div id="app">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-md navbar-light shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="bi bi-graph-up"></i> SPK RLG
                </a>

                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse mt-2 mt-md-0" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('penjualan.index') }}">Penjualan</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('permintaan.index') }}">Permintaan</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('produksi.index') }}">Produksi</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('forecast.index') }}">Forecasting</a></li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                        @if (Route::has('login'))
                        <!-- <li class="nav-item">
                            <a class="nav-link fw-semibold text-primary" href="{{ route('login') }}">Login</a>
                        </li> -->
                        @endif
                        @if (Route::has('register'))
                        <!-- <li class="nav-item">
                            <a class="nav-link fw-semibold text-primary" href="{{ route('register') }}">Register</a>
                        </li> -->
                        @endif
                        @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center gap-1"
                                href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false" v-pre>
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end py-2" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Optional: Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @stack('scripts')
</body>

</html>