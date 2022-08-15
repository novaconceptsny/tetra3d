@php($project = $project ?? null)

<header id="header" class="main__header universal mini">
    <nav id="navbar" class="main__navbar">
        <div class="left font-system-ui">
            <button class="menu__btn d-lg-none">
                <x-svg.bars/>
            </button>
            @if(isset($tour))
                <span class="navigator">{{  $tour?->name }}</span>
            @endif
            @if($project)
                <i class="fal fa-angle-right"></i>
                <span class="navigator">{{  $project?->name }}</span>
            @endif
        </div>
        <div class="right">
            <ul class="navbar__nav mini d-none d-lg-flex">
                @yield('page_actions')
            </ul>
            @if($project)
                <div class="contributors d-none d-xl-flex">
                    <div class="text">Contributors:</div>
                    <div class="profile__group">
                        @include('include.partials.contributors')
                    </div>
                </div>
            @endif
        </div>
    </nav>
</header>
