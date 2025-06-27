<x-wire-elements-pro::bootstrap.modal size="lg" style="width: 800px; height: 500px;">
    <x-slot name="title">
        <div class="d-flex align-items-center">
            <i class="fas fa-palette me-2"></i>
            Artwork Information
        </div>
    </x-slot>
    
    <div class="d-flex flex-column align-items-center justify-content-center">
        @if($artwork)
            <div class="d-flex flex-column align-items-center justify-content-center gap-4">
                <div class="col-md-12">
                    @if($artwork->image_url)
                        <img src="{{ $artwork->image_url }}" class="img-fluid rounded shadow-sm" alt="{{ $artwork->name }}" style="max-height: 800px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-12">
                    <div class="card-body p-0">
                        <h5 class="card-title text-primary mb-3">{{ $artwork->name }}</h5>
                        <ul class="list-unstyled mb-0">
                            @if($artwork->artist)
                                <li class="mb-2">
                                    <span class="fw-bold me-2 text-muted">Artist:</span> 
                                    <span class="text-dark">{{ $artwork->artist }}</span>
                                </li>
                            @endif
                            @if($artwork->type)
                                <li class="mb-2">
                                    <span class="fw-bold me-2 text-muted">Type:</span> 
                                    <span class="text-dark">{{ $artwork->type }}</span>
                                </li>
                            @endif
                            <li class="mb-2">
                                <span class="fw-bold me-2 text-muted">Dimensions:</span> 
                                <span class="text-dark">
                                    @if(isset($artwork->data['height_inch']) && isset($artwork->data['width_inch']))
                                        {{ $artwork->data['height_inch'] }}" × {{ $artwork->data['width_inch'] }}"
                                    @elseif($artwork->dimensions)
                                        {{ $artwork->dimensions }}
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </span>
                            </li>
                            @if($artwork->collection)
                                <li class="mb-2">
                                    <span class="fw-bold me-2 text-muted">Collection:</span> 
                                    <span class="text-dark">{{ $artwork->collection->name }}</span>
                                </li>
                            @endif
                            @if(isset($artwork->data['description']))
                                <li class="mb-2">
                                    <span class="fw-bold me-2 text-muted">Description:</span> 
                                    <span class="text-dark">{{ $artwork->data['description'] }}</span>
                                </li>
                            @endif
                            @if(isset($artwork->data['year']))
                                <li class="mb-2">
                                    <span class="fw-bold me-2 text-muted">Year:</span> 
                                    <span class="text-dark">{{ $artwork->data['year'] }}</span>
                                </li>
                            @endif
                            @if(isset($artwork->data['medium']))
                                <li class="mb-2">
                                    <span class="fw-bold me-2 text-muted">Medium:</span> 
                                    <span class="text-dark">{{ $artwork->data['medium'] }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                    <strong>Artwork not found.</strong> The requested artwork information could not be retrieved.
                </div>
            </div>
        @endif
    </div>

    <div class="d-flex justify-content-end">
        <button class="btn btn-primary c-btn-primary" type="button" wire:click="closeModal">
            <i class="fas fa-times me-1"></i>
            {{ __('Close') }}
        </button>
    </div>
</x-wire-elements-pro::bootstrap.modal> 