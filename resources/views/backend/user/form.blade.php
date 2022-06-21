@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Users" :route="route('backend.users.index')"/>
        <x-backend::layout.breadcrumb-item text="Form" :active="true"/>
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    @php($user = $user ?? null)
    @php($edit_mode = (bool)$user)
    @php($heading = $heading ?? ( $user ? __('Edit User') : __('Add New User') ))

    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">{{ $heading }}</h5>
        </div>
        <div class="card-body">

            <form class="row g-3" action="{{ $route }}" method="POST">
                @csrf
                @method($method ?? 'POST')

                <x-backend::inputs.company/>
                <x-backend::inputs.text col="col-6" name="first_name" value="{{ $user?->first_name }}"/>
                <x-backend::inputs.text col="col-6" name="last_name" value="{{ $user?->last_name }}"/>
                <x-backend::inputs.text col="col-6" name="email" value="{{ $user?->email }}"/>
                <x-backend::inputs.text col="col-6" name="password" />

                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit">
                        {{ $submit_text ?? ( $user ? __('Update') : __('Create') ) }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

