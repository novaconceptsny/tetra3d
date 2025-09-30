<style>
    /* Artwork card styling to match the image design */
    .artwork-card {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        height: 80px;
        transition: all 0.2s ease;
        cursor: grab;
        user-select: none;
        position: relative;
    }
    
    .artwork-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-color: #099F9A;
    }
    
    .artwork-card:active {
        cursor: grabbing;
    }
    
    .artwork-card.dragging {
        opacity: 0.5;
        transform: scale(0.95);
        cursor: grabbing;
        z-index: 1000;
    }
    
    .artwork-card.drag-over {
        border: 2px dashed #099F9A;
        background-color: rgba(0, 123, 255, 0.1);
    }
    
    /* Add a subtle indicator that items are draggable */
    .artwork-card::before {
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
    
    .artwork-card:hover::before {
        opacity: 1;
        color: #007bff;
    }
    
    .artwork-card.dragging::before {
        opacity: 0;
    }
    
    .artwork-thumbnail {
        width: 60px;
        height: 60px;
        margin-right: 12px;
        flex-shrink: 0;
    }
    
    .thumbnail-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 4px;
        background-color: #f8f9fa;
    }
    
    .artwork-details {
        flex: 1;
        min-width: 0;
    }
    
    .artist-name {
        font-size: 14px;
        color: #495057;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .artwork-title {
        font-size: 14px;
        font-style: italic;
        font-weight: 500;
        color: #495057;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .artwork-dimensions {
        font-size: 14px;
        color: #495057;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
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
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 2px;
        background-color: #f8f9fa;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    
    .pagination .page-item {
        margin: 0;
    }
    
    .pagination .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 12px;
        margin: 0 2px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background-color: #ffffff;
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
        font-size: 14px;
    }
    
    .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #007bff;
        color: #0056b3;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: #ffffff;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0,123,255,0.3);
    }
    
    .pagination .page-item.disabled .page-link {
        background-color: #ffffff;
        border-color: #dee2e6;
        color: #6c757d;
        cursor: default;
        pointer-events: none;
    }
    
    .pagination .page-item.disabled .page-link:hover {
        background-color: #ffffff;
        border-color: #dee2e6;
        color: #6c757d;
        transform: none;
        box-shadow: none;
    }
    
    /* Previous/Next button styling */
    .pagination .page-link:first-child,
    .pagination .page-link:last-child {
        font-weight: 600;
        font-size: 16px;
    }
    
    /* Ellipsis styling */
    .pagination .page-item.disabled .page-link {
        background-color: #ffffff;
        border-color: #dee2e6;
        color: #6c757d;
        font-weight: 500;
    }
    
    .debug-info {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    /* Top div styling */
    .top-div {
        height: 36px; /* Adjust this value as needed */
        background: #f5f5f5 !important;
        padding-top: 1px !important;
    }
    
    /* Card div scrollbar styling */
    .card-div {
        /* Webkit browsers (Chrome, Safari, Edge) */
        scrollbar-width: thin;
        scrollbar-color: #F5F5F5 #F5F5F5;
    }
    
    .card-div::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .card-div::-webkit-scrollbar-track {
        background: #F5F5F5;
        border-radius: 4px;
    }
    
    .card-div::-webkit-scrollbar-thumb {
        background: #F5F5F5;
        border-radius: 4px;
        border: 1px solid #E0E0E0;
    }
    
    .card-div::-webkit-scrollbar-thumb:hover {
        background: #E8E8E8;
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
                <div class="col-12 mb-2 card-col">
                    <div class="artwork-card artwork-img"
                         draggable="true"
                         data-img-url="${imageUrl}?uuid=${this.generateUUID()}"
                         data-title="${artwork.name}"
                         data-thumb-url="${imageUrl}"
                         data-artwork-id="${artwork.id}"
                         data-scale="${artwork.data?.scale || 1}"
                    >
                        <div class="artwork-thumbnail">
                            <img src="${imageUrl}" alt="card-img" class="thumbnail-img" />
                        </div>
                        <div class="artwork-details">
                            <div class="artist-name">${artwork.artist || ''}</div>
                            <div class="artwork-title">${artwork.name || ''}</div>
                            <div class="artwork-dimensions">${dimensions}</div>
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
                    paginationHtml += `<li class="page-item"><a class="page-link" href="?page=${pagination.current_page - 1}">&lt;</a></li>`;
                }
                
                // Smart page numbers with ellipsis
                const pages = this.generatePaginationPages(pagination.current_page, pagination.last_page);
                pages.forEach(page => {
                    if (page === '...') {
                        paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    } else {
                        const isActive = page === pagination.current_page ? 'active' : '';
                        paginationHtml += `<li class="page-item ${isActive}"><a class="page-link" href="?page=${page}">${page}</a></li>`;
                    }
                });
                
                // Next button
                if (pagination.current_page < pagination.last_page) {
                    paginationHtml += `<li class="page-item"><a class="page-link" href="?page=${pagination.current_page + 1}">&gt;</a></li>`;
                }
                
                paginationHtml += '</ul></nav>';
            }

            paginationDiv.innerHTML = paginationHtml;
        }

        generatePaginationPages(currentPage, totalPages) {
            const pages = [];
            const maxVisiblePages = 7; // Adjust this to show more/fewer pages
            
            if (totalPages <= maxVisiblePages) {
                // Show all pages if total is small
                for (let i = 1; i <= totalPages; i++) {
                    pages.push(i);
                }
            } else {
                // Always show first page
                pages.push(1);
                
                if (currentPage <= 4) {
                    // Near the beginning: 1, 2, 3, 4, 5, ..., last
                    for (let i = 2; i <= Math.min(5, totalPages - 1); i++) {
                        pages.push(i);
                    }
                    if (totalPages > 5) {
                        pages.push('...');
                    }
                    if (totalPages > 1) {
                        pages.push(totalPages);
                    }
                } else if (currentPage >= totalPages - 3) {
                    // Near the end: 1, ..., last-4, last-3, last-2, last-1, last
                    if (totalPages > 5) {
                        pages.push('...');
                    }
                    for (let i = Math.max(2, totalPages - 4); i <= totalPages; i++) {
                        pages.push(i);
                    }
                } else {
                    // In the middle: 1, ..., current-1, current, current+1, ..., last
                    pages.push('...');
                    for (let i = currentPage - 1; i <= currentPage + 1; i++) {
                        pages.push(i);
                    }
                    pages.push('...');
                    pages.push(totalPages);
                }
            }
            
            return pages;
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
        <div class="input-group" >
            <span class="input-group-text bg-white border-end-0" id="basic-addon1">
                <i class="fas fa-search fa-lg"></i>
            </span>
            {{--<button class="input-group-text p-3 bg-white border-0">
                <x-svg.magnifying-glass size="small"/>
            </button>--}}
            <input type="text" class="form-control form-control-md lead border-start-0 h-100" placeholder="Search" id="search-input"/>
        </div>
        <select class="form-select form-control all-btn h-100" id="collection-select">
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
