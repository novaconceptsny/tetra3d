<header id="header" class="main__header universal mini">
    <nav id="navbar" class="main__navbar">
        <div class="left">
            <button class="menu__btn d-lg-none">
                <x-svg.bars/>
            </button>
            <a href="{{ route('dashboard') }}" class="prev__btn">
                <x-lineawesome-angle-left-solid class="text-dark"/>
                <span class="navigator"> Pablo Picasso Show </span>
            </a>
        </div>
        <div class="right">
            <ul class="navbar__nav mini d-none d-lg-flex">
                @yield('page_actions')
                {{--<li class="nav__item">
                    <a href="#" class="nav__link">Configuration C</a>
                </li>
                <li class="nav__item">
                    <a href="#" class="nav__link">
                        <x-svg.location-dot/>
                        Map
                    </a>
                </li>
                <li class="nav__item">
                    <a href="#" class="nav__link">
                        <x-phosphor-globe-thin height="24" width="24"/>
                        Return to 360
                    </a>
                </li>
                <li class="nav__item">
                    <a href="#" class="nav__link">
                        <x-svg.magnifying-glass-plus/>
                        97%
                    </a>
                </li>--}}
            </ul>
            <div class="contributors d-none d-xl-flex">
                <div class="text">Contributors:</div>
                <div class="profile__group">
                    @include('include.partials.contributors')
                </div>
            </div>
        </div>
    </nav>
</header>
