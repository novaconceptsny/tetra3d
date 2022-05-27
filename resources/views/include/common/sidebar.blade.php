<aside class="sidebar mini">
    <a href="{{ route('dashboard') }}" class="brand mini">
        <img
            src="{{ asset('images/tetra__logo.png') }}"
            alt="brand logo"
            width="48"
            height="auto"
            class="logo"
        />
    </a>

    <ul class="navbar__nav mini">
        <li class="nav__item">
            <a href="{{ route('dashboard') }}" class="nav__link">
                <span class="icon">
                    <x-svg.grid-2/>
                </span>
                <span class="nav__text hide"> Home </span>
            </a>
        </li>
        <li class="nav__item">
            <a href="{{ route('walls') }}" class="nav__link">
                <span class="icon">
                    <x-svg.plus/>
                </span>
                <span class="nav__text hide"> Walls</span>
            </a>
        </li>
        <li class="nav__item">
            <a href="{{ route('dashboard') }}" class="nav__link plus">
                <span class="icon">
                    <x-svg.magnifying-glass/>
                </span>
                <span class="nav__text hide">Tour</span>
            </a>
        </li>
        <li class="nav__item">
            <a href="{{ route('artworks.index') }}" class="nav__link">
                <span class="icon">
                    <x-svg.book-open/>
                </span>
                <span class="nav__text hide"> Gallery</span>
            </a>
        </li>
    </ul>
    <hr class="divider"/>
    <ul class="navbar__nav mini bottom">
        <li class="nav__item">
            <a href="#" class="nav__link">
                <span class="icon">
                    <x-svg.bell/>
                </span>
                <span class="nav__text hide"> Notifications </span>
            </a>
        </li>
        <li class="nav__item">
            <a href="#" class="nav__link">
                <span class="icon">
                    <x-svg.circle-question/>
                </span>
                <span class="nav__text hide"> Help </span>
            </a>
        </li>
        <li class="profile__item">
            <a href="#" class="nav__link mx-2 p-2">
                <img
                    src="{{ asset('images/profile__photo.png') }}"
                    alt="profile photo"
                    width="86"
                    height="auto"
                    class="avatar"
                />
                <span class="nav__text hide"> John Doe </span>
            </a>
        </li>
        <li class="expand__menu__item">
            <a href="#" class="nav__link" id="expand__menu">
                <span class="icon">
                    <x-svg.angles-right/>
                </span>
            </a>
        </li>
    </ul>
</aside>
