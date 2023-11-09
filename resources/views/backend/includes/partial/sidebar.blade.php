<div class="leftside-menu">
    <x-backend::logo logo="{{ asset('backend/images/logo/logo_light.png') }}" mode="light"/>
    <x-backend::logo logo="{{ asset('backend/images/logo/logo_dark.png') }}" mode="dark"/>

    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <ul class="side-nav">
            <x-backend::layout.sidebar.title label="{{ __('Navigation') }}"/>

            <x-backend::layout.sidebar.nav-item
                label="Projects" icon="fal fa-home" route="{{ route('backend.projects.index') }}"
                permission="viewAny" :permission-params="\App\Models\Project::class"
            />
            <x-backend::layout.sidebar.nav-item
                label="Tours" icon="fal fa-vr-cardboard" route="{{ route('backend.tours.index') }}"
                permission="viewAny" :permission-params="\App\Models\Tour::class"
            />
            <x-backend::layout.sidebar.nav-item
                label="Users" icon="fal fa-users" route="{{ route('backend.users.index') }}"
                permission="viewAny" :permission-params="\App\Models\User::class"
            />
            <x-backend::layout.sidebar.nav-item
                label="Companies" icon="fal fa-sitemap" route="{{ route('backend.companies.index') }}"
                permission="viewAny" :permission-params="\App\Models\Company::class"
            />
            <x-backend::layout.sidebar.nav-item
                label="Artworks" icon="fal fa-paint-brush" route="{{ route('backend.artworks.index') }}"
                permission="viewAny" :permission-params="\App\Models\Artwork::class"
            />
            <x-backend::layout.sidebar.nav-item
                label="Artwork Collections" icon="fal fa-images" route="{{ route('backend.artwork-collections.index') }}"
                permission="viewAny" :permission-params="\App\Models\ArtworkCollection::class"
            />

            <x-backend::layout.sidebar.nav-item
                label="Logs" icon="fal fa-bug" route="{{ url(config('log-viewer.route_path')) }}"
                permission="viewLogViewer" target="_blank"
            />

        </ul>
        <div class="clearfix"></div>
    </div>
</div>
