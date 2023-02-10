@php($navEnabled = $navEnabled ?? true)
@php($navbarLight = $navbarLight ?? false)
<header id="header">
    <nav class="navbar navbar-expand-lg {{ $navbarLight ? 'navbar-light' : '' }}">
        <div class="nav-container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('redesign/images/Group-4740 5.svg') }}" alt="dash-logo"/>
            </a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                @if($navEnabled)
                    <ul class="navbar-nav mx-auto link-nav">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page"
                               href="{{ route('dashboard') }}">{{ __('Projects') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('artworks.index') }}">{{ __('Collection') }}</a>
                        </li>
                    </ul>
                @endif

                @yield('breadcrumbs')

                <div class="navbar-nav menu-nav ms-auto">
                    <div class="nav-left-btn-div">
                        @yield('menu')
                        @yield('page_actions')
                    </div>
                    <div class="profile">
                        <a href="#">
                            <img class="rounded-circle" src="{{ user()->avatar_url }}" alt="{{ user()->name }}"
                                 width="59"/>
                        </a>
                        <div class="nav-item dropdown">
                            <a class="dropdown-link nav-link text-white dropdown-toggle"
                               href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                               aria-expanded="false">
                                {{ user()->name }}
                            </a>
                            <ul class="dropdown-menu pro-drop" aria-labelledby="navbarDropdown">
                                <div class="drop-profile">
                                    <img src="{{ user()->avatar_url }}" alt="{{ user()->name }}">
                                    <div class="user-detail">
                                        <h6>{{ user()->name }}</h6>
                                        {{--<p class="profile-name">Kasia Wink</p>--}}
                                        <p class="profile-email">{{ user()->email }}</p>
                                    </div>
                                </div>
                                <div class="link">
                                    @can('access-backend')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('backend.dashboard') }}">
                                                <i class="fal fa-user-shield"></i>
                                                {{ __('Admin Area') }}
                                            </a>
                                        </li>
                                    @endcan
                                    @if(session()->has('admin_id'))
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);"
                                               onclick="event.preventDefault(); document.getElementById('back-to-admin-form').submit();">
                                                <i class="fal fa-arrow-from-left"></i>
                                                {{ __('Back to Admin') }}
                                            </a>
                                            <form id="back-to-admin-form" class="d-none" action="{{ route('back.to.admin') }}" method="post">
                                                @csrf
                                            </form>
                                        </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fal fa-user"></i>
                                            {{ __('My Profile') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fal fa-sign-out"></i>
                                            {{ __('Logout') }}
                                        </a>
                                    </li>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
