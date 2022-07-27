<div class="row">
    <div class="row">
        <x-backend::inputs.input col="col" name="view[hlookat]" label="hlookat" :value="$spot->xml->view['hlookat'] ?? 0"/>
        <x-backend::inputs.input col="col" name="view[vlookat]" label="vlookat" :value="$spot->xml->view['vlookat'] ?? 0"/>
        <x-backend::inputs.input col="col" name="view[fov]" label="fov" :value="$spot->xml->view['fov'] ?? 90"/>
        <x-backend::inputs.input col="col" name="view[fovmax]" label="fovmax" :value="$spot->xml->view['fovmax'] ?? 120"/>
    </div>

    <div class="col-12 mt-3">
        <h5>Scale Box</h5>
    </div>
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
