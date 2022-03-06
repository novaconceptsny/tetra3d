<div class="collections card mini">
    <div class="card-header">
        <h2 class="collectoin__title">Collection</h2>
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
                        type="text"
                        class="form-control"
                        placeholder="Type.."
                        aria-describedby="search__icon"
                    />
                </div>
                <button type="submit" class="btn">Search</button>
            </div>
        </form>
        <div class="photo__collectoin">
            <ul class="item__list">
                @for($i=1; $i<22; $i++)
                    <li class="item">
                        <div class="preview">
                            <img
                                src="{{ "images/collection/collection{$i}.png" }}"
                                alt="thumbnail"
                                width="100%"
                                height="auto"
                            />
                        </div>
                        <h3 class="item__title">Amy</h3>
                        <p class="item__text">I added some</p>
                    </li>
                @endfor
            </ul>
        </div>
    </div>
</div>
