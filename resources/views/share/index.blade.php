@extends('layouts.redesign')

@section('content')
<div class="container-fluid">
    <h5 class="mb-4">Share layouts</h5>
    <div class="row">
        <!-- Left Panel: Share Layout Form -->
        <div class="col-md-3">
            <div class="card p-3">
                <!-- Select Layout Button -->
                <button class="btn btn-outline-secondary mb-3 w-100" id="selectLayoutBtn">
                    Select layout
                </button>
                <!-- Thumbnail preview area -->
                <div class="mb-3" id="thumbnailContainer" style="height: 150px; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                    <span id="thumbnailPlaceholder">Share layout thumbnail</span>
                    <img id="selectedThumbnail" src="" alt="Selected Thumbnail" style="display:none; max-height: 100%; max-width: 100%;"/>
                </div>
                <!-- Title input (readonly, filled by selection) -->
                <input type="text" class="form-control mb-2" placeholder="Title" id="selectedLayoutTitle" readonly>
                <textarea class="form-control mb-2" placeholder="Description"></textarea>
                <button class="btn btn-primary w-100">Save</button>
            </div>
        </div>
        <!-- Right Panel: Shared Layouts -->
        <div class="col-md-9">
            <div class="row g-3">
                <!-- Example Card 1 -->
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <img src="IMAGE_URL_1" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Indisposable exhibition</h5>
                            <p class="card-text">Empowering art and poetry by Minna Pietarinen on UN declared Women’s Rights to make gender equality visible globally.</p>
                            <span class="badge bg-danger">Disabled</span>
                            <div class="mt-2 d-flex gap-2">
                                <a href="#" class="text-muted" title="Preview"><i class="bi bi-eye"></i></a>
                                <a href="#" class="text-muted" title="Share"><i class="bi bi-share"></i></a>
                                <a href="#" class="text-muted" title="Edit"><i class="bi bi-pencil"></i></a>
                                <a href="#" class="text-muted" title="Disable"><i class="bi bi-x-circle"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Example Card 2 -->
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <img src="IMAGE_URL_2" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">One Canal Park</h5>
                            <p class="card-text">50th anniversary of the Lahti International Poster Triennial. A world-renowned competition and exhibition of graphic design.</p>
                            <div class="mt-2 d-flex gap-2">
                                <a href="#" class="text-muted" title="Preview"><i class="bi bi-eye"></i></a>
                                <a href="#" class="text-muted" title="Share"><i class="bi bi-share"></i></a>
                                <a href="#" class="text-muted" title="Edit"><i class="bi bi-pencil"></i></a>
                                <a href="#" class="text-muted" title="Disable"><i class="bi bi-x-circle"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add more cards as needed -->
            </div>
        </div>
    </div>
</div>

<!-- Modal for layout selection -->
<div class="modal fade" id="selectLayoutModal" tabindex="-1" aria-labelledby="selectLayoutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="selectLayoutModalLabel">Select Layout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <select class="form-select" id="layoutSelect">
          <option value="" data-thumbnail="">-- Select a layout --</option>
          @foreach($layouts as $layout)
            <option value="{{ $layout->name }}" data-thumbnail="{{ $layout->thumbnail_url }}">
              {{ $layout->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="selectLayoutOkBtn">OK</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('styles')
    <link href="/css/page/share.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modalElement = document.getElementById('selectLayoutModal');
    var modal = new bootstrap.Modal(modalElement);
    var selectLayoutBtn = document.getElementById('selectLayoutBtn');
    var selectLayoutOkBtn = document.getElementById('selectLayoutOkBtn');
    var layoutSelect = document.getElementById('layoutSelect');

    // Handle opening the modal
    selectLayoutBtn.addEventListener('click', function() {
        // Reset the select to first option when opening modal
        layoutSelect.selectedIndex = 0;
        modal.show();
    });

    // Handle OK button click
    selectLayoutOkBtn.addEventListener('click', function() {
        var selectedOption = layoutSelect.options[layoutSelect.selectedIndex];
        var layoutName = selectedOption.value;
        var thumbnailUrl = selectedOption.getAttribute('data-thumbnail');

        console.log('Selected layout:', layoutName);
        console.log('Thumbnail URL:', thumbnailUrl);

        // Only proceed if a layout is actually selected
        if (layoutName && layoutName.trim() !== '') {
            // Set title
            document.getElementById('selectedLayoutTitle').value = layoutName;

            // Set thumbnail
            var img = document.getElementById('selectedThumbnail');
            var placeholder = document.getElementById('thumbnailPlaceholder');
            
            if (thumbnailUrl && thumbnailUrl.trim() !== '') {
                img.src = thumbnailUrl;
                img.style.display = 'block';
                placeholder.style.display = 'none';
                console.log('Thumbnail set successfully');
            } else {
                img.style.display = 'none';
                placeholder.style.display = 'block';
                console.log('No thumbnail available, showing placeholder');
            }

            // Close modal
            modal.hide();
        } else {
            // Show some feedback if no layout is selected
            console.log('No layout selected');
        }
    });

    // Handle modal hidden event for cleanup
    modalElement.addEventListener('hidden.bs.modal', function() {
        // Clean up any remaining backdrop
        var backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        // Clean up body classes
        document.body.classList.remove('modal-open');
        document.body.style.paddingRight = '';
    });

    // Handle modal shown event
    modalElement.addEventListener('shown.bs.modal', function() {
        // Focus on the select element when modal opens
        layoutSelect.focus();
    });
});
</script>
@endsection
