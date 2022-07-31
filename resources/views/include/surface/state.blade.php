<div class="col-md-6 col-lg-3 mb-3 ">
    <div href="#" class="card">
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
                            <div class="profiles__icons">
                                @include('include.partials.contributors')
                            </div>
                        </div>
                    </div>
                    <div class="right">
                        <div class="surface__items">
                            @php($options = json_encode(['route' => route('surfaces.destroy', $state)]))
                            <a href="#" class="icon" onclick="window.livewire.emit('showModal', 'modals.delete-surface', {{ $options }})">
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
</div>
