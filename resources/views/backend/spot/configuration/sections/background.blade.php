@foreach($spot->surfaces as $surface)
    <div class="rounded p-3 mb-3">
        <h5>{{ __('Surface ') . $loop->iteration }}</h5>
        <div class="row mt-3">
            <x-backend::inputs.input
                col="col" name="surfaces[{{$surface->id}}][background][name]" label="Name"
                :value="$spot->xml->surfaces[$surface->id]['background']['name'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="surfaces[{{$surface->id}}][background][type]" label="Hotspot Type"
                :value="$spot->xml->surfaces[$surface->id]['background']['type'] ?? ''"
            />
        </div>
        <div class="row mt-3">
            <x-backend::inputs.input
                col="col" name="surfaces[{{$surface->id}}][background][scale]" label="scale"
                :value="$spot->xml->surfaces[$surface->id]['background']['scale'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="surfaces[{{$surface->id}}][background][ath]" label="ath"
                :value="$spot->xml->surfaces[$surface->id]['background']['ath'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="surfaces[{{$surface->id}}][background][atv]" label="atv"
                :value="$spot->xml->surfaces[$surface->id]['background']['atv'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="surfaces[{{$surface->id}}][background][ox]" label="ox"
                :value="$spot->xml->surfaces[$surface->id]['background']['ox'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="surfaces[{{$surface->id}}][background][oy]" label="oy"
                :value="$spot->xml->surfaces[$surface->id]['background']['oy'] ?? ''"
            />
            <x-backend::inputs.input
                col="col" name="surfaces[{{$surface->id}}][background][zorder]" label="zorder"
                :value="$spot->xml->surfaces[$surface->id]['background']['zorder'] ?? ''"
            />
        </div>
        <div class="row mt-3">
            <x-backend::inputs.input col="col" name="surfaces[{{$surface->id}}][background][main_w]" label="main_w"/>
            <x-backend::inputs.input col="col" name="surfaces[{{$surface->id}}][background][main_h]" label="main_h"/>
            <x-backend::inputs.input
                label="shared_w" col="col"
                name="surfaces[{{$surface->id}}][background][shared_w]"
            />
            <x-backend::inputs.input
                col="col" label="shared_h"
                name="surfaces[{{$surface->id}}][background][shared_h]"
            />
            <x-backend::inputs.input
                col="col" label="ox_offset"
                name="surfaces[{{$surface->id}}][background][ox_offset]"
            />
            <x-backend::inputs.input
                col="col" label="oy_offset"
                name="surfaces[{{$surface->id}}][background][oy_offset]"
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

            <x-backend::inputs.select col="col" label="Onload" name="surfaces[{{$surface->id}}][background][onloaded]"
                                      :placeholder="false">
                @foreach($surface_types as $surface_type)
                    <x-backend::inputs.select-option
                        :text="$surface_type"
                        :selected="$spot->xml->surfaces[$surface->id]['background']['onloaded'] ?? ''"
                    />
                @endforeach
            </x-backend::inputs.select>
        </div>
    </div>
    <hr>
@endforeach
