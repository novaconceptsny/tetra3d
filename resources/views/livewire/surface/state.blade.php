<div href="#" class="card {{ $state->active ? 'shadow border-2 border-success' : '' }}">
    <a href="#" class="overlay__link"></a>
    <div class="card-img-top">
        <img
            src="{{ $state->getFirstMediaUrl('thumbnail') }}"
            alt="image"
            width="100%"
            height="auto"
        />
    </div>
    <div class="card-body">
        <div class="accordion__item">
            <div class="accordion__header">
                <div class="left">
                    <div class="user__details">
                        <h3 class="username">{{ $state->name }}</h3>
                        <div class="tag">{{ $state->user->name }} | {{ $state->created_at->format('m/d/Y') }}</div>
                    </div>
                </div>
                <div class="right">
                    <div class="surface__items">
                        <a href="{{ route('surfaces.show', [$surface, 'project_id' => $projectId]) }}" class="icon" >
                            <i class="fal fa-pencil"></i>
                        </a>
                        @php($options = json_encode(['route' => route('surfaces.destroy', $state)]))
                        <a href="#" class="icon" onclick="window.livewire.emit('showModal', 'modals.confirm', {{ $options }})">
                            <x-svg.trash-can/>
                        </a>
                        <a href="#" class="icon">
                            <x-svg.thumbs-up/>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
