@php($project = $project ?? null)

<header id="header" class="main__header universal mini">
    <nav id="navbar" class="main__navbar">
        <div class="left">
            <button class="menu__btn d-lg-none">
                <x-svg.bars/>
            </button>
            <a href="{{ route('dashboard') }}" class="prev__btn font-primary">
                <x-lineawesome-angle-left-solid class="text-dark"/>
                <span class="navigator">{{  $project?->name }}</span>
            </a>
        </div>
        <div class="right">
            <ul class="navbar__nav mini d-none d-lg-flex">
                @yield('page_actions')
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
