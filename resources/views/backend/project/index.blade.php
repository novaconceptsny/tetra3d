@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Projects" :active="true"/>
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="float-end">
                @can('create', \App\Models\Project::class)
                <a href="{{ route('backend.projects.create') }}" class="btn btn-sm btn-outline-primary"><i
                        class="fal fa-plus"></i> {{ __('Add New') }}</a>
                @endcan
            </div>
            <h5 class="mb-0 ">{{ __('Projects') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="mb-3">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('Company') }}</th>
                        <th scope="col">{{ __('Tours') }}</th>
                        <th scope="col">{{ __('Contributors') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    @forelse($projects as $project)
                        <tr>
                            <td>{{ $project->name }}</td>
                            <td>{{ $project->company->name }}</td>
                            <td>
                                @foreach($project->tours as $tour)
                                    <a href="{{ route('tours.show', [$tour, 'project_id' => $project->id]) }}" target="_blank">
                                        <span class="badge badge-info-lighten px-2 py-1">{{ $tour->name }}</span>
                                    </a>
                                @endforeach
                            </td>
                            <td>{{ $project->contributors_count }} Contributors</td>
                            <td>
                                <x-backend::dropdown.container permission="update|delete" :permission_params="$project">

                                    <x-backend::dropdown.item
                                        permission="update" :permission_params="$project"
                                        :route="route('backend.projects.edit', $project)">
                                        <i class="fal fa-pen mr-1"></i> {{ __('Edit') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="delete" :permission_params="$project"
                                        class="text-danger" data-bs-toggle="modal"
                                        data-bs-target="#confirm_project_{{ $project->id }}">
                                        <i class="fa fa-trash mr-1"></i> {{ __('Delete') }}
                                    </x-backend::dropdown.item>
                                </x-backend::dropdown.container>
                            </td>
                            <x-backend::modals.confirm
                                permission="delete" :permission_params="$project"
                                :route="route('backend.projects.destroy', $project)"
                                :model="$project" :button="false"
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
