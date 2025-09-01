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
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
    @include('backend.includes.partial.favicon')
    
    <style>
        .password-input-wrapper {
            position: relative;
        }
        
        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 0;
            font-size: 16px;
            z-index: 10;
        }
        
        .password-toggle-btn:hover {
            color: #495057;
        }
        
        .password-toggle-btn:focus {
            outline: none;
            color: #007bff;
        }
        
        .password-input-wrapper .form-control {
            padding-right: 40px;
        }
    </style>
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
                        {{ __('Enter your new password') }}
                    </p>
                    
                    <form class="d-flex flex-column align-items-center" method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        <div class="form-group login-custum-form-group">
                            <label for="email">{{ __('Email Address') }}</label>
                            <input type="email" id="email" placeholder="Email"
                                   class="form-control @error('email') is-invalid @enderror" name="email"
                                   value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                            />
                            <x-error field="email"/>
                        </div>
                        
                        <div class="form-group login-custum-form-group">
                            <label for="password">{{ __('New Password') }}</label>
                            <div class="password-input-wrapper position-relative">
                                <input type="password" id="password" placeholder="New Password"
                                       class="form-control @error('password') is-invalid @enderror" name="password"
                                       required autocomplete="new-password"
                                />
                                <button type="button" class="password-toggle-btn position-absolute" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password-eye"></i>
                                </button>
                            </div>
                            <x-error field="password"/>
                        </div>
                        
                        <div class="form-group login-custum-form-group">
                            <label for="password-confirm">{{ __('Confirm New Password') }}</label>
                            <div class="password-input-wrapper position-relative">
                                <input type="password" id="password-confirm" placeholder="Confirm New Password"
                                       class="form-control" name="password_confirmation"
                                       required autocomplete="new-password"
                                />
                                <button type="button" class="password-toggle-btn position-absolute" onclick="togglePassword('password-confirm')">
                                    <i class="fas fa-eye" id="password-confirm-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-login btn form-control">{{ __('Reset Password') }}</button>
                        
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
