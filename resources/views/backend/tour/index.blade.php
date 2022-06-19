@extends('layouts.backend')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="float-end">
                <a href="{{ route('backend.tours.create') }}" class="btn btn-sm btn-outline-primary"><i
                        class="fal fa-plus"></i> {{ __('Add New') }}</a>
            </div>
            <h5 class="mb-0 ">{{ __('Tours') }}</h5>
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
                    @forelse($tours as $tour)
                        <tr>
                            <td>{{ $tour->name }}</td>
                            <td>
                                <x-backend::dropdown.container permission="update|delete" :permission_params="$tour">
                                    <x-backend::dropdown.item
                                        permission="update" :permission_params="$tour"
                                        :route="route('backend.tours.surfaces.index', $tour)">
                                        <i class="fal fa-rectangle-landscape mr-1"></i> {{ __('Surfaces') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="update" :permission_params="$tour"
                                        :route="route('backend.tours.spots.index', $tour)">
                                        <i class="fal fa-circle-notch mr-1"></i> {{ __('Spots') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="update" :permission_params="$tour"
                                        :route="route('backend.tours.edit', $tour)">
                                        <i class="fal fa-pen mr-1"></i> {{ __('Edit') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="delete" :permission_params="$tour"
                                        class="text-danger" data-bs-toggle="modal"
                                        data-bs-target="#confirm_tour_{{ $tour->id }}">
                                        <i class="fa fa-trash mr-1"></i> {{ __('Delete') }}
                                    </x-backend::dropdown.item>
                                </x-backend::dropdown.container>
                            </td>
                            <x-backend::modals.confirm
                                permission="edit" :permission_params="$tour"
                                :route="route('backend.tours.destroy', $tour)"
                                :model="$tour" :button="false"
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
