<div>
    @foreach($overlays as $index => $overlay)
        <h5>
            {{ "Overlay {$index}" }}
            <x-backend::inputs.switch
                col="col-12 mb-3" name="overlays[{{$index}}][enabled]" label=""
                checked="{{ $overlay['enabled'] ?? false }}"
            />
        </h5>
        <div class="row mt-2" wire:key="{{$index}}">
            <x-backend::inputs.input
                name="overlays[{{$index}}][uuid]" value="{{ $overlay['uuid'] }}" type="hidden"
            />
            <x-backend::inputs.input
                col="col" name="overlays[{{$index}}][ath]" value="{{ $overlay['ath'] ?? 0 }}" label="ath"
            />
            <x-backend::inputs.input
                col="col" name="overlays[{{$index}}][atv]" value="{{ $overlay['atv'] ?? 0 }}" label="atv"
            />
            <x-backend::inputs.input
                col="col" name="overlays[{{$index}}][scale]" value="{{ $overlay['scale'] ?? 1 }}" label="scale"
            />
            <x-backend::inputs.input
                col="col" name="overlays[{{$index}}][zorder]" value="{{ $overlay['zorder'] ?? 20 }}" label="zorder"
            />

            <div class="col-12">
                <h5>{{ __('Overlay Image') }}</h5>
                <x-backend::media-attachment
                    name="overlays[{{$index}}][image]" rules="max:102400"
                    :media="$spot?->getFirstMedia('overlays',  ['uuid' => $overlay['uuid']])"
                />
            </div>

            <div class="col-12 text-end">
                <button type="button" class="btn btn-danger mt-3"
                        wire:loading.attr="disabled"
                        wire:click.prevent="remove({{$index}})">
                    <i class="la la-trash-o"></i>{{ __('Delete') }}
                </button>
            </div>
        </div>
    @endforeach

    <div class="form-group mt-5 text-end">
        <button type="button" class="btn btn-primary"
                wire:loading.attr="disabled"
                wire:click.prevent="add()">
            <i class="la la-plus"></i>{{ __('Add Overlay') }}
        </button>
    </div>
</div>
