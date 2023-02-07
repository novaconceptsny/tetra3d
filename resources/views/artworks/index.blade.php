@extends('layouts.redesign')

@section('content')
    <section class="collection">
        <div class="main-intro container-fluid">
            @include('include.common.greetings')
            <div class="table-responsive">
                <livewire:datatables.artwork-datatable :frontend="true"/>
            </div>
        </div>
    </section>
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
