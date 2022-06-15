@foreach($spot->surfaces as $surface)
    <div class="rounded p-3 mb-3">
        <h5>{{ __('Surface ') . $loop->iteration }}</h5>
        <div class="row">
            <x-backend::inputs.input
                col="col" name="surfaces[{{$surface->id}}][click][name]" label="Name"
                :value="$spot->xml->surfaces[$surface->id]['click']['name'] ?? ''"
            />
            <x-backend::inputs.select col="col" label="Style" name="surfaces[{{$surface->id}}][click][style]" :placeholder="false">
                @foreach($surface_click_styles as $surface_click_style)
                    <x-backend::inputs.select-option
                        :text="$surface_click_style"
                        :selected="$spot->xml->surfaces[$surface->id]['click']['style'] ?? ''"
                    />
                @endforeach
            </x-backend::inputs.select>
        </div>
        <livewire:xml-form.point
            :wire:key="$surface->id" :surface_id="$surface->id"
            :points="$spot->xml->surfaces[$surface->id]['click']['points'] ?? []"
        />
    </div>
    <hr>
@endforeach
