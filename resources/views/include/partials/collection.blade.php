<div class="collections card mini" style="z-index: 999 !important;">
    <div class="card-header">
        <h2 class="collection__title font-secondary">Collection</h2>
        <span class="expand__icon">
            <x-svg.up-right-and-down-left-from-center/>
        </span>
    </div>
    <div class="card-body">
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
                        data-img-url="https://tetra-gallery.s3.amazonaws.com/artgroup_5/164713872897.png" data-title="Space in Between - Nopal #5" data-thumb-url="https://tetra-gallery.s3.amazonaws.com/artgroup_thumbnail_5/164713872897.png" data-artwork-id="164713872897"
                    >
                        <div class="preview">
                            <img
                                src="{{ $artwork->url }}"
                                alt="thumbnail"
                                width="100%"
                                height="auto"
                            />
                        </div>
                        <h3 class="item__title">{{ $artwork->name }}</h3>
                        <p class="item__text">I added some</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
