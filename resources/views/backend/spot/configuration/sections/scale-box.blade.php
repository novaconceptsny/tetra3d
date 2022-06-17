<div class="rounded p-3 mb-3">
    <div class="row">
        <h5>
            <span class="me-2">{{ __('Square') }}</span>
            <x-backend::inputs.switch
                col="col-12 mb-3" name="scale_box[square][enabled]" label=""
                checked="{{ $spot->xml->scale_box['square']['enabled'] ?? false }}"
            />
        </h5>
        <x-backend::inputs.input
            col="col" name="scale_box[square][ath]" label="ath"
            :value="$spot->xml->scale_box['square']['ath'] ?? ''"
        />
        <x-backend::inputs.input
            col="col" name="scale_box[square][atv]" label="atv"
            :value="$spot->xml->scale_box['square']['atv'] ?? ''"
        />
        <x-backend::inputs.input
            col="col" name="scale_box[square][scale]" label="scale"
            :value="$spot->xml->scale_box['square']['scale'] ?? ''"
        />
    </div>

    <div class="row mt-5">
        <h5>
            <span class="me-2">{{ __('Cross') }}</span>
            <x-backend::inputs.switch
                col="col-12 mb-3" name="scale_box[cross][enabled]" label=""
                checked="{{ $spot->xml->scale_box['cross']['enabled'] ?? false }}"
            />
        </h5>
        <x-backend::inputs.input
            col="col" name="scale_box[cross][ath]" label="ath"
            :value="$spot->xml->scale_box['cross']['ath'] ?? ''"
        />
        <x-backend::inputs.input
            col="col" name="scale_box[cross][atv]" label="atv"
            :value="$spot->xml->scale_box['cross']['atv'] ?? ''"
        />
        <x-backend::inputs.input
            col="col" name="scale_box[cross][scale]" label="scale"
            :value="$spot->xml->scale_box['cross']['scale'] ?? ''"
        />
    </div>
</div>
