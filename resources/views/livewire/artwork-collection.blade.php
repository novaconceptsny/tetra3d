<div class="card-body">
    {{ $artworks->links() }}
    <form action="#" method="post">
        <div class="mb-3 search__box">
            <div class="input-group">
                <span class="input-group-text" id="search__icon">
                    <x-svg.magnifying-glass size="small"/>
                </span>
                <input
                    id="search_input"
                    type="text"
                    class="form-control"
                    placeholder="Type.."
                    aria-describedby="search__icon"
                />
            </div>
            <button type="submit" class="btn">Search</button>
        </div>
    </form>
    <div class="photo__collection">
        <ul class="item__list">
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
</div>
