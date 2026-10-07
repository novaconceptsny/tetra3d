<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\BroadcastServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\MacroServiceProvider::class,

    // Wire Elements Pro components (registers "modal-pro" and "slide-over-pro")
    WireElements\Pro\Components\Modal\ModalServiceProvider::class,
    WireElements\Pro\Components\SlideOver\SlideOverServiceProvider::class,
];
