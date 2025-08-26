<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                    <img width="200" src="{{ asset('backend/images/logo/logo_dark.png') }}" alt="logo-img"/>
                </div>
                <div class="fir-inner">
                    <h4 class="login d-flex align-items-center justify-content-center">
                        {{ __('Register') }}
                    </h4>
                    <p class="text-center">
                        {{ __('Create your account to get started') }}
                    </p>
                    <form class="d-flex flex-column align-items-center" method="POST" action="{{ route('register') }}">
                        @csrf
                        @if(request('redirect'))
                            <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                        @endif
                        <div class="form-group login-custum-form-group">
                            <label for="first_name">{{ __('First Name') }}</label>
                            <input type="text" id="first_name" placeholder="First Name"
                                   class="form-control @error('first_name') is-invalid @enderror" name="first_name"
                                   value="{{ old('first_name') }}" required autocomplete="given-name" autofocus
                            />
                            <x-error field="first_name"/>
                        </div>
                        <div class="form-group login-custum-form-group">
                            <label for="last_name">{{ __('Last Name') }}</label>
                            <input type="text" id="last_name" placeholder="Last Name"
                                   class="form-control @error('last_name') is-invalid @enderror" name="last_name"
                                   value="{{ old('last_name') }}" required autocomplete="family-name"
                            />
                            <x-error field="last_name"/>
                        </div>
                        <!-- Hidden company field with default value "my workspace" -->
                        <input type="hidden" name="company_name" value="My Workspace">
                        <input type="hidden" name="company_id" value="">
                        <div class="form-group login-custum-form-group">
                            <label for="email">{{ __('Email') }}</label>
                            <input type="email" id="email" placeholder="Email"
                                   class="form-control @error('email') is-invalid @enderror" name="email"
                                   value="{{ old('email') }}" required autocomplete="email"
                            />
                            <x-error field="email"/>
                        </div>
                        <div class="form-group login-custum-form-group">
                            <label for="password">{{ __('Password') }}</label>
                            <input
                                placeholder="Password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password" id="password" type="password"
                                required autocomplete="new-password"
                            >
                            <x-error field="password"/>
                        </div>
                        <div class="form-group login-custum-form-group">
                            <label for="password-confirm">{{ __('Confirm Password') }}</label>
                            <input
                                placeholder="Confirm Password"
                                class="form-control"
                                name="password_confirmation" id="password-confirm" type="password"
                                required autocomplete="new-password"
                            >
                        </div>
                        <button type="submit" class="btn-login btn form-control">Register</button>
                        
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                By registering, you agree to receive a verification code via email to complete your account setup.
                            </small>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <p class="m-0">
                                {{ __('Already have an account?') }} 
                                <a href="{{ route('login') }}" class="text-decoration-none fw-bold">
                                    {{ __('Sign in here') }}
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
