@props([
    'direction' => 'asc',
    'sorted' => false
])

@if($sorted)
    <i class="fad fa-sort-{{ Str::lower($direction) == 'asc' ? 'down' : 'up' }} align-self-center ms-2"></i>
@else
    <i class="fad fa-sort align-self-center ms-2"></i>
@endif
