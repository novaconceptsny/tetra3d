@foreach($spots as $_spot)
    <div class="rounded p-3 mb-3">
        <h5>
            {{ __('Spot ') . $_spot->id }} ({{ $_spot->name }})
            <x-backend::inputs.switch
                col="col-12 mb-3" name="navigations[{{$_spot->id}}][enabled]" label=""
                checked="{{ $spot->xml->navigations[$_spot->id]['enabled'] ?? false }}"
            />
        </h5>
        <div class="row mt-3">
            <x-backend::inputs.input
                type="hidden" name="navigations[{{$_spot->id}}][name]"
                :value="$_spot->name"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$_spot->id}}][rx]" label="rx"
                :value="$spot->xml->navigations[$_spot->id]['rx'] ?? 70"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$_spot->id}}][hlookat]" label="hlookat"
                :value="$spot->xml->navigations[$_spot->id]['hlookat'] ?? 0"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$_spot->id}}][vlookat]" label="vlookat"
                :value="$spot->xml->navigations[$_spot->id]['vlookat'] ?? 0"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$_spot->id}}][ath]" label="ath"
                :value="$spot->xml->navigations[$_spot->id]['ath'] ?? 0"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$_spot->id}}][atv]" label="atv"
                :value="$spot->xml->navigations[$_spot->id]['atv'] ?? 0"
            />
            <x-backend::inputs.input
                col="col" name="navigations[{{$_spot->id}}][scale]" label="scale"
                :value="$spot->xml->navigations[$_spot->id]['scale'] ?? '0.6'"
            />
        </div>
    </div>
    <hr>
@endforeach
