@php($navEnabled = $navEnabled ?? true)
@php($navbarLight = $navbarLight ?? false)
<header id="header">
    <nav class="navbar navbar-expand-lg {{ $navbarLight ? 'navbar-light' : '' }}">
        <div class="nav-container mb-1">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('logo.png') }}" alt="dash-logo"/>
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
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('activity.index') }}">{{ __('Activity') }}</a>
                        </li>
                    </ul>
                @endif

                @yield('breadcrumbs')

                <div class="navbar-nav menu-nav">
                    <div class="nav-left-btn-div">
                        @yield('menu')
                        @yield('page_actions')
                    </div>
                    <div class="profile">
                        @auth
                            <div class="nav-item dropdown">
                                <a class="dropdown-link nav-link text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <img class="user-img-border" src="{{ user()->avatar_url }}" alt="{{ user()->name }}"/>
                                </a>
                                <ul class="dropdown-menu pro-drop" aria-labelledby="navbarDropdown">
                                    <div class="drop-profile">
                                        <img class="user-img-border" src="{{ user()->avatar_url }}" alt="{{ user()->name }}">
                                        <div class="user-detail">
                                            <h6>{{ user()->name }}</h6>
                                            {{--<p class="profile-name">Kasia Wink</p>--}}
                                            <p class="profile-email">{{ user()->email }}</p>
                                        </div>
                                    </div>
                                    <div class="link">
                                        @can('access-backend')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('backend.dashboard') }}" target="_blank">
                                                    <i class="fal fa-user-shield"></i>
                                                    {{ __('Admin Area') }}
                                                </a>
                                            </li>
                                        @endcan
                                        @if(session()->has('admin_id'))
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);"
                                                   target="_blank"
                                                   onclick="event.preventDefault(); document.getElementById('back-to-admin-form').submit();">
                                                    <i class="fal fa-arrow-from-left"></i>
                                                    {{ __('Back to Admin') }}
                                                </a>
                                                <form id="back-to-admin-form" target="_blank" class="d-none" action="{{ route('back.to.admin') }}" method="post">
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
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
