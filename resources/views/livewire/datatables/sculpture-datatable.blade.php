<div class="card">
    <x-loader/>
    <div class="card-header d-flex flex-column">
        <div class="d-flex mb-2">
            <h5 class="me-auto">{{ $heading }}</h5>
            <div class="float-end">
                @if(isset($routes['create']))
                    <a href="{{ route($routes['create']) }}" class="btn btn-sm btn-primary"><i
                            class="fal fa-plus"></i> {{ __('Add New') }}</a>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body py-0">
        <div class="mb-3 scrollbar table-responsive" x-data="{sculptureImage: null}">
            <table class="table table-hover fs--1 table-sm" >

                @include('backend.includes.datatable.s_header')

                <tbody class="list">
                @foreach($rows as $row)
                    <tr class="dt-row">
                        <td class="td sculpture-img">
                            <img
                                @click="sculptureImage = @js(asset('').'storage/sculptures/thumbnails/'.$row->image_url);"
                                src="{{ asset('').'storage/sculptures/thumbnails/'.$row->image_url }}"
                                alt="" width="50"
                                data-bs-toggle="modal" data-bs-target="#sculptureImage"
                            >
                        </td>

                        @include('backend.includes.datatable.content')

                        <td>
                            @include('backend.includes.datatable.s_actions')
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="modal fade" id="sculptureImage" tabindex="-1" >
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
</div>
