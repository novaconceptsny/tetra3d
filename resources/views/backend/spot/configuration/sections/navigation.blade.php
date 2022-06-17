@foreach($spots as $spot)
    <div class="rounded p-3 mb-3">
        <h5>{{ __('Spot ') . $spot->id }}</h5>
        <div class="row mt-3">
            <x-backend::inputs.input
                col="col" name="navigations[{{$spot->id}}][ath]" label="ath"
                :value="$spot->xml->navigations[$spot->id]['background']['ath'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$spot->id}}][atv]" label="atv"
                :value="$spot->xml->navigations[$spot->id]['background']['atv'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$spot->id}}][scale]" label="scale"
                :value="$spot->xml->navigations[$spot->id]['background']['scale'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$spot->id}}][zorder]" label="zorder"
                :value="$spot->xml->surfaces[$spot->id]['background']['zorder'] ?? ''"
            />
        </div>
    </div>
    <hr>
@endforeach
