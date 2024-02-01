@php($navEnabled = $navEnabled ?? true)
@php($navbarLight = $navbarLight ?? false)
<header id="header">
    <nav class="navbar navbar-expand-lg {{ $navbarLight ? 'navbar-light' : '' }}">
        <div class="nav-container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
{{--                <img src="{{ asset('logo.png') }}" alt="dash-logo"/>--}}
                <svg width="38" height="41" viewBox="0 0 38 41" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <rect width="38" height="41" fill="url(#pattern0)"/>
                    <defs>
                        <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                            <use xlink:href="#image0_3275_7167" transform="scale(0.0263158 0.0243902)"/>
                        </pattern>
                        <image id="image0_3275_7167" width="38" height="41" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAApCAYAAABZa1t7AAAEhklEQVRYCe2YSyhtURjHjzd5k0cUJa8MhCIZKEISYUBIikQSAyIGTDCRiTBTDDCQDOQxoQyUQkSSgTzyyPuR9/O7/Vet3dnbPvu8tntvt7vqtM4+Z69v/fb3fet7bA39pUPzl3LRvw32/PxMS0tLVF9fT/Pz86oYwSyNPT4+0traGjU1NZGbmxuFhobS7OzsnwN7f3+n7e1t6urqoqioKLKwsCCNRvNnwa6vr2lwcJDS0tLI1dWVAQEKn7CwsN+vsc/PT1pZWaGsrCzy9fUlS0tLEZS9vT0VFxfT4eHh7zHl19cXXVxcUE1NDTk6On4Dghn9/f1pfHycnp6eCPerMXQ6PzR0eXlJQ0NDzHe4H3GzWVtbk5+fHzU0NNDLy4saLCIZsmD39/c0NTVF+fn5ZGdnJzIZAAFUVFREi4uL9PHxIRKo1oUsWGNjI3l7e4uAoCkbGxvKzs5mWry9vVWLgclB2Lm7uxNkyoLBTNxkfI6Ojqb+/n7a3d0VFku/wL+M9TH4b19fHyUkJNDm5qYgUi+Yp6cntbW10dbWlqLZ9vf3qaWlhZaXlwXhSl/wAAsLC5SYmEgeHh4sFq6urgpL9ILBdEpaQrDt7OxkwsPDww2KYw8PD5STk0MODg5CcIZljAKDgL29PeFJ8AUn9ubmhh2Q2NhYQbhSSsIDnJ6eUnd3Nzs83EW0Z7PAYIKdnR2qrKwUTMCFy4Hh/vPzcxodHaX09PRvp5yvxWwWGLQ1MjLCgq22UHyXgkFLMzMzVFFRQT4+PoJmpev4tVlgiFsIukhBXCCfORi0tLGxQXV1dRQSEkJWVlaie5HOkpOTKT4+XvT7j4LBZPCjyMhIWfjAwEDq6emhg4MDKisr+3kwaAGxDkne1tZWtCE0ihNYUlLCTjg0CjOXl5eL7lNVY0hZwcHB1N7eTldXV+yjrTFnZ2cWq8bGxkQ59cfAnJycGFB1dTWtr6+Lwgn3MZTaHR0ddHx8LPofFwArLS1VT2M4ldPT08wMExMThGCpaygleIAVFhaqBwZtwGQnJycs0OqC0vc7wAoKCtQD07ehof+bDaYvVxoKIr3v7e2N8vLyTNeYodWFdGNd1/A7pLSBgQHCAeLBGbNR4YIvjImJYfWYNKHrApD+Dt88Ojqi4eFhiouLEwHxPfSCKVWwubm5TLh2tSmFkF6jSZmcnGSluru7+zcoLy8vVhSg+uBDth5TqvkR4VHzo1XDawGED6WBAhIRPygo6FuHhUo5JSWFQaPx0R6yYLgBG+rrktC2NTc3ExxZOl5fX6m1tZUCAgJIrlRHK4hiACWR3MPpBOMbwTf09ZXYHKaCyfBBqYO8KW35oG0AVVVVsUKT7yE36wXji/BUSp04knRmZiYzm4uLi8iPAIjTnZGRQXNzcywdcbm6ZoPBuACldxf8dGnPSPJJSUnU29tLZ2dnBndRRoMBEFFb7m2PNhC0FBERwaoOtGVYY8wwCYxvIH0/xsHQjtXW1rJWDj5nyjALjG/I3yiio0pNTWUtHKoOHBxThypgpm6utO4/mJJ25P77BZFFwELkd0tjAAAAAElFTkSuQmCC"/>
                    </defs>
                </svg>

                <span>Tetra</span>

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
                            <a class="nav-link" href="{{ route('artworks.index') }}">{{ __('Inventory') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('activity.index') }}">{{ __('Activity') }}</a>
                        </li>
                    </ul>
                @endif

                @yield('breadcrumbs')

                <div class="navbar-nav menu-nav">
                    @yield('outside-menu')
                    <div class="nav-left-btn-div">
                        @yield('menu')
                        @yield('page_actions')
                    </div>
                    <div class="profile">
                        @auth
                            <div class="nav-item dropdown">
                                <a class="dropdown-link nav-link text-white" href="#" id="navbarDropdown" role="button"
                                   data-bs-toggle="dropdown">
                                    <img class="user-img-border" src="{{ user()->avatar_url }}"
                                         alt="{{ user()->name }}"/>
                                </a>
                                <ul class="dropdown-menu pro-drop" aria-labelledby="navbarDropdown">
                                    <div class="drop-profile">
                                        <img class="user-img-border" src="{{ user()->avatar_url }}"
                                             alt="{{ user()->name }}">
                                        <div class="user-detail">
                                            <h6>{{ user()->name }}</h6>
                                            {{--<p class="profile-name">Kasia Wink</p>--}}
                                            <p class="profile-email">{{ user()->email }}</p>
                                        </div>
                                    </div>
                                    <div class="link">
                                        @can('access-backend')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('backend.dashboard') }}"
                                                   target="_blank">
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
                                                <form id="back-to-admin-form" target="_blank" class="d-none"
                                                      action="{{ route('back.to.admin') }}" method="post">
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
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                              class="d-none">
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
