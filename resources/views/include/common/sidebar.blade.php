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
            <a href="#" class="nav__link">
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
        @can('access-backend')
            <li class="nav__item">
                <a href="{{ route('backend.dashboard') }}" class="nav__link" target="_blank">
                    <x-ri-dashboard-line/>
                    <span class="nav__text hide"> Backend </span>
                </a>
            </li>
        @endcan
        @if(session()->has('admin_id'))
            <a class="nav__link" href="javascript:void(0);"
               onclick="event.preventDefault(); document.getElementById('back-to-admin-form').submit();">
                <span class="icon">
                    <i class="fal fa-user-unlock"></i>
                </span>
                <span class="nav__text hide"> Back to Admin </span>
            </a>
            <form id="back-to-admin-form" class="d-none" action="{{ route('back.to.admin') }}" method="post">
                @csrf
            </form>
        @endif
        <li class="nav__item">
            <a class="nav__link" href="javascript:void(0);"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span class="icon">
                    <i class="fal fa-power-off"></i>
                </span>
                <span class="nav__text hide"> Logout </span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
        <li class="profile__item">
            <a href="#" class="nav__link mx-2 p-2">
                <img
                    src="{{ user()->avatar_url }}"
                    alt="profile photo"
                    width="86"
                    height="auto"
                    class="avatar"
                />
                <span class="nav__text hide"> {{ user()->name }} </span>
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
