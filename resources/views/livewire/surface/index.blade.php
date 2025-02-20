<div>
    <h6 class="mb-4">List view</h6>

    <div class="row g-4">
        @foreach($surfaces as $surface)
            <div class="col-6 col-md-4" wire:key="surface_{{$surface->id}}">
                @if($surface->states->count() > 0)
                    @php $activeState = $surface->states->firstWhere('active', true) ?? $surface->states->first() @endphp
                    <a href="{{ route('surfaces.show', [$surface, 'layout_id' => $layout->id, 'surface_state_id' => $activeState->id, 'return_to_versions' => true]) }}" class="me-1">
                @else
                    <a href="{{ route('surface-states.create', ['surface' => $surface, 'layout_id' => $layout->id, 'return_to_versions' => true]) }}" class="me-1">
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
                        @field-saved="$dispatch('surface-name-updated')"
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

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('surface-name-updated', function() {
                Swal.fire({
                    title: 'Warning',
                    text: 'All layouts of this tour will have updated surface.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Proceed',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If user clicks "Proceed", the change is already saved
                        // You can add additional actions here if needed
                    } else {
                        // If user clicks "Cancel", you might want to revert the change
                        // You'll need to implement this functionality if needed
                        Livewire.dispatch('revertSurfaceName');
                    }
                });
            });
        });
    </script>
@endsection
