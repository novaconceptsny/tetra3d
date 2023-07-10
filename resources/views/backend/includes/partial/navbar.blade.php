<div class="navbar-custom">
    <ul class="list-unstyled topbar-menu float-end mb-0">
        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle arrow-none" href="{{ route('dashboard') }}" target="_blank">
                <i class="dripicons-monitor noti-icon"></i>
            </a>
        </li>
        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user arrow-none me-0" data-bs-toggle="dropdown" href="#"
               role="button" aria-haspopup="false"
               aria-expanded="false">
                <span class="account-user-avatar">
                    <img src="{{ user()->avatar_url }}" alt="user-image" class="rounded-circle" style="border: 2px solid #222436 !important;">
                </span>
                <span>
                    <span class="account-user-name">{{ user()->name }}</span>
                    <span class="account-position">{{ user()->role }}</span>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
                <!-- item-->
                <div class=" dropdown-header noti-title">
                    <h6 class="text-overflow m-0">{{ __('Welcome') }} {{ user()->name }}!</h6>
                </div>

                <!-- item-->
                @if(session()->has('admin_id'))
                    <form class="d-inline" action="{{ route('back.to.admin') }}" method="post">
                        @csrf
                        <button type="submit" class="dropdown-item notify-item"><i class="fal fa-arrow-to-left"></i>
                            {{ __('Back to Admin') }}
                        </button>
                    </form>
                @endif
                <a href="javascript:void(0);"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="dropdown-item notify-item">
                    <i class="mdi mdi-logout me-1"></i>
                    <span>{{ __('Logout') }}</span>
                </a>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>

    </ul>
    {{--<a href="{{ route('dashboard.leftSidebarCondensed') }}" class="d-none d-xl-block" style="
	border: none;
    color: #313a46;
    height: 70px;
    line-height: 70px;
    width: 60px;
    background-color: transparent;
    font-size: 24px;
    cursor: pointer;
    float: left;
    z-index: 1;
    position: relative;
    margin-left: -24px;
	padding: 1px 6px;
    text-align: center;
">
        <i class="mdi mdi-menu"></i>
    </a>--}}
    <button class="button-menu-mobile open-left d-xl-none">
        <i class="mdi mdi-menu"></i>
    </button>
</div>
