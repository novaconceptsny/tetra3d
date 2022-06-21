@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Companies" :route="route('backend.companies.index')"/>
        <x-backend::layout.breadcrumb-item text="Form" :active="true"/>
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    @php($company = $company ?? null)
    @php($edit_mode = (bool)$company)
    @php($heading = $heading ?? ( $company ? __('Edit Company') : __('Add New Company') ))

    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">{{ $heading }}</h5>
        </div>
        <div class="card-body">

            <form class="row g-3" action="{{ $route }}" method="POST">
                @csrf
                @method($method ?? 'POST')

                <x-backend::inputs.text name="name" value="{{ $company ? $company->name : '' }}"/>

                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit">
                        {{ $submit_text ?? ( $company ? __('Update') : __('Create') ) }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

