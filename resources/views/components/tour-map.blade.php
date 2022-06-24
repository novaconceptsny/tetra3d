<div class="tour-map-container">
    <div class="floorPlan tour-map" name=""
         defaultwidth="{{$tour->map->width}}" defaultheight="{{$tour->map->height}}"
         style="background-image:url('{{ $tour->map->getFirstMediaUrl('image') }}')"
    >

        @foreach($tour->map->spots as $spot)
            <a href="{{ route('tours.show', [$tour, 'spot_id' => $spot->id]) }}">
                <div class="pin" top="{{ $spot->pivot->y }}" left="{{ $spot->pivot->x }}"
                     style="top: {{ $spot->pivot->y }}px; left: {{ $spot->pivot->x }}px;">
                </div>
            </a>
        @endforeach
    </div>
</div>
