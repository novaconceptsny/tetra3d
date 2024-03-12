<div class="card">
    <div class="card-header d-flex flex-column">
        <div class="d-flex mb-2">
            <h5 class="me-auto">{{ $heading }}</h5>
            <div class="float-end">
                @if($routes['create'])
                    <a href="{{ route($routes['create']) }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> {{ __('Add New') }}</a>
                @endif
                @if($selectedRows)
                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm_bulk_delete">
                        <i class="fal fa-trash"></i> {{ __('Bulk Delete') }}
                    </button>
                    <div class="modal fade border-0" id="confirm_bulk_delete">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <p class="text-center mt-2">{{ __('Are you sure you want to perform this action ?') }}</p>
                                    <div class="float-right">
                                        <button wire:click="deleteSelectedRows" class="btn btn-sm btn-danger" data-dismiss="modal">{{ __('Yes') }}</button>
                                        <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">{{ __('No') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- Filters Start -->
        <div class="d-flex">
            <div>
                <select wire:model.live="perPage" class="form-control" id="per_page">
                    @foreach($perPageOptions as $option)
                        <option value="{{$option}}">{{$option}}</option>
                    @endforeach
                </select>
            </div>
            <div class="ml-auto">
                <input wire:model.live="search" class="form-control" type="text" placeholder="{{ __('Search') }}">
            </div>
            <div class="align-self-end ms-2 col">
                <button class="btn btn-primary btn-sm" wire:click="resetFilters">{{ __('Reset') }}</button>
            </div>
        </div>
        <div class="mt-3">
            <h5>Visible Columns</h5>
           <div>
               @foreach($columns as $column => $header)
                   <button class="btn btn-{{$header['visible'] ? '' : 'outline-'}}primary btn-sm me-1"
                           wire:click="changeVisibility('{{$column}}')">{{ __($header['name']) }}</button>
               @endforeach
           </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="mb-3 table-responsive table-nowrap scrollbar">
            <table class="table table-bordered table-striped table-hover fs--1 table-sm">

                <thead class="bg-200 text-900">
                <tr class="dt-row">
                    <th></th>
                    @foreach($columns as $col_name => $column)
                        @php
                            $moveAfter = isset($column['move_after']) ? "data-move-after={$column['move_after']}" : "";
                            $moveBefore = isset($column['move_before']) ? "data-move-before={$column['move_before']}" : "";
                        @endphp

                        @if($column['visible'])
                            <th class="cursor-pointer td" scope="col" wire:click="sort('{{$col_name}}')"
                                data-column="{{ $col_name }}" {{ $moveAfter }} {{ $moveBefore }}>
                                <span class="d-flex">
                                    <span> {{ __($column['name']) }}</span>
                                    @if($column['sortable'] ?? false)
                                        <x-backend::sort-icon
                                            :direction="$sortOrder"
                                            :sorted="$sortBy == ($columns[$col_name]['sort_by'] ?? $col_name)"
                                        />
                                    @endif
                                </span>
                            </th>
                        @endif
                    @endforeach
                    <th></th>
                </tr>
                </thead>

                <tbody class="list">
                @foreach($rows as $row)
                    <tr class="dt-row">
                        <td>
                            <input type="checkbox" wire:model.live="selectedRows" value="{{$row->id}}">
                        </td>
                        @foreach($columns as $col_name => $column)
                            @if($column['visible'] && ($column['render'] ?? true))
                                <td class="td" data-column="{{ $col_name }}" >{!! $row->$col_name !!}</td>
                            @endif
                        @endforeach
                        @yield('extended_columns')
                        {{--<td class="td" data-move-before="name" >
                            <img height="50" src="{{ $row->image_url }}">
                        </td>--}}
                        <td>
                            @yield('actions')
                            {{--<x-backend::dropdown.container>
                                <x-backend::dropdown.item :route="route($routes['edit'], $row)">
                                    <i class="fal fa-pen mr-1"></i> {{ __('Edit') }}
                                </x-backend::dropdown.item>
                                <x-backend::dropdown.divider/>
                                <x-backend::dropdown.item class="text-danger" data-toggle="modal" data-target="#confirm_{{$label}}_{{ $row->id }}">
                                    <i class="fa fa-trash mr-1"></i> {{ __('Delete') }}
                                </x-backend::dropdown.item>
                                <x-backend::modals.confirm :route="route($routes['delete'], $row)" :model="$row" :button="false"></x-backend::modals.confirm>
                            </x-backend::dropdown.container>--}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer py-0">
        <div class="mt-1 d-flex justify-content-between" style="line-height: 1;">
            <p class="fs--1 align-self-center">
                <select wire:model.live="perPage" class="form-control form-control-sm d-inline" style="width: auto" id="per_page">
                    @foreach($perPageOptions as $option)
                        <option value="{{$option}}">{{$option}}</option>
                    @endforeach
                </select>
                <span>Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }} of  {{ number_format($rows->total()) }} entries</span>
            </p>
            {{ $rows->appends(request()->query())->links() }}
        </div>
    </div>
</div>
