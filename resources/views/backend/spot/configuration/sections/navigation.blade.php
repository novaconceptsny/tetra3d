@foreach($spots as $_spot)
    <div class="rounded p-3 mb-3">
        <h5>{{ __('Spot ') . $_spot->id }}</h5>
        <div class="row mt-3">
            <x-backend::inputs.input
                col="col" name="navigations[{{$_spot->id}}][rx]" label="rx"
                :value="$spot->xml->navigations[$_spot->id]['rx'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$_spot->id}}][hlookat]" label="hlookat"
                :value="$spot->xml->navigations[$_spot->id]['hlookat'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$_spot->id}}][vlookat]" label="vlookat"
                :value="$spot->xml->navigations[$_spot->id]['vlookat'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$_spot->id}}][ath]" label="ath"
                :value="$spot->xml->navigations[$_spot->id]['ath'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$_spot->id}}][atv]" label="atv"
                :value="$spot->xml->navigations[$_spot->id]['atv'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$_spot->id}}][scale]" label="scale"
                :value="$spot->xml->navigations[$_spot->id]['scale'] ?? ''"
            />
        </div>
    </div>
    <hr>
@endforeach
