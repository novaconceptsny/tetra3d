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
                <h4>
                    <livewire:editable-field :model="$surface" field="name" wire:key="editable_field_{{$surface->id}}"/>
                </h4>
            </div>
            <div class="row">
                <x-surface_state.add-new :surface="$surface" :layout-id="$layout->id"/>
                @foreach($surface->states as $state)
                    <div class="col-3" wire:key="surface_{{$surface->id}}_card">
                        <div class="card p-0 {{ $state->active ? 'border' : '' }}">
                            <div class="card-img" wire:click="changeActiveState({{ $state->id }})">
                                <img
                                    src="{{ $state->getFirstMediaUrl('thumbnail') }}"
                                    alt="ver-card-img"
                                    class="w-100 h-100 p-0 m-0"
                                />
                            </div>
                            <div class="card-body">
                                <div class="main-card-body">
                                    <div class="text">
                                        <h6>{{ $state->name }}</h6>
                                        <p>{{ $state->user->name }} | {{ $state->created_at->format('m/d/Y') }}</p>
                                    </div>
                                    <div class="icon">
                                        <a href="{{ route('surfaces.show', [$surface, 'layout_id' => $layout->id, 'surface_state_id' => $state->id, 'return_to_versions' => true]) }}"
                                           class="me-1">
                                            <i class="fal fa-pencil text-dark"></i>
                                        </a>
                                        <a href="#" class=" me-1" wire:click="removeSurfaceState({{ $state->id }})" wire:key="remove_{{$state->id}}">
                                            <i class="fal fa-trash text-dark"></i>
                                        </a>
                                        <livewire:likes :likeable="$state" wire:key="{{ $state->id }}"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <x-separator-line/>
        </div>

    @endforeach
</div>
