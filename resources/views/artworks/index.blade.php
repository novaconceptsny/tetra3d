@extends('layouts.master')

@section('content')
    <div class="dashboard gallery mini">
        <div class="inner__table table-responsive p-2 pb-4">
            <livewire:datatables.artwork-datatable/>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .inner__table .card-body, .card-footer{
            padding: 1rem 1rem !important;
        }

        .inner__table .dt-row{
            font-family: "Roboto", Arial, Helvetica, sans-serif;
        }

        .inner__table .dropdown-toggle::after{
            content: unset !important;
        }
    </style>
@endsection
