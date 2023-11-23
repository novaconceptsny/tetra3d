<div>
    @foreach($surfaces as $surface)
        <livewire:surface.surface-row :layout-id="$layout->id" :surface="$surface" wire:key="{{ $surface->id }}"/>
    @endforeach
</div>
