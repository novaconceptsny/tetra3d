<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ config('app.name') }} - Email Verification</title>
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
                        {{ __('Email Verification') }}
                    </h4>
                    <p class="text-center">
                        {{ __('Please enter the verification code sent to your email') }}
                    </p>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="d-flex flex-column align-items-center" method="POST" action="{{ route('verification.verify') }}">
                        @csrf
                        <div class="form-group login-custum-form-group">
                            <label for="email">{{ __('Email Address') }}</label>
                            <input type="email" id="email" placeholder="Email"
                                   class="form-control @error('email') is-invalid @enderror" name="email"
                                   value="{{ session('verification_email') ?? old('email') }}" required autocomplete="email" autofocus
                            />
                        </div>
                        <div class="form-group login-custum-form-group">
                            <label for="verification_code">{{ __('Verification Code') }}</label>
                            <input type="text" id="verification_code" placeholder="Enter 6-digit code"
                                   class="form-control @error('verification_code') is-invalid @enderror" name="verification_code"
                                   value="{{ old('verification_code') }}" required maxlength="6" style="text-transform: uppercase;"
                            />
                        </div>
                        <button type="submit" class="btn-login btn form-control">Verify Email</button>
                    </form>

                    <div class="mt-3 text-center">
                        <p>Didn't receive the code? <small class="text-muted">(Check your spam folder)</small></p>
                        <form method="POST" action="{{ route('verification.resend') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('verification_email') ?? old('email') }}">
                            <button type="submit" class="btn btn-link p-0">Resend Code</button>
                        </form>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="{{ route('login') }}" class="text-decoration-none">Back to Login</a>
                    </div>
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
// Auto-uppercase the verification code input
document.getElementById('verification_code').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>
</body>
</html> 