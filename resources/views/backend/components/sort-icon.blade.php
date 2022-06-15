@props([
    'direction' => 'asc',
    'sorted' => false
])

@if($sorted)
    <i class="fad fa-sort-{{ Str::lower($direction) == 'asc' ? 'down' : 'up' }} align-self-center ml-1"></i>
@else
    <i class="fad fa-sort align-self-center ml-1"></i>
@endif
