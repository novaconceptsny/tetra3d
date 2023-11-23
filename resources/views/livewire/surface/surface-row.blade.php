<div class="row">
    <x-loader wire:target="changeActiveState" message="Switching current state..."/>

    <div class="col-12">
        <h4><livewire:editable-field :model="$surface" field="name"/></h4>
    </div>
    <div class="row">
        <x-surface_state.add-new :surface="$surface" :layout-id="$layoutId"/>
        @foreach($surface->states as $state)
            <div class="col-3">
                <div class="card p-0 {{ $state->active ? 'shadow border border-2 border-success' : 'border' }}">
                    <div class="card-img" wire:click="changeActiveState({{ $state->id }})">
                        <img
                            src="{{ $state->getFirstMediaUrl('thumbnail') }}"
                            alt="ver-card-img"
                            class="w-100 h-100 p-0 m-0"
                        />
                    </div>
                    <x-surface_state.actions
                        :surface="$surface" :state="$state"
                        :layout-id="$layoutId"
                    />
                </div>
            </div>
        @endforeach
    </div>
    <x-separator-line/>
</div>
