@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Tours" :route="route('backend.tours.index')" />
        <x-backend::layout.breadcrumb-item text="Spots" :active="true"/>
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 ">{{ __('Spots') }}</h5>
            <div class="float-end">
                <a href="{{ route('backend.tours.spots.create', $tour) }}" class="btn btn-sm btn-outline-primary"><i
                        class="fal fa-plus"></i> {{ __('Add New') }}</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="mb-3">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">{{ __('Tour') }}</th>
                        <th scope="col">{{ __('Spot Name') }}</th>
                        <th scope="col">{{ __('Surfaces') }}</th>
                        <th scope="col">{{ __('Panos') }}</th>
                        <th scope="col">{{ __('XML') }}</th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    @forelse($spots as $spot)
                        <tr>
                            <td>{{ $tour->name }}</td>
                            <td>
                                <a href="{{ route('backend.spots.edit', $spot) }}">{{ $spot->friendly_name }}</a>
                            </td>
                            <td>{{ $spot->surfaces_count }} {{ __('Surfaces') }}</td>
                            <td>
                                <span class="text-{{ $spot->panoStatus()->color() }}">
                                    <i class="{{ $spot->panoStatus()->icon() }}"></i>
                                    <span>{{ $spot->panoStatus()->value }}</span>
                                </span>
                            </td>
                            <td>
                                <span class="text-{{ $spot->xmlStatus()->color() }}">
                                    <i class="{{ $spot->xmlStatus()->icon() }}"></i>
                                    <span>{{ $spot->xmlStatus()->value }}</span>
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('tours.show', [$tour, 'spot_id' => $spot->id]) }}" target="_blank">
                                    <i class="mdi mdi-rotate-3d-variant"></i>
                                </a>
                            </td>
                            <td>
                                <x-backend::dropdown.container permission="update|delete" :permission_params="$spot">

                                    <x-backend::dropdown.item
                                        permission="perform-admin-actions"
                                        onclick="Livewire.dispatch('modal.open', {component: 'modals.krpano-tools'})">
                                        <i class="fal fa-toolbox mr-1"></i> {{ __('Krpano Tools') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="perform-admin-actions"
                                        :route="route('backend.spot-configuration.show', $spot)" target="_blank">
                                        <i class="fal fa-code mr-1"></i> {{ __('Show Configuration') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="perform-admin-actions"
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
                                permission="delete" :permission_params="$spot"
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
