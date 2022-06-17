@extends('layouts.backend')

@section('content')
    <div class="card">
        <div class="card-header">
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
                    @forelse($project->spotConfigurations as $configuration)
                        <tr>
                            <td>{{ $configuration->spot->name }}</td>
                            <td>
                                <x-backend::dropdown.container permission="update|delete" :permission_params="$project">
                                    <x-backend::dropdown.item
                                        permission="update" :permission_params="$project"
                                        :route="route('spot-configurations.edit', $configuration)">
                                        <i class="fal fa-pen mr-1"></i> {{ __('Configure') }}
                                    </x-backend::dropdown.item>
                                </x-backend::dropdown.container>
                            </td>
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
