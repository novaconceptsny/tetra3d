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

    <div class="container-fluid">
        <div class="row login-row">
            <div class="inner-div col-lg-4">
                <div class="logo">
{{--                    <img src="{{ asset('logo.png') }}" alt="logo-img"/>--}}
                    <svg width="38" height="41" viewBox="0 0 38 41" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <rect width="38" height="41" fill="url(#pattern0)"/>
                        <defs>
                            <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <use xlink:href="#image0_3275_7167" transform="scale(0.0263158 0.0243902)"/>
                            </pattern>
                            <image id="image0_3275_7167" width="38" height="41" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAApCAYAAABZa1t7AAAEhklEQVRYCe2YSyhtURjHjzd5k0cUJa8MhCIZKEISYUBIikQSAyIGTDCRiTBTDDCQDOQxoQyUQkSSgTzyyPuR9/O7/Vet3dnbPvu8tntvt7vqtM4+Z69v/fb3fet7bA39pUPzl3LRvw32/PxMS0tLVF9fT/Pz86oYwSyNPT4+0traGjU1NZGbmxuFhobS7OzsnwN7f3+n7e1t6urqoqioKLKwsCCNRvNnwa6vr2lwcJDS0tLI1dWVAQEKn7CwsN+vsc/PT1pZWaGsrCzy9fUlS0tLEZS9vT0VFxfT4eHh7zHl19cXXVxcUE1NDTk6On4Dghn9/f1pfHycnp6eCPerMXQ6PzR0eXlJQ0NDzHe4H3GzWVtbk5+fHzU0NNDLy4saLCIZsmD39/c0NTVF+fn5ZGdnJzIZAAFUVFREi4uL9PHxIRKo1oUsWGNjI3l7e4uAoCkbGxvKzs5mWry9vVWLgclB2Lm7uxNkyoLBTNxkfI6Ojqb+/n7a3d0VFku/wL+M9TH4b19fHyUkJNDm5qYgUi+Yp6cntbW10dbWlqLZ9vf3qaWlhZaXlwXhSl/wAAsLC5SYmEgeHh4sFq6urgpL9ILBdEpaQrDt7OxkwsPDww2KYw8PD5STk0MODg5CcIZljAKDgL29PeFJ8AUn9ubmhh2Q2NhYQbhSSsIDnJ6eUnd3Nzs83EW0Z7PAYIKdnR2qrKwUTMCFy4Hh/vPzcxodHaX09PRvp5yvxWwWGLQ1MjLCgq22UHyXgkFLMzMzVFFRQT4+PoJmpev4tVlgiFsIukhBXCCfORi0tLGxQXV1dRQSEkJWVlaie5HOkpOTKT4+XvT7j4LBZPCjyMhIWfjAwEDq6emhg4MDKisr+3kwaAGxDkne1tZWtCE0ihNYUlLCTjg0CjOXl5eL7lNVY0hZwcHB1N7eTldXV+yjrTFnZ2cWq8bGxkQ59cfAnJycGFB1dTWtr6+Lwgn3MZTaHR0ddHx8LPofFwArLS1VT2M4ldPT08wMExMThGCpaygleIAVFhaqBwZtwGQnJycs0OqC0vc7wAoKCtQD07ehof+bDaYvVxoKIr3v7e2N8vLyTNeYodWFdGNd1/A7pLSBgQHCAeLBGbNR4YIvjImJYfWYNKHrApD+Dt88Ojqi4eFhiouLEwHxPfSCKVWwubm5TLh2tSmFkF6jSZmcnGSluru7+zcoLy8vVhSg+uBDth5TqvkR4VHzo1XDawGED6WBAhIRPygo6FuHhUo5JSWFQaPx0R6yYLgBG+rrktC2NTc3ExxZOl5fX6m1tZUCAgJIrlRHK4hiACWR3MPpBOMbwTf09ZXYHKaCyfBBqYO8KW35oG0AVVVVsUKT7yE36wXji/BUSp04knRmZiYzm4uLi8iPAIjTnZGRQXNzcywdcbm6ZoPBuACldxf8dGnPSPJJSUnU29tLZ2dnBndRRoMBEFFb7m2PNhC0FBERwaoOtGVYY8wwCYxvIH0/xsHQjtXW1rJWDj5nyjALjG/I3yiio0pNTWUtHKoOHBxThypgpm6utO4/mJJ25P77BZFFwELkd0tjAAAAAElFTkSuQmCC"/>
                        </defs>
                    </svg>

                    <span>Tetra</span>
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
                            <input type="email" id="email" placeholder="Email"
                                   class="form-control @error('email') is-invalid @enderror" name="email"
                                   value="{{ old('email') }}" required autocomplete="email" autofocus
                            />
                            <x-error field="email"/>
                        </div>
                        <div class="form-group login-custum-form-group">
                            <label for="password">{{ __('Password') }}</label>
                            <input
                                placeholder="Password"
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
            <div class="col-lg-8 sec-col">
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
                <img src="{{asset('redesign/images/login-hero-banner.gif')}}" alt="login-page-img">
            </div>
        </div>
    </div>
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
