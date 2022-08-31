@props([
    'surface',
    'state',
    'projectId',
    'comments' => false
])

<div class="accordion__header">
    <div class="left">
        <div class="user__details">
            <h3 class="username">{{ $state->name }}</h3>
            <div class="tag">{{ $state->user->name }} | {{ $state->created_at->format('m/d/Y') }}</div>
        </div>
    </div>
    <div class="right">
        <div class="surface__items">
            <a href="{{ route('surfaces.show', [$surface, 'project_id' => $projectId, 'surface_state_id' => $state->id]) }}"
               class="icon me-1">
                <i class="fal fa-pencil"></i>
            </a>
            @if(!$comments)
            @php($options = json_encode(['confirm_btn_attributes' => "onclick=\"window.livewire.emit('removeSurfaceState', $state->id)\""]))
            <a href="#" class="icon me-1" onclick="window.livewire.emit('showModal', 'modals.confirm', {{ $options }})">
                <i class="fal fa-trash text-danger"></i>
            </a>
            @endif
            <livewire:likes :likeable="$state" wire:key="{{ $state->id }}"/>
            @if($comments)
                <button type="button" class="icon arrow">
                    <x-svg.angle-up/>
                </button>
            @endif
        </div>
    </div>
</div>
