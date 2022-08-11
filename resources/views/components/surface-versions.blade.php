@props([
    'surface',
    'project'
])
<div {{ $attributes->class(['surface__sidebar card']) }}>
    <div class="card-body">
        <div class="top">
            <div class="left">
                <h2 class="surface__title">{{ $surface->name }} </h2>
                {{--<p class="date__time">Last Edited: S.Lythe | 8/1/2021</p>--}}
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
                <div class="accordion__item">
                    <div class="accordion__header">
                        <div class="left">
                            <div class="user__details">
                                <h3 class="username">{{ $state->name }}</h3>
                                <div class="tag">{{ $state->user->name }} | {{ $state->created_at->format('m/d/Y') }}</div>
                            </div>
                        </div>
                        <div class="right">
                            <div class="surface__items">
                                <a href="#" class="icon accordion__trigger">
                                    <x-svg.pen-to-square/>
                                </a>
                                <a href="#" class="icon">
                                    <x-svg.trash-can/>
                                </a>
                                <a href="#" class="icon">
                                    <x-svg.thumbs-up/>
                                </a>
                                <button type="button" class="icon arrow">
                                    <x-svg.angle-up/>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="accordion__body">
                        <livewire:comments :commentable="$state"/>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
