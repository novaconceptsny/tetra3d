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
    
    /* Loading states */
    .form-control:disabled, .form-select:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .pagination {
        margin-bottom: 0;
    }
    
    .debug-info {
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>

<script>
    class ArtworkCollection {
        constructor(projectId) {
            this.projectId = projectId;
            this.currentPage = 1;
            this.search = '';
            this.collectionId = '';
            this.isLoading = false;
            this.init();
        }

        init() {
            this.bindEvents();
            this.loadArtworks();
        }

        bindEvents() {
            // Search input
            const searchInput = document.querySelector('#search-input');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', (e) => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        this.search = e.target.value;
                        this.currentPage = 1;
                        this.loadArtworks();
                    }, 500);
                });
            }

            // Collection select
            const collectionSelect = document.querySelector('#collection-select');
            if (collectionSelect) {
                collectionSelect.addEventListener('change', (e) => {
                    this.collectionId = e.target.value;
                    this.currentPage = 1;
                    this.loadArtworks();
                });
            }

            // Pagination
            document.addEventListener('click', (e) => {
                if (e.target.matches('.pagination a')) {
                    e.preventDefault();
                    const url = new URL(e.target.href);
                    const page = url.searchParams.get('page');
                    if (page) {
                        this.currentPage = parseInt(page);
                        this.loadArtworks();
                    }
                }
            });
        }

        async loadArtworks() {
            if (this.isLoading) return;
            
            this.isLoading = true;
            this.showLoading();

            try {
                const params = new URLSearchParams({
                    search: this.search,
                    collection_id: this.collectionId,
                    page: this.currentPage
                });

                const response = await fetch(`/projects/${this.projectId}/artworks?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load artworks');
                }

                const data = await response.json();
                this.renderArtworks(data.artworks);
                this.renderPagination(data.pagination);
                this.renderCollections(data.collections);
                this.updateDebugInfo(data);

            } catch (error) {
                console.error('Error loading artworks:', error);
                this.showError('Failed to load artworks');
            } finally {
                this.isLoading = false;
                this.hideLoading();
            }
        }

        renderArtworks(artworks) {
            const cardRow = document.querySelector('.card-row');
            if (!cardRow) return;

            // Split artworks into two columns
            const firstColumn = artworks.slice(0, Math.ceil(artworks.length / 2));
            const secondColumn = artworks.slice(Math.ceil(artworks.length / 2));

            const artworkColumns = [firstColumn, secondColumn];
            
            let html = '';
            artworkColumns.forEach(artworkColumn => {
                artworkColumn.forEach(artwork => {
                    html += this.renderArtworkCard(artwork);
        });
    });
    
            cardRow.innerHTML = html;
            this.refreshArtworkBadges();
        }

        renderArtworkCard(artwork) {
            const imageUrl = artwork.media && artwork.media.length > 0 
                ? artwork.media[0].original_url 
                : artwork.image_url || '/placeholder.jpg';
            
            const dimensions = artwork.converted_dimensions || '';
            
            return `
                <div class="col-12 mb-3 card-col">
                    <div class="card mb-3 artwork-img"
                         draggable="true"
                         data-img-url="${imageUrl}?uuid=${this.generateUUID()}"
                         data-title="${artwork.name}"
                         data-thumb-url="${imageUrl}"
                         data-artwork-id="${artwork.id}"
                         data-scale="${artwork.data?.scale || 1}"
                    >
                        <div class="row justify-content-center">
                            <div class="col-md-4">
                                <div class="card-img">
                                    <img src="${imageUrl}" alt="card-img" class="img-fluid" />
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <div class="paragraph">${artwork.artist || ''}</div>
                                    <div class="heading">${artwork.name || ''}</div>
                                    <div class="dimensions">${dimensions}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        renderPagination(pagination) {
            const paginationDiv = document.querySelector('.pagination-div');
            if (!paginationDiv) return;

            let paginationHtml = '';
            
            if (pagination.last_page > 1) {
                paginationHtml += '<nav><ul class="pagination">';
                
                // Previous button
                if (pagination.current_page > 1) {
                    paginationHtml += `<li class="page-item"><a class="page-link" href="?page=${pagination.current_page - 1}">Previous</a></li>`;
                }
                
                // Page numbers
                for (let i = 1; i <= pagination.last_page; i++) {
                    const isActive = i === pagination.current_page ? 'active' : '';
                    paginationHtml += `<li class="page-item ${isActive}"><a class="page-link" href="?page=${i}">${i}</a></li>`;
                }
                
                // Next button
                if (pagination.current_page < pagination.last_page) {
                    paginationHtml += `<li class="page-item"><a class="page-link" href="?page=${pagination.current_page + 1}">Next</a></li>`;
                }
                
                paginationHtml += '</ul></nav>';
            }

            paginationDiv.innerHTML = paginationHtml;
        }

        renderCollections(collections) {
            const select = document.querySelector('#collection-select');
            if (!select) return;

            let html = '<option value="">All</option>';
            collections.forEach(collection => {
                const selected = collection.id == this.collectionId ? 'selected' : '';
                html += `<option value="${collection.id}" ${selected}>${collection.name}</option>`;
            });
            
            select.innerHTML = html;
        }

        updateDebugInfo(data) {
            const debugInfo = document.querySelector('.debug-info');
            if (debugInfo) {
                debugInfo.innerHTML = `
                    Showing ${data.pagination.from || 0} to ${data.pagination.to || 0} of ${data.pagination.total} results
                    ${this.search ? `| Search: "${this.search}"` : ''}
                    ${this.collectionId ? `| Collection ID: ${this.collectionId}` : ''}
                `;
            }
        }


        generateUUID() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                const r = Math.random() * 16 | 0;
                const v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        showLoading() {
            const loader = document.querySelector('x-loader');
            if (loader) {
                loader.style.display = 'block';
            }
            
            // Disable form elements during loading
            const searchInput = document.querySelector('#search-input');
            const collectionSelect = document.querySelector('#collection-select');
            if (searchInput) searchInput.disabled = true;
            if (collectionSelect) collectionSelect.disabled = true;
        }

        hideLoading() {
            const loader = document.querySelector('x-loader');
            if (loader) {
                loader.style.display = 'none';
            }
            
            // Re-enable form elements after loading
            const searchInput = document.querySelector('#search-input');
            const collectionSelect = document.querySelector('#collection-select');
            if (searchInput) searchInput.disabled = false;
            if (collectionSelect) collectionSelect.disabled = false;
        }

        showError(message) {
            console.error(message);
            
            // Show error in the artwork area
        const cardRow = document.querySelector('.card-row');
        if (cardRow) {
                cardRow.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            ${message}
                        </div>
                    </div>
                `;
            }
        }

        refreshArtworkBadges() {
        if (typeof window.refreshArtworkBadges === 'function') {
                window.refreshArtworkBadges();
            }
        }
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        const projectId = {{ $project->id }};
        window.artworkCollection = new ArtworkCollection(projectId);
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
            <input type="text" class="form-control form-control-md lead border-start-0" placeholder="Search" id="search-input"/>
        </div>
        <select class="form-select form-control all-btn" id="collection-select">
            <option value="">All</option>
            <!-- Collections will be loaded by JavaScript -->
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
            <!-- Artworks will be loaded by JavaScript -->
        </div>
    </div>
    <div class="pagination-div">
        <!-- Pagination will be rendered by JavaScript -->
    </div>
    <div class="debug-info mt-2 text-muted small">
        <!-- Debug info will be rendered by JavaScript -->
    </div>
</div>
