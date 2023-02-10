<div class="row">
    <x-loader wire:target="changeActiveState" message="Switching current state..."/>

    <div class="col-12">
        <h4><livewire:editable-field :model="$surface" field="name"/></h4>
    </div>
    <div class="row">
        <x-surface_state.add-new :surface="$surface" :project-id="$projectId"/>
        @foreach($surface->states as $state)
            <div class="col-3">
                <div class="card p-0 {{ $state->active ? 'shadow border border-2 border-success' : 'border' }}">
                    @if(!$state->active)
                        <a href="javascript:void(0)" class="overlay__link" wire:click="changeActiveState({{ $state->id }})"></a>
                    @endif
                    <div class="card-img">
                        <img
                            src="{{ $state->getFirstMediaUrl('thumbnail') }}"
                            alt="ver-card-img"
                            class="w-100 h-100 p-0 m-0"
                        />
                    </div>
                    <x-surface_state.actions
                        :surface="$surface" :state="$state"
                        :project-id="$projectId"
                    />
                </div>
            </div>
        @endforeach
    </div>
    <x-separator-line/>
</div>
