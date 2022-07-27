<div class="row g-3">
    @foreach($actions as $name => $action)
        <x-backend::inputs.radio col="col" :label="$action['label']">
            @foreach($action['options'] as $value => $label)
                <x-backend::inputs.radio-option
                    name="quick_actions[{{$name}}]" label="{{$label}}" value="{{$value}}"
                    :selected="$spot->xml->quick_actions[$name] ?? 'default'"
                />
            @endforeach
        </x-backend::inputs.radio>
    @endforeach
</div>

