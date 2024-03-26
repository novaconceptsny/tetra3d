@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Collections" :active="true"/>
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="float-end">
                @can('create', \App\Models\ArtworkCollection::class)
                    <a href="{{ route('backend.artwork-collections.create') }}" class="btn btn-sm btn-outline-primary"><i
                            class="fal fa-plus"></i> {{ __('Add New') }}</a>
                @endcan
            </div>
            <h5 class="mb-0 ">{{ __('Collections') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="mb-3">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">{{ __('Company') }}</th>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('Artworks') }}</th>
                        <th scope="col">{{ __('Sculptures') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    @forelse($collections as $collection)
                        <tr>
                            <td>{{ $collection->company->name }}</td>
                            <td>{{ $collection->name }}</td>
                            <td>{{ $collection->artworks_count }}</td>
                            <td>{{ $collection->sculpture_models_count }}</td>
                            <td>
                                <x-backend::dropdown.container permission="update|delete" :permission_params="$collection">
                                    <x-backend::dropdown.item
                                        permission="update" :permission_params="$collection"
                                        :route="route('backend.artwork-collections.edit', $collection)">
                                        <i class="fal fa-pen mr-1"></i> {{ __('Edit') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="delete" :permission_params="$collection"
                                        class="text-danger" data-bs-toggle="modal"
                                        data-bs-target="#confirm_artworkCollection_{{ $collection->id }}">
                                        <i class="fa fa-trash mr-1"></i> {{ __('Delete') }}
                                    </x-backend::dropdown.item>
                                </x-backend::dropdown.container>
                            </td>
                            <x-backend::modals.confirm
                                permission="delete" :permission_params="$collection"
                                :route="route('backend.artwork-collections.destroy', $collection)"
                                :model="$collection" :button="false"
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

