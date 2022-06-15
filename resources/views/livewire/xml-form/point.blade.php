<div>
    @foreach($points as $index => $point)
        <div class="row mt-2" wire:key="{{$index}}">
            <x-backend::inputs.input
                col="col-5" name="surfaces[{{$surface_id}}][click][points][{{$index}}][ath]"
                value="{{ $point['ath'] }}"
                label="ath"
            />
            <x-backend::inputs.input
                col="col-5" name="surfaces[{{$surface_id}}][click][points][{{$index}}][atv]" value="{{ $point['atv'] }}"
                label="atv"
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
            <i class="la la-plus"></i>{{ __('Add Point') }}
        </button>
    </div>
</div>
