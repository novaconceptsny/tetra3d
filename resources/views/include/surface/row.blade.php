<h4 class="font-secondary section__title">
    {{ $surface->name }}
</h4>
<div class="inner__field row mb-4">
    <div class="col-md-6 col-lg-3 mb-3 ">
        <div href="#" class="card overflow-hidden">
            <a href="#" class="overlay__link"></a>
            <div class="card-body">
                <img
                    src="{{ $surface->getFirstMediaUrl('background') }}"
                    alt="image"
                    class="bg__image"
                />
                <a href="{{ route('surfaces.show', [$surface, 'project_id' => $project->id, 'new' => 1]) }}"
                   class="add__btn">
                    <span class="plus__icon">
                        <x-svg.plus width="35" height="35"/>
                    </span>
                    Add New Option
                </a>
            </div>
        </div>
    </div>
    @foreach($surface->states as $state)
        @include('include.surface.state')
    @endforeach
</div>
<hr>
