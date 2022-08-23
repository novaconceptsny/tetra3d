@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Companies" :active="true"/>
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="float-end">
                <a href="{{ route('backend.companies.create') }}" class="btn btn-sm btn-outline-primary"><i
                        class="fal fa-plus"></i> {{ __('Add New') }}</a>
            </div>
            <h5 class="mb-0 ">{{ __('Companies') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="mb-3">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">{{ __('Name') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    @forelse($companies as $company)
                        <tr>
                            <td>{{ $company->name }}</td>
                            <td>
                                <x-backend::dropdown.container permission="update|delete" :permission_params="$company">
                                    @if($company->admin)
                                        <x-backend::switch-to-user :user="$company->admin"/>
                                    @endif

                                    <x-backend::dropdown.item
                                        permission="accessCollector" :permission_params="$company"
                                        onclick="window.livewire.emit('showModal', 'modals.collector-tools', {{ $company->id }})">
                                        <i class="fal fa-power-off mr-1 text-info"></i> {{ __('Collector Tools') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="update" :permission_params="$company"
                                        :route="route('backend.companies.edit', $company)">
                                        <i class="fal fa-pen mr-1"></i> {{ __('Edit') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="delete" :permission_params="$company"
                                        class="text-danger" data-bs-toggle="modal"
                                        data-bs-target="#confirm_company_{{ $company->id }}">
                                        <i class="fa fa-trash mr-1"></i> {{ __('Delete') }}
                                    </x-backend::dropdown.item>
                                </x-backend::dropdown.container>
                            </td>
                            <x-backend::modals.confirm
                                permission="edit" :permission_params="$company"
                                :route="route('backend.companies.destroy', $company)"
                                :model="$company" :button="false"
                            />
                        </tr>
                    @empty
                        <x-backend::layout.tr-no-record/>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-0"></div>
    </div>
@endsection
