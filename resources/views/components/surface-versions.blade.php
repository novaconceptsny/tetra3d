@props([
    'surface',
    'project',
    'current_state_id'
])
<div {{ $attributes->class(['surface__sidebar card show']) }}>
    <div class="card-body">
        <div class="top">
            <div class="left">
                <h2 class="surface__title">{{ $surface->name }} </h2>
            </div>
            <div class="right">
                <a href="{{ route('surfaces.show', [$surface, 'project_id' => $project->id, 'new' => 1]) }}" class="btn">
                    <x-svg.plus width="24" height="24"/>
                    Add
                </a>
            </div>
        </div>
        <button class="btn sidebar__trigger">
            <x-svg.angles-right color="white"/>
        </button>
        <div class="tetra__accordion">
            @foreach($surface->states as $state)
                @php($is_active = $state->id == $current_state_id)
                <div class="accordion__item {{ $is_active ? 'active' : ''}}" style="cursor: unset" >
                    <x-surface_state.actions
                        :surface="$surface"
                        :state="$state"
                        :project-id="$project->id"
                        :comments="true"
                    />
                    <div class="accordion__body {{ $is_active ? 'd-block' : '' }}">
                        <livewire:comments :commentable="$state"/>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
