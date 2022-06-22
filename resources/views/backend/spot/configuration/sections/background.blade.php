@foreach($spot->surfaces as $surface)
    <div class="rounded p-3 mb-3">
        <h5>{{ "surface_{$surface->id}" }}</h5>
        <div class="row mt-3">
            <x-backend::inputs.input
                col="col" name="surfaces[{{$surface->id}}][background][scale]" label="scale"
                :value="$spot->xml->surfaces[$surface->id]['background']['scale'] ?? 1"
            />
            <x-backend::inputs.input
                col="col" name="surfaces[{{$surface->id}}][background][ath]" label="ath"
                :value="$spot->xml->surfaces[$surface->id]['background']['ath'] ?? 0"
            />
            <x-backend::inputs.input
                col="col" name="surfaces[{{$surface->id}}][background][atv]" label="atv"
                :value="$spot->xml->surfaces[$surface->id]['background']['atv'] ?? 0"
            />
            <x-backend::inputs.input
                col="col" name="surfaces[{{$surface->id}}][background][ox]" label="ox"
                :value="$spot->xml->surfaces[$surface->id]['background']['ox'] ?? 0"
            />
            <x-backend::inputs.input
                col="col" name="surfaces[{{$surface->id}}][background][oy]" label="oy"
                :value="$spot->xml->surfaces[$surface->id]['background']['oy'] ?? 0"
            />
            <x-backend::inputs.input
                col="col" name="surfaces[{{$surface->id}}][background][zorder]" label="zorder"
                :value="$spot->xml->surfaces[$surface->id]['background']['zorder'] ?? 21"
            />
        </div>
        <div class="row mt-3">
            <x-backend::inputs.input
                col="col" label="main_w"
                name="surfaces[{{$surface->id}}][background][main_w]"
                :value="$spot->xml->surfaces[$surface->id]['background']['main_w'] ?? 0"
            />
            <x-backend::inputs.input
                col="col" label="main_h"
                name="surfaces[{{$surface->id}}][background][main_h]"
                :value="$spot->xml->surfaces[$surface->id]['background']['main_h'] ?? 0"
            />
            <x-backend::inputs.input
                label="shared_w" col="col"
                name="surfaces[{{$surface->id}}][background][shared_w]"
                :value="$spot->xml->surfaces[$surface->id]['background']['shared_w'] ?? 0"
            />
            <x-backend::inputs.input
                col="col" label="shared_h"
                name="surfaces[{{$surface->id}}][background][shared_h]"
                :value="$spot->xml->surfaces[$surface->id]['background']['shared_h'] ?? 0"
            />
            <x-backend::inputs.input
                col="col" label="ox_offset"
                name="surfaces[{{$surface->id}}][background][ox_offset]"
                :value="$spot->xml->surfaces[$surface->id]['background']['ox_offset'] ?? 0"
            />
            <x-backend::inputs.input
                col="col" label="oy_offset"
                name="surfaces[{{$surface->id}}][background][oy_offset]"
                :value="$spot->xml->surfaces[$surface->id]['background']['oy_offset'] ?? 0"
            />

        </div>
        <div class="row mt-3">
            <x-backend::inputs.select col="col" label="Select" name="surfaces[{{$surface->id}}][background][select]"
                                      :placeholder="false">
                <x-backend::inputs.select-option
                    text="h"
                    :selected="$spot->xml->surfaces[$surface->id]['background']['select'] ?? ''"
                />
                <x-backend::inputs.select-option
                    text="w"
                    :selected="$spot->xml->surfaces[$surface->id]['background']['select'] ?? ''"
                />
            </x-backend::inputs.select>

            <x-backend::inputs.select
                col="col" label="Load Surface" :placeholder="false"
                name="surfaces[{{$surface->id}}][background][onloaded]"
            >
                @foreach($surface_types as $surface_type => $surface_name)
                    <x-backend::inputs.select-option
                        :text="$surface_name"
                        :value="$surface_type"
                        :selected="$spot->xml->surfaces[$surface->id]['background']['onloaded'] ?? ''"
                    />
                @endforeach
            </x-backend::inputs.select>

            <div class="col-12 mt-3">
                <h5>{{ __('Shared Image') }}</h5>
                <x-media-library-attachment name="surfaces[{{$surface->id}}][shared_image]" rules="max:102400" />
            </div>
        </div>
    </div>
    <hr>
@endforeach
