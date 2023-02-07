@php($navEnabled = $navEnabled ?? true)
@php($navbarLight = $navbarLight ?? false)
<header id="header">
    <nav class="navbar navbar-expand-lg {{ $navbarLight ? 'navbar-light' : '' }}">
        <div class="container-fluid">
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
                            <a class="nav-link" aria-current="page" href="{{ route('dashboard') }}">{{ __('Projects') }}</a>
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
                            <img class="rounded-circle" src="{{ user()->avatar_url }}" alt="{{ user()->name }}" width="59"/>
                        </a>
                        <div class="nav-item dropdown">
                            <a class="dropdown-link nav-link text-white dropdown-toggle"
                               href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ user()->name }}
                            </a>
                            <ul class="dropdown-menu pro-drop" aria-labelledby="navbarDropdown">
                                <div class="drop-profile">
                                    <img class="rounded-circle" src="{{ user()->avatar_url }}" alt="drop-img"/>
                                    <p class="profile-name">{{ user()->name }}</p>
                                    <small class="profile-email">{{ user()->email }}</small>
                                </div>
                                <div class="link">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fa fa-user"></i>
                                            {{ __('My Profile') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="fa fa-sign-out"></i>
                                            Logout
                                        </a>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
