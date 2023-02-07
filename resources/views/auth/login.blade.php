@section('content')
    <div class="row">
        <div class="col-sm-2 col-md-3 col-lg-3 col-xl-4"></div>
        <div class="col-sm-8 col-md-6 col-lg-6 col-xl-4">
            <h1 class="primary__title text-center">{{ __('Sign In') }}</h1>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <input id="email" type="email"
                                   class="form-control @error('email') is-invalid @enderror" name="email"
                                   value="{{ old('email') }}" required autocomplete="email" autofocus>
                            <x-error field="email"/>
                        </div>

                        <div class="mb-5">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input
                                class="font-primary form-control @error('password') is-invalid @enderror"
                                name="password" id="password" type="password"
                                required autocomplete="current-password"
                            >
                            <x-error field="password"/>
                        </div>
                        <div class="d-grid mb-3">
                            <button class="btn" type="submit">{{ __('Login') }}</button>
                        </div>
                        @if (Route::has('password.request'))
                            <div class="mb-3 text-center">
                                <a class="btn btn-link forgot__pass" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ config('app.name') }}</title>
    <!-- bootstrap css link  -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
        crossorigin="anonymous"
    />
    <!-- own css file  -->
    <link rel="stylesheet" href="{{ asset('redesign/css/style.css') }}"/>
</head>
<body>

<main class="login">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-6 fir-col">
                <div class="fir-inner">
                    <div class="heading">
                        <img src="{{ asset('redesign/images/logo.png') }}" alt="logo-img"/>
                        <h3>{{ config('app.name') }}</h3>
                    </div>
                    <h4 class="login d-flex align-items-center justify-content-center">
                        {{ __('Login') }}
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
                                <input class="form-control" type="checkbox" id="checkbox" class="m-0 p-0"/>
                                <label for="checkbox" class="m-0 p-0">Remember me</label>
                            </div>
                        </div>
                        <button type="submit" class="btn-login btn form-control">Login</button>
                    </form>
                </div>
            </div>
            <div class="col-xl-6 sec-col">
                <div class="ell-img">
                    <img src="{{ asset('redesign/images/Group 4738.png') }}" alt="sec-bg"/>
                </div>
                <div class="sec-inner">
                    <div class="ellipse">
                        <img src="{{ asset('redesign/images/logo.png') }}" alt="ellipse-logo"/>
                    </div>
                    <h1 class="text-white">{{ __('Welcome To Tetra') }}</h1>
                    <p class="text-white">
                        {{ __('Tetra3D is a 3D renderer written in Go. It is largely
                        implemented in software, but uses Ebiten to render the triangles
                        and perform depth testing through hardware.') }}
                    </p>
                </div>
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
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
    integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13"
    crossorigin="anonymous"
></script>
</body>
</html>
