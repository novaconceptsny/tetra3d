@foreach($spots as $spot)
    <div class="rounded p-3 mb-3">
        <h5>{{ __('Spot ') . $spot->id }}</h5>
        <div class="row mt-3">
            <x-backend::inputs.input
                col="col" name="navigations[{{$spot->id}}][rx]" label="rx"
                :value="$spot->xml->navigations[$spot->id]['rx'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$spot->id}}][hlookat]" label="hlookat"
                :value="$spot->xml->navigations[$spot->id]['hlookat'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$spot->id}}][vlookat]" label="vlookat"
                :value="$spot->xml->navigations[$spot->id]['vlookat'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$spot->id}}][ath]" label="ath"
                :value="$spot->xml->navigations[$spot->id]['ath'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$spot->id}}][atv]" label="atv"
                :value="$spot->xml->navigations[$spot->id]['atv'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$spot->id}}][scale]" label="scale"
                :value="$spot->xml->navigations[$spot->id]['scale'] ?? ''"
            />
        </div>
    </div>
    <hr>
@endforeach
