<div>
    <x-loader message="Switching current state..."/>
    <h4 class="font-secondary section__title">
        <livewire:surface-title :surface="$surface"/>
    </h4>
    <div class="inner__field row mb-4">
        <x-surface_state.add-new :surface="$surface" :project-id="$projectId"/>
        @foreach($surface->states as $state)
            <div class="col-md-6 col-lg-3 mb-3">
                <div href="#" class="card {{ $state->active ? 'shadow border-2 border-success' : '' }}">
                    @if(!$state->active)
                        <a href="#" class="overlay__link" wire:click="changeActiveState({{ $state }})"></a>
                    @endif
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
                            <x-surface_state.actions
                                :surface="$surface" :state="$state"
                                :project-id="$projectId"
                            />
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <hr>
</div>
