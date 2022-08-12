@props([
    'message' => 'Loading...'
])

@once
    <div class="loader" wire:loading {{ $attributes->merge() }}>{{ $message }}</div>
@endonce
