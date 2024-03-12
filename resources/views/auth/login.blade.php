<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ config('app.name') }}</title>
    <!-- bootstrap css link  -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <!-- own css file  -->
    <link rel="stylesheet" href="{{ asset('redesign/css/style.css') }}"/>
    @include('backend.includes.partial.favicon')
</head>
<body class="login_body">

<main class="login">
    <div class="inner-div col-lg-6">
            <div class="heading">
                <img src="{{ asset('logo.png') }}" alt="logo-img"/>
            </div>
        <div class="fir-inner">
            <h4 class="login d-flex align-items-center justify-content-center">
                {{ __('User Login') }}
            </h4>
            <p class="text-center">
                {{ __('Sign in to access your tours') }}
            </p>
            <form class="d-flex flex-column align-items-center" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group login-custum-form-group">
                    <label for="email">{{ __('Email') }}</label>
                    <input type="email" id="email"
                           class="form-control @error('email') is-invalid @enderror" name="email"
                           value="{{ old('email') }}" required autocomplete="email" autofocus
                    />
                    <x-error field="email"/>
                </div>
                <div class="form-group login-custum-form-group">
                    <label for="password">{{ __('Password') }}</label>
                    <input
                        class="form-control @error('password') is-invalid @enderror"
                        name="password" id="password" type="password"
                        required autocomplete="current-password"
                    >
                </div>
                <div class="check-main w-100">
                    <div class="checkbox">
                        <input class=" m-0 p-0" type="checkbox" id="checkbox" />
                        <label for="checkbox" class="m-0 p-0">Remember me</label>
                    </div>
                </div>
                <button type="submit" class="btn-login btn form-control">Login</button>
            </form>
        </div>
    </div>
{{--    <div class="container-fluid">--}}
{{--        <div class="row">--}}
{{--            --}}
{{--            <div class="col-xl-6 sec-col">--}}
{{--                <div class="ell-img">--}}
{{--                    <img src="{{ asset('redesign/images/Group 4738.png') }}" alt="sec-bg"/>--}}
{{--                </div>--}}
{{--                <div class="sec-inner">--}}
{{--                    <div class="ellipse">--}}
{{--                        <img src="{{ asset('redesign/images/logo.png') }}" alt="ellipse-logo"/>--}}
{{--                    </div>--}}
{{--                    <h1 class="text-white">{{ __('Welcome To Tetra') }}</h1>--}}
{{--                    <p class="text-white">--}}
{{--                        {{ __('Tetra3D is a 3D renderer written in Go. It is largely--}}
{{--                        implemented in software, but uses Ebiten to render the triangles--}}
{{--                        and perform depth testing through hardware.') }}--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</main>

<!-- bootstrap script links -->
<script
    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
    integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB"
    crossorigin="anonymous"
></script>

<script src="{{ asset('js/bootstrap.min.js') }}"></script>
</body>
</html>
