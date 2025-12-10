@foreach($spots as $_spot)
    @php
        // Check if current spot has navigation data for target spot
        $currentNav = $spot->xml->navigations[$_spot->id] ?? [];
        $currentHlookat = $currentNav['hlookat'] ?? null;
        
        // If not set, check reverse navigation (target spot -> current spot)
        // and use negated hlookat value
        if ($currentHlookat === null) {
            $reverseNav = $_spot->xml->navigations[$spot->id] ?? [];
            $reverseHlookat = $reverseNav['hlookat'] ?? null;
            if ($reverseHlookat !== null) {
                $currentHlookat = -$reverseHlookat;
            }
        }
        
        // Default to 0 if still not set
        $hlookatValue = $currentHlookat ?? 0;
    @endphp
    <div class="rounded p-3 mb-3">
        <h5>
            {{ $_spot->friendly_name }}
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
                :value="$hlookatValue"
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
