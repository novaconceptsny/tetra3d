<{{$element}} >
<span x-data>
    @if($editing)
        <input type="text" wire:model.live="value" x-on:click.away="$wire.updateValue()" @keydown.enter="$wire.updateValue()">
        @error('title') <span class="error">{{ $message }}</span> @enderror
    @else
        {{ $value }}
        @can($permission)
            <i class="fal fa-pencil" style="font-size: 0.65em; cursor: pointer" wire:click="$set('editing', true)"></i>
        @endcan
    @endif
</span>
</{{$element}}>
