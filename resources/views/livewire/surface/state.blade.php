<div class="col-md-6 col-lg-3 mb-3">
    <div href="#" class="card {{ $state->active ? 'shadow border-2 border-success' : '' }}">
        <a href="#" class="overlay__link" wire:click="$emit('changeActiveState', {{ $state }})"></a>
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
                <x-surface_state.actions :surface="$surface" :state="$state" :project-id="$projectId"/>
            </div>
        </div>
    </div>
</div>
