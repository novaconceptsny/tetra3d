@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Users" :active="true"/>
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="float-end">
                @can('create', \App\Models\User::class)
                    <a href="{{ route('backend.users.create') }}" class="btn btn-sm btn-outline-primary"><i
                            class="fal fa-plus"></i> {{ __('Add New') }}</a>
                @endif
            </div>
            <h5 class="mb-0 ">{{ __('Users') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="mb-3">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('Email') }}</th>
                        <th scope="col">{{ __('Role') }}</th>
                        <th scope="col">{{ __('Company') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->company?->name }}</td>
                            <td>
                                <x-backend::dropdown.container permission="update|delete" :permission_params="$user">
                                    <x-backend::dropdown.item
                                        permission="update" :permission_params="$user"
                                        :route="route('backend.users.edit', $user)">
                                        <i class="fal fa-pen mr-1"></i> {{ __('Edit') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="delete" :permission_params="$user"
                                        class="text-danger" data-bs-toggle="modal"
                                        data-bs-target="#confirm_user_{{ $user->id }}">
                                        <i class="fa fa-trash mr-1"></i> {{ __('Delete') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::switch-to-user :user="$user"/>
                                </x-backend::dropdown.container>
                            </td>
                            <x-backend::modals.confirm
                                permission="edit" :permission_params="$user"
                                :route="route('backend.users.destroy', $user)"
                                :model="$user" :button="false"
                            />
                        </tr>
                    @empty
                        <x-backend::layout.tr-no-record/>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class=" py-0 px-2">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
