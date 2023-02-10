@props([

])
<div class="dropdown nav-left-btn me-2">
    <button class="dropdown menu-btn" type="button" id="dropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
        {{ __('Menu') }}
    </button>

    <div class="dropdown-menu pt-0" aria-labelledby="dropdownMenu">
        {{ $slot }}
    </div>
</div>
