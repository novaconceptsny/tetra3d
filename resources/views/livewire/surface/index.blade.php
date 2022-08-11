<div>
    @foreach($surfaces as $surface)
        <livewire:surface.surface-row :project="$project" :surface="$surface"/>
    @endforeach
</div>
