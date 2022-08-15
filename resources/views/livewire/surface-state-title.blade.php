<span x-data>
    @if($editing)
        <input type="text" wire:model="title" x-on:click.away="$wire.updateTitle()" >
        @error('title') <span class="error">{{ $message }}</span> @enderror
    @else
        {{ $title }}
        <i class="fal fa-pencil" style="font-size: 0.65em;" wire:click="$set('editing', true)"></i>
    @endif
</span>
