<div>
    <h6 class="mb-4">List view</h6>

    <div class="row g-4" style="justify-content: space-between; display: flex; flex-wrap: wrap;">
        @foreach($surfaces as $surface)
            <div class="col-6 col-md-4" style="width: 30%;" wire:key="surface_{{$surface->id}}">
                @if($surface->states->count() > 0)
                    @php $activeState = $surface->states->firstWhere('active', true) ?? $surface->states->first() @endphp
                    <a href="{{ route('surfaces.show', [$surface, 'layout_id' => $layout->id, 'surface_state_id' => $activeState->id, 'return_to_versions' => true]) }}" class="surface-link">
                @else
                    <a href="{{ route('surfaces.show', [$surface, 'layout_id' => $layout->id, 'new' => 1]) }}" class="surface-link">
                @endif
                    <div class="surface-card">
                        <div class="surface-preview">
                            @if($surface->states->count() > 0)
                                @php $activeState = $surface->states->firstWhere('active', true) ?? $surface->states->first() @endphp
                                <img 
                                    src="{{ $activeState->getFirstMediaUrl('thumbnail') }}"
                                    alt="{{ $surface->display_name }}"
                                />
                            @else
                                <img 
                                    src="{{ $surface->getFirstMediaUrl('background') }}"
                                    alt="{{ $surface->display_name }}"
                                />
                            @endif
                        </div>
                    </div>
                </a>
                <h5>
                    <livewire:editable-field 
                        :model="$surface" 
                        field="display_name" 
                        wire:key="editable_field_{{$surface->id}}"
                    />
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

        .surface-link {
            display: block;
            margin-bottom: 1.5rem;
            text-decoration: none;
        }

        .surface-preview {
            aspect-ratio: 1;
            overflow: hidden;
            background-color: #f5f5f5;
            border-radius: 4px;
            width: 100%;
            height: 300px;
            margin-bottom: 0.75rem;
        }

        .surface-preview img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>
</div>
