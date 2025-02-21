<{{$element}} >
<span x-data>
    @if($editing)
        <input type="text" 
            wire:model.live="value" 
            x-on:click.away="if (!confirm('All layouts of this tour will have updated surface. Proceed?')) { 
                $wire.$set('editing', false);
                $wire.$set('value', '{!! $originalValue !!}');
            } else { 
                $wire.updateValue() 
            }" 
            @keydown.enter="if (!confirm('All layouts of this tour will have updated surface. Proceed?')) { 
                $wire.$set('editing', false);
                $wire.$set('value', '{!! $originalValue !!}');
            } else { 
                $wire.updateValue() 
            }"
        >
        @error('title') <span class="error">{{ $message }}</span> @enderror
    @else
        {{ $value }}
        @can($permission)
            <i class="fal fa-pencil" style="font-size: 0.65em; cursor: pointer" wire:click="$set('editing', true)"></i>
        @endcan
    @endif
</span>
</{{$element}}>
