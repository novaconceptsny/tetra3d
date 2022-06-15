@php($unreadNotificationsCount = user()->unreadNotifications->count())
@php($notificationsCount = user()->notifications->count())
<li class="dropdown notification-list">
    <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button"
       aria-haspopup="false" aria-expanded="false">
        <i class="fal fa-bell noti-icon"></i>
        @if($unreadNotificationsCount)
        <span class="translate-middle badge rounded-pill bg-danger position-absolute"
              style="top: 40%;left: 80%">
            {{ $unreadNotificationsCount }}
            <span class="visually-hidden">{{ __('unread messages') }}</span>
        </span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg">

        <div class="dropdown-item noti-title">
            <h5 class="m-0">
                @if($unreadNotificationsCount)
                <span class="float-end">
                    <a href="{{ route('notifications.read.all') }}" class="text-dark">
                        <small>{{ __('Clear All') }}</small>
                    </a>
                </span>
                @endif
                {{ __('Notification') }}
            </h5>
        </div>

        <div style="max-height: 230px;" data-simplebar>

            @forelse(user()->notifications as $notification)
            <a href="javascript:void(0);" class="dropdown-item notify-item">
                <div class="notify-icon bg-primary">
                    <i class="{{ $notification->icon }}"></i>
                </div>
                <p class="notify-details">{{ $notification->message }}
                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                </p>
            </a>
            @empty
                <a href="javascript:void(0);" class="dropdown-item notify-item text-center">
                    {{ __("You're All Caught Up!") }}
                </a>
            @endforelse
        </div>


        @if($notificationsCount)
        <a href="{{ route('notifications') }}" class="dropdown-item text-center text-primary notify-item notify-all">
            {{ __('View All') }}
        </a>
        @endif

    </div>
</li>
