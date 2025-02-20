<div>
    <h6 class="mb-4">List view</h6>

    <div class="row g-4">
        @foreach($surfaces as $surface)
            <div class="col-6 col-md-4" wire:key="surface_{{$surface->id}}">
                <a href="{{ route('surfaces.show', [$surface, 'layout_id' => $layout->id, 'new' => 1]) }}">
                    <div class="surface-card">
                        <div class="surface-preview">
                            @if($surface->states->count() > 0)
                                @php $activeState = $surface->states->firstWhere('active', true) ?? $surface->states->first() @endphp
                                <img 
                                    src="{{ $activeState->getFirstMediaUrl('thumbnail') }}"
                                    alt="{{ $surface->name }}"
                                />
                            @endif
                        </div>
                    </div>
                </a>
                <h5>
                    <livewire:editable-field :model="$surface" field="name" wire:key="editable_field_{{$surface->id}}"/>
                </h5>
            </div>
        @endforeach
    </div>

    <style>
        .surface-card {
            transition: transform 0.2s;
        }
        
        .surface-card:hover {
            transform: scale(1.02);
        }

        .surface-preview {
            aspect-ratio: 1;
            overflow: hidden;
            background-color: #f5f5f5;
            border-radius: 4px;
            width: 100%;
            height: 300px;
        }

        .surface-preview img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>
</div>
