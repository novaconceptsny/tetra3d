@extends('layouts.master')

@section('content')
    <div class="dashboard gallery mini">
        <div class="row justify-content-center">
            <div class="col-md-8 col-sm-12">
                <div class="card shadow-none">
                    <div class="card-header">
                        <h5 class="m-0">Edit Profile</h5>
                    </div>
                    <div class="card-body ">
                        <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-6">
                                    <label for="first_name">{{ __('First Name') }}</label>
                                    <input
                                        id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror"
                                        name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                    />
                                    <x-error field="first_name"/>
                                </div>
                                <div class="col-6">
                                    <label for="first_name">{{ __('Last Name') }}</label>
                                    <input
                                        id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror"
                                        name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                    />
                                    <x-error field="last_name"/>
                                </div>
                                <div class="col-4 mt-4">
                                    <h6>{{ __('Profile Picture') }}</h6>
                                    <x-backend::media-attachment
                                        :show-filename="false"
                                        name="avatar" rules="max:102400"
                                        :media="$user?->getFirstMedia('avatar')"
                                    />
                                </div>

                                <div class="col-12 text-end">
                                    <button class="btn btn-success" type="submit">
                                        {{ __('Update') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card shadow-none mt-5">
                    <div class="card-header">
                        <h5 class="m-0">Update Password</h5>
                    </div>
                    <div class="card-body ">
                        <form action="{{ route('profile.password.update') }}" method="post">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="password">{{ __('Enter Current Password') }}</label>
                                    <input
                                        id="password" type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                    />
                                    <x-error field="password"/>
                                </div>
                                <div class="col-6">
                                    <label for="new_password">{{ __('Enter New Password') }}</label>
                                    <input
                                        id="new_password" type="password" name="new_password"
                                        class="form-control @error('new_password') is-invalid @enderror"
                                    />
                                    <x-error field="new_password"/>
                                </div>
                                <div class="col-6">
                                    <label for="new_password_confirmation">{{ __('Confirm New Password') }}</label>
                                    <input
                                        id="new_password_confirmation" type="password" name="new_password_confirmation"
                                        class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                    />
                                    <x-error field="new_password_confirmation"/>
                                </div>

                                <div class="col-12 text-end">
                                    <button class="btn btn-success" type="submit">
                                        {{ __('Update') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('backend/css/media-library.css') }}">
@endsection
