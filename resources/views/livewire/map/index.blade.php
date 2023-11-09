<div class="row">
    <div class="col-3">
        <div class="list-group">
            @foreach($tour->maps as $map)
                <button
                    class="list-group-item list-group-item-action {{ $map->id == $selectedMap->id ? 'active' : '' }}"
                    type="button" wire:click="selectMap({{ $map->id }})">{{ $map->name }}
                </button>
            @endforeach
                <button class="list-group-item list-group-item-action {{ $creatingNewMap ? 'active' : '' }}"
                        type="button" wire:click="createNew">
                    <span class="me-1">Add New Map</span>
                    <i class="fal fa-plus-square text-primary"></i>
                </button>
        </div>
    </div>
    <div class="col-9">
        <div class="row g-3 ">
            <x-backend::inputs.text name="selectedMap.name" wire:model.live="selectedMap.name"/>
            <x-backend::inputs.text col="col-6" name="selectedMap.width" wire:model.live="selectedMap.width"/>
            <x-backend::inputs.text col="col-6" name="selectedMap.height" wire:model.live="selectedMap.height"/>
            <div class="col-12">
                <h5>{{ __('Map Image') }}</h5>

                <x-backend::media-attachment
                    name="mapImage" rules="max:102400"
                    :media="$selectedMap?->getFirstMedia('image')"
                />
            </div>

            @if($selectedMap)
                <div class="row g-3 mt-2">
                    <div class="col-12"><h5>{{ __('Spots') }}</h5></div>
                    @foreach($tour->spots as $spot)
                        <div class="row g-2">
                            <x-backend::inputs.text
                                col="col-6" name='{{ "spots.{$spot->id}.x" }}'
                                wire:model.live="spots.{{ $spot->id }}.x" label='{{ "{$spot->friendly_name} X" }}'
                            />
                            <x-backend::inputs.text
                                col="col-6" name='{{ "spots.{$spot->id}.y" }}'
                                wire:model.live="spots.{{ $spot->id }}.y" label='{{ "{$spot->friendly_name} Y" }}'
                            />
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="text-end">
                @if(!$creatingNewMap)
                <button class="btn btn-danger me-2" type="button" wire:click="$dispatch('showModal', {alias: 'modals.confirm', params: @json($deleteOptions)})">
                    {{ __('Delete') }}
                </button>
                @endif
                <button class="btn btn-primary" wire:click="update" type="button">
                    {{ __('Update') }}
                </button>
            </div>
        </div>
    </div>
</div>
