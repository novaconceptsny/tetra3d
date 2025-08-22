<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ config('app.name') }} - {{ __('Reset Password') }}</title>
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
                    <img width="200" src="{{ asset('backend/images/logo/logo_dark.png') }}" alt="logo-img"/>
                </div>
                <div class="fir-inner">
                    <h4 class="login d-flex align-items-center justify-content-center">
                        {{ __('Reset Password') }}
                    </h4>
                    <p class="text-center">
                        {{ __('Enter your email address to receive a password reset link') }}
                    </p>
                    
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form class="d-flex flex-column align-items-center" method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="form-group login-custum-form-group">
                            <label for="email">{{ __('Email Address') }}</label>
                            <input type="email" id="email" placeholder="Email"
                                   class="form-control @error('email') is-invalid @enderror" name="email"
                                   value="{{ old('email') }}" required autocomplete="email" autofocus
                            />
                            <x-error field="email"/>
                        </div>
                        <button type="submit" class="btn-login btn form-control">{{ __('Send Password Reset Link') }}</button>
                        
                        <div class="text-center mt-3">
                            <p class="m-0">
                                {{ __('Remember your password?') }} 
                                <a href="{{ route('login') }}" class="text-decoration-none fw-bold">
                                    {{ __('Back to Login') }}
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-8 sec-col">
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
