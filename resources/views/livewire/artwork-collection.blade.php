<div class="col-3 side-col" :class="{ 'd-none': sidebar === 'comments' }">
    <x-loader/>
    <div class="top-div">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0" id="basic-addon1">
                <i class="fas fa-search fa-lg"></i>
            </span>
            {{--<button class="input-group-text p-3 bg-white border-0">
                <x-svg.magnifying-glass size="small"/>
            </button>--}}
            <input type="text" class="form-control form-control-md lead border-start-0" placeholder="Search" wire:model.live.debounce.500ms="search"/>
        </div>
        <select class="form-select form-control all-btn" wire:model.live="collectionId">
            <option value="">All</option>
            @foreach($collections as $collection)
                <option value="{{ $collection->id }}">{{ $collection->name }}</option>
            @endforeach
        </select>
        <div class="change-icon-layout d-flex justify-content-between align-items-center">
            <button class="btn border  border-secondary" onclick="setListLayout()"><i class="fas fa-bars"></i></button>
            <button class="btn border border-secondary" onclick="setGridLayout()"> <i class="fas fa-th-large"></i></button>

        </div>
        {{--<div class="outside">
            <div class="line"></div>
            <button class="editor-comment-btn btn" @click="sidebar = 'comments'">
                <i class="fal fa-comment-alt-lines"></i>
                <span>{{ __('Comments') }}</span>
            </button>
        </div>--}}
    </div>
    <div class="card-div">
        <div class="row card-row">
            @php
                $firstArtworkColumn = $artworks->getCollection();
                $secondArtworkColumn = $firstArtworkColumn->splice(0,ceil($firstArtworkColumn->count() / 2));

                $artworkColumns['firstArtworkColumn'] = $firstArtworkColumn;
                $artworkColumns['secondArtworkColumn'] = $secondArtworkColumn;
            @endphp

            @foreach($artworkColumns as $artworkColumn)
                @foreach($artworkColumn as $artwork)
                    <div class="col-12 mb-3 card-col">
                        <div class="card mb-3 artwork-img"
                             data-img-url="{{ $artwork->image_url. "?uuid=". str()->uuid() }}"
                             data-title="{{$artwork->name}}"
                             data-thumb-url="{{$artwork->image_url}}"
                             data-artwork-id="{{$artwork->id}}"
                             data-scale="{{$artwork->data->scale}}"
                        >
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card-img">
                                        <img src="{{ $artwork->image_url }}" alt="card-img" />
                                        {{--                                        <img src="{{ $artwork->image_url }}" alt="card-img" />--}}
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <div class="paragraph">{{ $artwork->artist }}</div>
                                        <div class="heading">{{ $artwork->name }}</div>
                                        <div class="dimensions">{{ $artwork->dimensions }} inches</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
    <div class="pagination-div">
        {{ $artworks->onEachSide(1)->links() }}
    </div>
</div>
