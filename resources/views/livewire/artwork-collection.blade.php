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
        {{--<div class="outside">
            <div class="line"></div>
            <button class="editor-comment-btn btn" @click="sidebar = 'comments'">
                <i class="fal fa-comment-alt-lines"></i>
                <span>{{ __('Comments') }}</span>
            </button>
        </div>--}}
    </div>
    <div class="card-div">
        <div class="card-row row">
            @php
                $firstArtworkColumn = $artworks->getCollection();
                $secondArtworkColumn = $firstArtworkColumn->splice(0,ceil($firstArtworkColumn->count() / 2));

                $artworkColumns['firstArtworkColumn'] = $firstArtworkColumn;
                $artworkColumns['secondArtworkColumn'] = $secondArtworkColumn;
            @endphp

            @foreach($artworkColumns as $artworkColumn)
                <div class="col-md-12 col-xl-6">
                    @foreach($artworkColumn as $artwork)
                        <div class="card mb-2 artwork-img"
                             data-img-url="{{ $artwork->image_url. "?uuid=". str()->uuid() }}"
                             data-title="{{$artwork->name}}"
                             data-thumb-url="{{$artwork->image_url}}"
                             data-artwork-id="{{$artwork->id}}"
                             data-scale="{{$artwork->data->scale}}"
                        >
                            <div class="card-img">
                                <img src="{{ $artwork->image_url }}" alt="card-img" />
                            </div>
                            <div class="card-body">
                                <div class="heading">{{ $artwork->name }}</div>
                                <div class="paragraph">{{ $artwork->artist }}</div>
                                <div>[{{ $artwork->dimensions }}]</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    <div class="pagination-div">
        {{ $artworks->onEachSide(1)->links() }}
    </div>
</div>
