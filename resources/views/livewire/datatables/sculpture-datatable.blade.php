<div class="card">
    <x-loader />
    <div class="card-header d-flex flex-column">
        <div class="d-flex mb-2">
            <h5 class="me-auto">{{ $heading }}</h5>
            <div class="float-end">
                @if(isset($routes['create']))
                    <a href="{{ route($routes['create']) }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i>
                        {{ __('Add New') }}</a>
                @endif
                @include('backend.includes.datatable.bulk-delete')
            </div>
        </div>
        <div class="d-flex">
            @include('backend.includes.datatable.search')
            <div class="me-1">
                <select wire:model.live="selectedCollection" class="form-control rounded-0">
                    <option value="">All Collections</option>
                    @foreach($collections as $collection)
                        <option value="{{$collection->id}}">{{$collection->name}}</option>
                    @endforeach
                </select>
            </div>

            @if(isset($columns['company_name']))
                <div class="me-1">
                    <select wire:model.live="selectedCompany" class="form-control rounded-0">
                        <option value="">All Companies</option>
                        @foreach($companies as $company)
                            <option value="{{$company->id}}">{{$company->name}}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            {{--<div class="me-1">
                <select wire:model.live="selectedArtist" class="form-control">
                    <option value="">All Artists</option>
                    @foreach($artists as $artist)
                    <option value="{{$artist}}">{{$artist}}</option>
                    @endforeach
                </select>
            </div>--}}
            @include('backend.includes.datatable.reset-filters')
        </div>

        @if($selectedRows && user()->can('bulkUpdate', \App\Models\SculptureModel::class))
            <div class="d-flex mt-2 justify-content-end">
                <div class="me-1 ">
                    <label for="">Move to Collection</label>
                    <select wire:model.live="targetCollection" class="form-control  rounded-0 border-black">
                        <option value="">Select Collection</option>
                        @foreach($collections as $collection)
                            <option value="{{$collection->id}}">{{$collection->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="align-self-end ms-2">
                    <button class="btn btn-primary {{ !$targetCollection ? 'disabled' : '' }}"
                        wire:click="updateCollection">{{ __('Move') }}</button>
                </div>
            </div>
        @endif
        @include('backend.includes.datatable.toggle-columns')
    </div>
    <div class="card-body py-0">
        <div class="mb-3 scrollbar table-responsive" x-data="{sculptureImage: null}">
            <table class="table table-hover fs--1 table-sm">

                @include('backend.includes.datatable.header')

                <tbody class="list">
                    @foreach($rows as $row)
                        <tr class="dt-row">
                            @include('backend.includes.datatable.bulk-selection')
                            <td class="td sculpture-img">
                                <img @click="sculptureImage = @js($row->image_url);" src="{{ $row->image_url }}" alt=""
                                    width="50" data-bs-toggle="modal" data-bs-target="#sculptureImage">
                            </td>

                            @include('backend.includes.datatable.content')

                            <td>
                                @include('backend.includes.datatable.actions')
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="modal fade" id="sculptureImage" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img :src="sculptureImage">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('backend.includes.datatable.footer')
</div>