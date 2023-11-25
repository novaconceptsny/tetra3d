<div>
    <div class="mb-4 layout-intro">
        <span>{{ $layout->name }}</span>
        <span>{{ $layout->tour->name }}</span>
        <span>{{ $layout->created_at->format('M d, Y') }}</span>
    </div>

    @foreach($surfaces as $surface)
        <div class="row">
            <x-loader wire:target="changeActiveState" message="Switching current state..."/>

            <div class="col-12">
                <h4><livewire:editable-field :model="$surface" field="name"/></h4>
            </div>
            <div class="row">
                <x-surface_state.add-new :surface="$surface" :layout-id="$layout->id"/>
                @foreach($surface->states as $state)
                    <div class="col-3" wire:key="surface_{{$surface->id}}_card">
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
                                :layout-id="$layout->id"
                            />
                        </div>
                    </div>
                @endforeach
            </div>
            <x-separator-line/>
        </div>

        {{--<livewire:surface.surface-row :layout-id="$layout->id" :surface="$surface" wire:key="{{ $surface->id }}"/>--}}
    @endforeach
</div>
