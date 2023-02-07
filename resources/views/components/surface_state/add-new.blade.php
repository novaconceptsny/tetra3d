@props([
    'surface',
    'projectId',
])

{{--<div class="col-3 fir-col">
    <div href="#" class="card overflow-hidden">
        <a href="#" class="overlay__link"></a>
        <div class="card-body">
            <img
                src="{{ $surface->getFirstMediaUrl('background') }}"
                alt="image"
                class="bg__image"
            />
            <a href="{{ route('surfaces.show', [$surface, 'project_id' => $projectId, 'new' => 1]) }}"
               class="add__btn">
                <span class="plus__icon">
                    <x-svg.plus width="35" height="35"/>
                </span>
                Add New Option
            </a>
        </div>
    </div>
</div>--}}
<div class="col-3 fir-col">
    <div class="card p-0">
        <a href="{{ route('surfaces.show', [$surface, 'project_id' => $projectId, 'new' => 1]) }}">
            <div class="abs-div">
                <div class="content">
                    <div class="plus p-3 rounded-circle bg-success">
                        <x-svg.plus width="35" height="35"/>
                    </div>
                    <h6>{{ __('Add New Option') }}</h6>
                </div>
            </div>
        </a>
        <div class="card-img p-0 m-0">
            <img
                src="{{ $surface->getFirstMediaUrl('background') }}"
                alt="ver-card-img"
                class="w-100 h-100 p-0 m-0"
            />
        </div>
    </div>
</div>
