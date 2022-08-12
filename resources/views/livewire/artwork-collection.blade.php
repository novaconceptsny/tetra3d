<div class="card-body position-relative">
    <form action="#" method="post">
        <div class="mb-3 search__box">
            <div class="input-group">
                <span class="input-group-text" id="search__icon">
                    <x-svg.magnifying-glass size="small"/>
                </span>
                <input
                    wire:model="search"
                    id="search_input"
                    type="text"
                    class="form-control search"
                    placeholder="Type.."
                    aria-describedby="search__icon"
                />
            </div>
            <select class="form-control w-50" wire:model="searchBy" id="">
                <option value="all">All</option>
                <option value="artist">Artists</option>
                <option value="name">Artworks</option>
            </select>
        </div>
    </form>
    <div class="photo__collection" style="height: 80%;">
        <ul class="item__list" wire:loading.remove>
            @foreach($artworks as $artwork)
                <li class="item artwork-img"
                    data-img-url="{{$artwork->image_url}}"
                    data-title="{{$artwork->name}}"
                    data-thumb-url="{{$artwork->image_url}}"
                    data-artwork-id="{{$artwork->id}}"
                    data-scale="{{$artwork->data->scale}}"
                >
                    <div class="preview">
                        <img
                            src="{{ $artwork->image_url }}"
                            alt="thumbnail"
                            width="100%"
                            height="auto"
                        />
                    </div>
                    <h3 class="item__title">{{ $artwork->name }}</h3>
                    <p class="item__text">{{ $artwork->artist }}</p>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="position-relative py-2" style="bottom: 0; left: 25%">
        {{ $artworks->links() }}
    </div>
</div>
