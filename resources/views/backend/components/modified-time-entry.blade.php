@props([
    'originalTime',
    'modifiedTime',
    'modification',
    'entryType'
])

@if($modifiedTime)
    {{ carbon($originalTime)->format(config('dates.attendance.datetime')) }}
    <i class="fal fa-long-arrow-right"></i>
    {{ carbon($modifiedTime)->format(config('dates.attendance.datetime')) }}
@endif

@if($modification->type == 'delete')
    @if($entryType == 'checkin')
        {{ carbon($modification->modifiable->checkin->logged_at)->format(config('dates.attendance.datetime')) }}
    @endif

    @if($entryType == 'checkout' && $modification->modifiable->checkout->logged_at)
        {{ carbon($modification->modifiable->checkout->logged_at)->format(config('dates.attendance.datetime')) }}
    @endif
@endif
