@extends('layouts.auth')

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
