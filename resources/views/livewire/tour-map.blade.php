@if($selectedMap)
    <div class="tour-map-container">
        <div class="me-2" style="width: 250px">
            <ul class="list-group">
                @foreach($tour->maps as $map)
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action {{ $map->id == $selectedMap->id ? 'active' : '' }}"
                       wire:click="selectMap({{ $map }})" wire:loading.class="disabled">{{ $map->name }}
                    </a>
                @endforeach
            </ul>
        </div>
        <div class="floorPlan tour-map"
             defaultwidth="{{$selectedMap->width}}" defaultheight="{{$selectedMap->height}}"
             style="background-image:url('{{ $selectedMap->getFirstMediaUrl('image') }}')"
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
                    <div class="pin" top="{{ $spot->pivot->y }}" left="{{ $spot->pivot->x }}"
                         style="top: {{ $spot->pivot->y }}px; left: {{ $spot->pivot->x }}px;">
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
