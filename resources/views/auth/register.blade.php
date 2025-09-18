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
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
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
                            <div class="password-input-wrapper">
                                <input
                                    placeholder="Password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password" id="password" type="password"
                                    required autocomplete="new-password"
                                >
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password-eye"></i>
                                </button>
                            </div>
                            <x-error field="password"/>
                        </div>
                        <div class="form-group login-custum-form-group">
                            <label for="password-confirm">{{ __('Confirm Password') }}</label>
                            <div class="password-input-wrapper">
                                <input
                                    placeholder="Confirm Password"
                                    class="form-control"
                                    name="password_confirmation" id="password-confirm" type="password"
                                    required autocomplete="new-password"
                                >
                                <button type="button" class="password-toggle" onclick="togglePassword('password-confirm')">
                                    <i class="fas fa-eye" id="password-confirm-eye"></i>
                                </button>
                            </div>
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

<style>
.password-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #999999;
    cursor: pointer;
    padding: 8px;
    z-index: 10;
    transition: all 0.3s ease;
    border-radius: 6px;
    font-size: 16px;
    min-width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.password-toggle:hover {
    color: #099F9A;
    background-color: rgba(9, 159, 154, 0.1);
}

.password-toggle:focus {
    outline: none;
    color: #099F9A;
    background-color: rgba(9, 159, 154, 0.1);
}

.password-toggle:active {
    transform: translateY(-50%) scale(0.95);
}

.password-input-wrapper .form-control {
    padding-right: 45px;
    border: 1px solid #D3D3D3;
    border-radius: 12px;
    background-color: #F5F5F5;
    color: #999999;
    transition: all 0.3s ease;
}

.password-input-wrapper .form-control:focus {
    border-color: #099F9A;
    background-color: #ffffff;
    color: #000000;
    box-shadow: 0 0 0 2px rgba(9, 159, 154, 0.2);
}

.password-input-wrapper .form-control::placeholder {
    color: #999999;
    font-weight: var(--light-font);
}

.password-input-wrapper .form-control:not(:placeholder-shown) {
    color: #000000;
}

/* Ensure the icon is visible */
.password-toggle i {
    display: inline-block;
    width: 16px;
    height: 16px;
    text-align: center;
    line-height: 1;
}
</style>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const eyeIcon = document.getElementById(inputId + '-eye');
    
    if (input.type === 'password') {
        input.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>
