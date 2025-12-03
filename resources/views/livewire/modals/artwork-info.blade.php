<x-wire-elements-pro::bootstrap.modal size="fullscreen" class="artwork-info-modal">
    <x-slot name="title">
        <div class="d-flex align-items-center">
            <i class="fas fa-palette me-2"></i>
            Artwork Details
        </div>
    </x-slot>
    
    <style>
        @media (max-width: 768px) {
            .artwork-info-modal .modal-body {
                overflow-y: auto !important;
                max-height: calc(100vh - 150px) !important;
                -webkit-overflow-scrolling: touch;
                padding: 1rem;
            }
            
            .artwork-info-modal-body {
                overflow-y: auto !important;
                max-height: calc(100vh - 150px) !important;
                -webkit-overflow-scrolling: touch;
            }
            
            .artwork-info-modal .modal-content {
                height: 100vh;
                display: flex;
                flex-direction: column;
            }
            
            .artwork-info-modal .modal-header {
                flex-shrink: 0;
            }
            
            .artwork-info-modal .artwork-info-close-button {
                flex-shrink: 0;
                position: sticky;
                bottom: 0;
                background: white;
                z-index: 10;
                padding: 1rem;
                margin-top: 1rem;
                border-top: 1px solid #dee2e6;
            }
        }
    </style>
    
    <div class="artwork-info-modal-body" style="overflow-y: auto; max-height: calc(100vh - 200px); -webkit-overflow-scrolling: touch;">
        @if($artwork)
            <div class="row g-4">
                <!-- Left Section: Artwork Image -->
                <div class="col-md-5 col-12">
                    @if($artwork->image_url)
                        <img src="{{ $artwork->image_url }}" class="img-fluid rounded shadow-sm artwork-info-image" alt="{{ $artwork->name }}" style="width: 100%; height: auto; object-fit: contain;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center artwork-info-image" style="height: 400px; min-height: 300px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif
                </div>
                
                <!-- Right Section: Artwork Information -->
                <div class="col-md-7 col-12">
                    <div class="artwork-info-section">
                        <h5 class="mb-4">Artwork Information</h5>
                        <div class="artwork-info-list">
                            <div class="info-item mb-3">
                                <span class="info-label fw-bold me-2">Name:</span>
                                <span class="info-value">{{ $artwork->name }}</span>
                            </div>
                            @if($artwork->artist)
                                <div class="info-item mb-3">
                                    <span class="info-label fw-bold me-2">Artist:</span>
                                    <span class="info-value">{{ $artwork->artist }}</span>
                                </div>
                            @endif
                            @if($artwork->type)
                                <div class="info-item mb-3">
                                    <span class="info-label fw-bold me-2">Type:</span>
                                    <span class="info-value">{{ $artwork->type }}</span>
                                </div>
                            @endif
                            @if($artwork->collection)
                                <div class="info-item mb-3">
                                    <span class="info-label fw-bold me-2">Collection:</span>
                                    <span class="info-value">{{ $artwork->collection->name }}</span>
                                </div>
                            @endif
                            <div class="info-item mb-3">
                                <span class="info-label fw-bold me-2">Dimensions:</span>
                                <span class="info-value">
                                    @if(isset($artwork->data['height_inch']) && isset($artwork->data['width_inch']))
                                        {{ $artwork->data['height_inch'] }}" × {{ $artwork->data['width_inch'] }}"
                                    @elseif($artwork->dimensions)
                                        {{ $artwork->dimensions }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="info-item mb-3">
                                <span class="info-label fw-bold me-2">Unit:</span>
                                <span class="info-value">{{ $artwork->original_unit ?? 'cm' }}</span>
                            </div>
                            <div class="info-item mb-3">
                                <span class="info-label fw-bold me-2">Description:</span>
                                <div class="info-value">
                                    @if($artwork->description)
                                        <textarea 
                                            class="form-control" 
                                            rows="5" 
                                            readonly 
                                            style="resize: none; background-color: #f8f9fa; cursor: default;"
                                        >{{ $artwork->description }}</textarea>
                                    @else
                                        <span class="text-muted">No description available</span>
                                    @endif
                                </div>
                            </div>
                        </div>
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

    <div class="d-flex justify-content-center mt-3 mb-3 artwork-info-close-button">
        <button class="btn btn-primary c-btn-primary" type="button" wire:click="$dispatch('modal.close')" style="min-height: 44px; min-width: 100px; touch-action: manipulation; -webkit-tap-highlight-color: rgba(0,0,0,0.1);">
            {{ __('Close') }}
        </button>
    </div>
</x-wire-elements-pro::bootstrap.modal> 