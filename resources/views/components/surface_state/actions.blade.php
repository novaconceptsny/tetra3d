@props([
    'surface',
    'state',
    'layoutId',
    'comments' => false
])

<div class="card-body">
    <div class="main-card-body">
        <div class="text">
            <h6>{{ $state->name }}</h6>
            <p>{{ $state->user->name }} | {{ $state->created_at->format('m/d/Y') }}</p>
        </div>
        <div class="icon">
            <a href="{{ route('surfaces.show', [$surface, 'layout_id' => $layoutId, 'surface_state_id' => $state->id, 'return_to_versions' => true]) }}"
               class="me-1">
                <i class="fal fa-pencil text-dark"></i>
            </a>
            <a href="#" class=" me-1" wire:click="removeSurfaceState({{ $state->id }})" wire:key="remove_{{$state->id}}">
                <i class="fal fa-trash text-dark"></i>
            </a>
            <livewire:likes :likeable="$state" wire:key="{{ $state->id }}"/>
            @if($comments)
                <button type="button" class="arrow">
                    <x-svg.angle-up/>
                </button>
            @endif

        </div>
    </div>
</div>
