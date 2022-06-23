<div class="rounded p-3">
    @foreach($scales as $scale => $name)
        <div class="row mb-4">
            <h5>
                <span class="me-2">{{ $name }}</span>
                <x-backend::inputs.switch
                    col="col-12 mb-3" name="scale_box[{{$scale}}][enabled]" label=""
                    checked="{{ $spot->xml->scale_box[$scale]['enabled'] ?? false }}"
                />
            </h5>
            <x-backend::inputs.input
                col="col" name="scale_box[{{$scale}}][ath]" label="ath"
                :value="$spot->xml->scale_box[$scale]['ath'] ?? 0"
            />
            <x-backend::inputs.input
                col="col" name="scale_box[{{$scale}}][atv]" label="atv"
                :value="$spot->xml->scale_box[$scale]['atv'] ?? 0"
            />
            <x-backend::inputs.input
                col="col" name="scale_box[{{$scale}}][scale]" label="scale"
                :value="$spot->xml->scale_box[$scale]['scale'] ?? '0.7'"
            />
        </div>
    @endforeach
</div>
