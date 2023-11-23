<x-wire-elements-pro::bootstrap.modal>
    @if($selectedMap)
        <div class="tour-map-container" x-data="{ scale: 1, pinVisible: true }" x-init="setMapScale">
            <div class="me-2" style="width: 250px">
                <ul class="list-group">
                    @foreach($tour->maps as $map)
                        <a href="javascript:void(0)" class="list-group-item list-group-item-action {{ $map->id == $selectedMap->id ? 'active' : '' }}"
                           wire:click="selectMap({{ $map }})" wire:loading.class="disabled">{{ $map->name }}
                        </a>
                    @endforeach
                </ul>
            </div>
            <div class="floorPlan tour-map" x-ref="floorPlan"
                 defaultwidth="{{$selectedMap->width}}" defaultheight="{{$selectedMap->height}}"
                 style="background-image:url('{{ $selectedMap->getFirstMediaUrl('image') }}')"
                 x-bind:style="{ transform: 'scale(' + scale + ')' }"
            >

                @php
                    $parameters = [
                        $tour,
                         'project_id' => isset($project) ? $project?->id : null,
                         'shared' => Route::is('shared-tours.show'),
                         'shared_tour_id' => $shared_tour_id ?? null
                    ]
                @endphp

                @foreach($selectedMap->spots as $spot)
                    <a href="{{ route('tours.show', array_merge($parameters, ['spot_id' => $spot->id]) )}}">
                        <div
                            x-data="{
                                top: @js($spot->pivot->y),
                                left: @js($spot->pivot->x),
                            }"
                            class="pin" top="{{ $spot->pivot->y }}" left="{{ $spot->pivot->x }}"
                            :style="{ top: ((top * scale) - 40) + 'px', left: (left * (scale) - 20) + 'px' }">
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div >
            <p class="text-center mb-0">Map not available</p>
        </div>
    @endif

</x-wire-elements-pro::bootstrap.modal>
