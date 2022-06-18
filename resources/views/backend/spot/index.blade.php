@extends('layouts.backend')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="float-end">
                <a href="{{ route('backend.tours.spots.create', $tour) }}" class="btn btn-sm btn-outline-primary"><i
                        class="fal fa-plus"></i> {{ __('Add New') }}</a>
            </div>
            <h5 class="mb-0 ">{{ __('Spots') }}</h5>
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
                    @forelse($spots as $spot)
                        <tr>
                            <td>{{ $spot->name }}</td>
                            <td>
                                <x-backend::dropdown.container permission="update|delete" :permission_params="$spot">

                                    <x-backend::dropdown.item
                                        permission="update" :permission_params="$spot"
                                        :route="route('backend.spot-configuration.show', $spot)">
                                        <i class="fal fa-code mr-1"></i> {{ __('Show Configuration') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="update" :permission_params="$spot"
                                        :route="route('backend.spot-configuration.edit', $spot)">
                                        <i class="fal fa-cog mr-1"></i> {{ __('Configure Spot') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="update" :permission_params="$spot"
                                        :route="route('backend.spots.edit', $spot)">
                                        <i class="fal fa-pen mr-1"></i> {{ __('Edit') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="delete" :permission_params="$spot"
                                        class="text-danger" data-bs-toggle="modal"
                                        data-bs-target="#confirm_spot_{{ $spot->id }}">
                                        <i class="fa fa-trash mr-1"></i> {{ __('Delete') }}
                                    </x-backend::dropdown.item>
                                </x-backend::dropdown.container>
                            </td>
                            <x-backend::modals.confirm
                                permission="edit" :permission_params="$spot"
                                :route="route('backend.spots.destroy', $spot)"
                                :model="$spot" :button="false"
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
