<header id="header" class="main__header universal mini">
    <nav id="navbar" class="main__navbar w-100">
        <div class="left">
            <button class="menu__btn d-lg-none">
                <svg
                    width="56"
                    height="56"
                    viewBox="0 0 56 56"
                    fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M1 12H1.56 56"
                        stroke="black"
                        stroke-width="8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    ></path>
                    <path
                        d="M1 28H1.56 56"
                        stroke="black"
                        stroke-width="8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    ></path>
                    <path
                        d="M1 44H1.56 56"
                        stroke="black"
                        stroke-width="8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    ></path>
                </svg>
            </button>
        </div>
        <div class="search__box ms-auto ms-lg-0">
            {{--<div class="input-group">
                <span class="input-group-text" id="nav__search__icon">
                    <x-svg.magnifying-glass/>
                </span>
                <input
                    type="text"
                    class="form-control d-none d-lg-inline-block"
                    placeholder="Search"
                    aria-describedby="nav__search__icon"
                />
            </div>--}}
        </div>
        <div class="right">
            <ul class="navbar__nav dash__nav">
                {{--<li class="nav__item">
                    <a href="#" class="nav__link">Help</a>
                </li>
                <li class="nav__item">
                    <a href="#" class="nav__link">
                        Notifications
                        <span
                            class="position-absolute top-0 start-100 translate-middle p-1 border border-light rounded-circle badge__notification"
                        >
                            <span class="visually-hidden">New alerts</span>
                        </span>
                    </a>
                </li>--}}
                {{--<li class="nav__item">
                    <a href="{{ route('profile.edit') }}" class="nav__link">
                        <div href="#" class="profile__photo">
                            <img
                                src="{{ user()->avatar_url }}"
                                alt="profile"
                                width="48"
                                height="auto"
                                class="avatar"
                            />
                        </div>
                    </a>
                </li>--}}
            </ul>
        </div>
    </nav>
    <div class="search__box ms-auto ms-lg-0" id="search__box">
        <div class="input-group">
            <span class="input-group-text" id="nav__search__icon">
                <svg
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z"
                        stroke="#B5B5B5"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                    <path
                        d="M20.9999 20.9999L16.6499 16.6499"
                        stroke="#B5B5B5"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </span>
            <input
                type="text"
                class="form-control"
                placeholder="Search"
                aria-describedby="nav__search__icon"
            />
        </div>
    </div>
</header>
