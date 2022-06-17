<div class="leftside-menu">
    <x-backend::logo logo="{{ asset('backend/images/logo/logo_light.png') }}" mode="light"/>
    <x-backend::logo logo="{{ asset('backend/images/logo/logo_dark.png') }}" mode="dark"/>

    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <ul class="side-nav">
            <x-backend::layout.sidebar.title label="{{ __('Navigation') }}"/>

            <x-backend::layout.sidebar.nav-item label="Projects" icon="fal fa-home" route="{{ route('backend.projects.index') }}"/>
            <x-backend::layout.sidebar.nav-item label="Tours" icon="fal fa-circle" route="{{ route('backend.tours.index') }}"/>

        </ul>
        <div class="clearfix"></div>
    </div>
</div>
