<div>
    @if($selectedMap)
        <div class="tour-map-container" >
            <div class="me-2" style="width: 250px">
                <ul class="list-group">
                    @foreach($tour->maps as $map)
                        <a href="javascript:void(0)" class="list-group-item list-group-item-action {{ $map->id == $selectedMap->id ? 'active' : '' }}"
                           wire:click="selectMap({{ $map }})" wire:loading.class="disabled">{{ $map->name }}
                        </a>
                    @endforeach
                </ul>
            </div>
            <div class="floorPlan tour-map" x-intersect="setMapScale" wire:loading.remove
                 defaultwidth="{{$selectedMap->width}}" defaultheight="{{$selectedMap->height}}"
                 style="background-image:url('{{ $selectedMap->getFirstMediaUrl('image') }}')"
            >

                @php
                    $parameters = [
                        $tour,
                         'layout_id' => $layoutId,
                         'shared' => Route::is('shared-tours.show'),
                         'shared_tour_id' => $shared_tour_id ?? null
                    ]
                @endphp

                @foreach($selectedMap->spots as $spot)
                    <a href="{{ route('tours.show', array_merge($parameters, ['spot_id' => $spot->id]) )}}">
                        <div class="pin" top="{{ $spot->pivot->y }}" left="{{ $spot->pivot->x }}"
                             style="top: {{ $spot->pivot->y }}px; left: {{ $spot->pivot->x }}px;">
                        </div>
                    </a>
                @endforeach
            </div>
            <div wire:loading class="bg-light rounded-2" style="height: {{$selectedMap->height}}px; width: {{$selectedMap->width}}px"></div>
        </div>
    @else
        <div >
            <p class="text-center mb-0">Map not available</p>
        </div>
    @endif
</div>
