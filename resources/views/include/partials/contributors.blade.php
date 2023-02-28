<div class="profile__group">
    @foreach($project->contributors->take(4) as $contributor)
        <a href="#" class="profile">
            <img
                src="{{ $contributor->avatar_url }}"
                alt="{{ $contributor->name }}"
                width="32"
                height="auto"
                class="photo"
            />
            <span class="profile__tooltip"> {{ $contributor->name }} </span>
        </a>
    @endforeach

    @php($moreContributors = $project->contributors->skip(4))

    @if($moreContributors->count())
            <a href="#" class="profile" style="z-index: 999">
                <span class="number__count font-primary">+{{ $moreContributors->count() }}</span>
                <span class="profile__tooltip">
                    @foreach($moreContributors as $contributor)
                        <span>{{ $contributor->name }}</span><br>
                    @endforeach
                </span>
            </a>
    @endif
</div>
