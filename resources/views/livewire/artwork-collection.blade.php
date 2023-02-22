<div class="col-3 side-col">
    <x-loader/>
    <div class="top-div">
        <div class="search">
            <div class="search-bar input-group">
                <button class="input-group-text p-3 bg-white border-0">
                    <x-svg.magnifying-glass size="small"/>
                </button>
                <input type="text" class="form-control fs-4 border-0" placeholder="Search" wire:model.debounce.500ms="search"/>
            </div>
            <select class="form-select all-btn" wire:model="searchBy">
                <option value="all">All</option>
                <option value="artist">Artists</option>
                <option value="name">Artworks</option>
            </select>
        </div>
        <div class="outside">
            <div class="line"></div>
            <button class="editor-comment-btn" @click="sidebar = 'comments'">
                <i class="fal fa-comment-alt-lines"></i>
                <span>{{ __('Comments') }}</span>
            </button>
        </div>
    </div>
    <div class="card-div">
        <div class="card-row row">
            @foreach($artworks as $artwork)
            <div class="col-md-12 col-xl-6">
                <div class="card h-100">
                    <div class="card-img artwork-img"
                         data-img-url="{{ $artwork->image_url. "?uuid=". str()->uuid() }}"
                         data-title="{{$artwork->name}}"
                         data-thumb-url="{{$artwork->image_url}}"
                         data-artwork-id="{{$artwork->id}}"
                         data-scale="{{$artwork->data->scale}}">
                        <img src="{{ $artwork->image_url }}" alt="card-img" />
                    </div>
                    <div class="card-body">
                        <h6>{{ $artwork->name }}</h6>
                        <p>{{ $artwork->artist }}</p>
                        <p>[{{ $artwork->dimensions }}]</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="pagination-div">
        {{ $artworks->links() }}
    </div>
</div>
