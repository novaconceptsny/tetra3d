@props(['user'])

@if(user()->can('switch to user'))
    <form class="d-inline" action="{{ route('login.as.user', $user) }}" method="post">
        @csrf
        <button type="submit" class="dropdown-item">
            <i class="fal fa-sign-in mr-1"></i> {{ __('Login as this user') }}
        </button>
    </form>
@endif
