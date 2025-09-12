<style>
    .artwork-img {
        cursor: grab;
        transition: all 0.2s ease;
        user-select: none;
        position: relative;
    }
    
    .artwork-img:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .artwork-img:active {
        cursor: grabbing;
    }
    
    .artwork-img.dragging {
        opacity: 0.5;
        transform: scale(0.95);
        cursor: grabbing;
        z-index: 1000;
    }
    
    .artwork-img.drag-over {
        border: 2px dashed #007bff;
        background-color: rgba(0, 123, 255, 0.1);
    }
    
    /* Add a subtle indicator that items are draggable */
    .artwork-img::before {
        content: '⋮⋮';
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 12px;
        color: #6c757d;
        opacity: 0.6;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }
    
    .artwork-img:hover::before {
        opacity: 1;
        color: #007bff;
    }
    
    .artwork-img.dragging::before {
        opacity: 0;
    }
    
    /* Improve card appearance */
    .artwork-img .card {
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }
    
    .artwork-img:hover .card {
        border-color: #007bff;
    }
    
    /* Count badge styling */
    .artwork-count-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background-color: #e91e63;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        transition: all 0.2s ease;
    }
    
    .artwork-img {
        position: relative;
    }
    
    .artwork-count-badge:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }
</style>

<script>
    // Listen for Livewire updates to refresh artwork badges
    document.addEventListener('livewire:updated', function() {
        if (typeof window.refreshArtworkBadges === 'function') {
            window.refreshArtworkBadges();
        }
    });
    
    // Listen for Livewire component initialization
    document.addEventListener('livewire:init', function() {
        if (typeof window.refreshArtworkBadges === 'function') {
            setTimeout(() => {
                window.refreshArtworkBadges();
            }, 300);
        }
    });
    
    // Also listen for DOM changes in the artwork list
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && 
                mutation.target.classList && 
                mutation.target.classList.contains('card-row')) {
                if (typeof window.refreshArtworkBadges === 'function') {
                    window.refreshArtworkBadges();
                }
            }
        });
    });
    
    // Start observing when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        const cardRow = document.querySelector('.card-row');
        if (cardRow) {
            observer.observe(cardRow, { childList: true, subtree: true });
        }
        
        // Refresh badges when artwork collection is loaded
        if (typeof window.refreshArtworkBadges === 'function') {
            setTimeout(() => {
                window.refreshArtworkBadges();
            }, 200);
        }
    });
</script>

<div class="col-3 side-col" :class="{ 'd-none': sidebar === 'comments' }">
    <x-loader/>
    <div class="top-div">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0" id="basic-addon1">
                <i class="fas fa-search fa-lg"></i>
            </span>
            {{--<button class="input-group-text p-3 bg-white border-0">
                <x-svg.magnifying-glass size="small"/>
            </button>--}}
            <input type="text" class="form-control form-control-md lead border-start-0" placeholder="Search" wire:model.live.debounce.500ms="search"/>
        </div>
        <select class="form-select form-control all-btn" wire:model.live="collectionId">
            <option value="">All</option>
            @foreach($collections as $collection)
                <option value="{{ $collection->id }}">{{ $collection->name }}</option>
            @endforeach
        </select>
        <div class="change-icon-layout d-flex justify-content-between align-items-center d-none">
            <button class="btn border  border-secondary" onclick="setListLayout()"><i class="fas fa-bars"></i></button>
            <button class="btn border border-secondary" onclick="setGridLayout()"> <i class="fas fa-th-large"></i></button>

        </div>
        {{--<div class="outside">
            <div class="line"></div>
            <button class="editor-comment-btn btn" @click="sidebar = 'comments'">
                <i class="fal fa-comment-alt-lines"></i>
                <span>{{ __('Comments') }}</span>
            </button>
        </div>--}}
    </div>
    <div class="card-div">
        <div class="row card-row">
            @php
                $firstArtworkColumn = $artworks->getCollection();
                $secondArtworkColumn = $firstArtworkColumn->splice(0,ceil($firstArtworkColumn->count() / 2));

                $artworkColumns['firstArtworkColumn'] = $firstArtworkColumn;
                $artworkColumns['secondArtworkColumn'] = $secondArtworkColumn;
            @endphp

            @foreach($artworkColumns as $artworkColumn)
                @foreach($artworkColumn as $artwork)
                    <div class="col-12 mb-3 card-col">
                        <div class="card mb-3 artwork-img"
                             draggable="true"
                             data-img-url="{{ $artwork->image_url. "?uuid=". str()->uuid() }}"
                             data-title="{{$artwork->name}}"
                             data-thumb-url="{{$artwork->image_url}}"
                             data-artwork-id="{{$artwork->id}}"
                             data-scale="{{$artwork->data->scale}}"
                        >
                            <div class="row justify-content-center">
                                <div class="col-md-4">
                                    <div class="card-img">
                                        <img src="{{ $artwork->image_url }}" alt="card-img" class="img-fluid" />
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <div class="paragraph">{{ $artwork->artist }}</div>
                                        <div class="heading">{{ $artwork->name }}</div>
                                        <div class="dimensions">{{ $artwork->getConvertedDimensions($projectUnit) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
    <div class="pagination-div">
        {{ $artworks->onEachSide(1)->links() }}
    </div>
</div>
