<div class="row">
    <div class="col-3">
        <div class="list-group">
            @foreach($sections as $form => $section)
                <button type="button"
                        class="list-group-item list-group-item-action {{ $form == $activeForm ? 'active' : '' }}"
                        wire:click="$set('activeForm', '{{$form}}')">{{ $section['name'] }}
                </button>
            @endforeach
        </div>
    </div>
    <div class="col-9">
        <div class="card">
            <div class="card-body">
                <form class="gap-2" action="{{ route('backend.spot-configuration.update', $spot) }}" method="post">
                    @csrf
                    @method('put')
                    @foreach($sections as $form => $section)
                        <div class="{{ $activeForm == $form ? '' : 'd-none' }}">
                            <h5 class="mb-3 mt-0">{{ $section['name'] }}</h5>
                            @include("backend.spot.configuration.sections.{$form}")
                        </div>
                    @endforeach

                    <div class="form-group mt-5 float-end">
                        <button type="submit" class="btn btn-success">
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
