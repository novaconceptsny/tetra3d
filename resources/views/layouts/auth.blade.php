<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title', 'Login') | {{ config('app.name') }}</title>
    <!--Required meta tag-->
    <meta charset="UTF-8"/>
    <meta name="description" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta property="og:title" content=""/>
    <meta property="og:type" content=""/>
    <meta property="og:url" content=""/>
    <meta property="og:image" content=""/>
    <!--Fontawesome cdn	-->

    <!-- CSS File -->
    <link rel="stylesheet" href="{{ asset('css/normalize.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap"
        rel="stylesheet"
    />

    <link rel="stylesheet" href="{{ asset('css/style.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}"/>
</head>

<body>
<!--Starts Header Area-->
<header id="header" class="main__header">
    <div class="container-fluid">
        <nav id="navbar" class="main__navbar">
            <a href="{{ route('dashboard') }}" class="brand">
                <img
                    src="{{ asset('images/tetra__logo.png') }}"
                    alt="brand logo"
                    width="48"
                    height="auto"
                    class="logo"
                />
            </a>
            @if (Route::has('register'))
                <a class="nav__link" href="{{ route('register') }}">{{ __('Register') }}</a>
            @endif
        </nav>
    </div>
</header>
<!--Ends Header Area-->

<!--Starts Main Area-->
<main id="site__body">
    <div class="container">
        <div class="signin__screen">
            @yield('content')
        </div>
    </div>
</main>
<!--Ends Main Area-->

<!-- JS File -->
<script src="{{ asset('js/modernizr-3.11.2.min.js') }}"></script>
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
</body>
</html>
