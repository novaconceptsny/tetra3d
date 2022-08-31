<div>
    @foreach($surfaces as $surface)
        <livewire:surface.surface-row :project-id="$project->id" :surface="$surface" wire:key="{{ $surface->id }}"/>
    @endforeach
</div>
