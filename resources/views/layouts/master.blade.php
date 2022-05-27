<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
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
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}"/>
</head>

<body>
<!--Starts Header Area-->
<!--Starts Header Area-->
@if(request()->routeIs('dashboard'))
    @include('include.common.header')
@else
@include('include.common.page-header')
@endif
<!--Ends Header Area-->

<!--Starts Main Area-->
<main id="site__body">
    <section class="universal__wrapper">
        @include('include.common.sidebar')
        @yield('content')
    </section>
</main>
<!--Ends Main Area-->

<!-- JS File -->
<script src="{{ asset('js/modernizr-3.11.2.min.js') }}"></script>
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>

<script>
    $(function (){
        let url = window.location.href;

        $('.sidebar .navbar__nav li.nav__item').each(function (){
            let link = $(this).find("a:first-child");

            if(link.attr('href').replace(/\/+$/, '') === url.replace(/\/+$/, '')){
                $(this).addClass('active');
            }
        })
    })
</script>

@yield('scripts')
</body>
</html>
