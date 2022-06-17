<div>
    @foreach($overlays as $index => $overlay)
        <div class="row mt-2" wire:key="{{$index}}">
            <x-backend::inputs.input
                col="col" name="overlays[{{$index}}][ath]" value="{{ $overlay['ath'] }}" label="ath"
            />
            <x-backend::inputs.input
                col="col" name="overlays[{{$index}}][atv]" value="{{ $overlay['atv'] }}" label="atv"
            />
            <x-backend::inputs.input
                col="col" name="overlays[{{$index}}][scale]" value="{{ $overlay['scale'] }}" label="scale"
            />
            <x-backend::inputs.input
                col="col" name="overlays[{{$index}}][zorder]" value="{{ $overlay['zorder'] }}" label="zorder"
            />

            <div class="col-2 text-end">
                <button type="button" class="btn btn-danger mt-3"
                        wire:click.prevent="remove({{$index}})">
                    <i class="la la-trash-o"></i>{{ __('Delete') }}
                </button>
            </div>
        </div>
    @endforeach

    <div class="form-group mt-5 text-end">
        <button type="button" class="btn btn-primary"
                wire:click.prevent="add()">
            <i class="la la-plus"></i>{{ __('Add Overlay') }}
        </button>
    </div>
</div>
