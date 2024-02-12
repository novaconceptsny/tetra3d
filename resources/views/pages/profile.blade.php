@extends('layouts.redesign')

@section('content')
    <section class="profile">
        <div class="container-fluid content-container">
            <div class="row profile-row justify-content-evenly">
                <div class="edit-col">
                    <div class="edit-profile">
                        <h4>{{ __('Edit profile') }}</h4>
                        <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row col-12">
                                <div class="col-xxl-6 form-group custum-form-group">
                                    <label class="form-label" for="first_name">{{ __('First Name') }}</label>
                                    <input
                                        id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror"
                                        name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                    />
                                    <x-error field="first_name"/>
                                </div>

                                <div class="col-xxl-6 form-group custum-form-group">
                                    <label class="form-label" for="last_name">{{ __('Last Name') }}</label>
                                    <input
                                        id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror"
                                        name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                    />
                                    <x-error field="last_name"/>
                                </div>

                                <div class="col-12 form-group custum-form-group">
                                    <label class="form-label" for="email">{{ __('Email') }}</label>
                                    <input
                                        id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email', $user->email) }}"
                                    />
                                    <x-error field="email"/>
                                </div>

                                <div class="profile-pic col-12 form-group custum-form-group">
                                    <label class="form-label">{{ __('Profile Picture') }}</label>
                                    <x-backend::media-attachment
                                        :show-filename="false"
                                        name="avatar" rules="max:102400"
                                        :media="$user?->getFirstMedia('avatar')"
                                    />
                                </div>

                                <div class="update col-12">
                                    <button class="btn" type="submit">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="pass-col">
                    <div class="update-pass">
                        <h4>{{ __('Update password') }}</h4>
                        <form class="row" action="{{ route('profile.password.update') }}" method="post">
                            @csrf
                            <div class="col-12 form-group custum-form-group">
                                <label class="form-label" for="password">{{ __('Enter Current Password') }}</label>
                                <input
                                    id="password" type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                />
                                <x-error field="password"/>
                            </div>

                            <div class="col-12 form-group custum-form-group">
                                <label class="form-label" for="new_password">{{ __('Enter New Password') }}</label>
                                <input
                                    id="new_password" type="password" name="new_password"
                                    class="form-control @error('new_password') is-invalid @enderror"
                                />
                                <x-error field="new_password"/>
                            </div>

                            <div class="col-12 form-group custum-form-group">
                                <label class="form-label" for="new_password_confirmation">{{ __('Confirm New Password') }}</label>
                                <input
                                    id="new_password_confirmation" type="password" name="new_password_confirmation"
                                    class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                />
                                <x-error field="new_password_confirmation"/>
                            </div>

                            <div class="update col-12 form-group">
                                <button class="btn" type="submit">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('backend/css/media-library.css') }}">
@endsection
