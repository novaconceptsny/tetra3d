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
                <i class="fal fa-pencil"></i>
            </a>
            @if(!$comments)
                @php($options = json_encode([
                    'confirm_btn_attributes' => "onclick=\"window.livewire.emit('removeSurfaceState', $state->id)\"",
                    'message' => 'Are you sure you want to delete this version ?'
                ]))
                <a href="#" class=" me-1" onclick="window.livewire.emit('showModal', 'modals.confirm', {{ $options }})">
                    <i class="fal fa-trash text-danger"></i>
                </a>
            @endif
            <livewire:likes :likeable="$state" wire:key="{{ $state->id }}"/>
            @if($comments)
                <button type="button" class="arrow">
                    <x-svg.angle-up/>
                </button>
            @endif

        </div>
    </div>
</div>
