@extends('layouts.redesign')

@section('content')
<div class="container" style="background: #f7f9fb; min-height: 100vh; padding: 40px 0;">
    <!-- About Section -->
    <div style="margin-bottom: 40px;">
        <h4>About</h4>
        <p>Tetra3D is a tool designed.......................</p>
    </div>

    <!-- Tutorials Section -->
    <div style="margin-bottom: 40px;">
        <h4 style="margin-bottom: 20px;">Tutorials</h4>
        <div style="margin-bottom: 10px;"><strong>Tetra3D</strong></div>
        <div style="display: flex; gap: 24px; margin-bottom: 32px;">
            <div>
                <div>Creating a Layout</div>
                <img src="{{ asset('images/dashboard__bg.png') }}" style="width:200px; border-radius:8px;">
            </div>
            <div>
                <div>Adding artwork & sculptures</div>
                <img src="{{ asset('images/dashboard__bg.png') }}" style="width:200px; border-radius:8px;">
            </div>
            <div>
                <div>Uploading artwork</div>
                <img src="{{ asset('images/dashboard__bg.png') }}" style="width:200px; border-radius:8px;">
            </div>
            <div>
                <div>Sharing your layout</div>
                <img src="{{ asset('images/dashboard__bg.png') }}" style="width:200px; border-radius:8px;">
            </div>
        </div>
        <div style="margin-bottom: 10px;"><strong>Curate2D</strong></div>
        <div style="display: flex; gap: 24px;">
            <div>
                <div>Uploading an image</div>
                <img src="{{ asset('images/dashboard__bg.png') }}" style="width:200px; border-radius:8px;">
            </div>
            <div>
                <div>Editing your project</div>
                <img src="{{ asset('images/dashboard__bg.png') }}" style="width:200px; border-radius:8px;">
            </div>
            <div>
                <div>Adding artwork & sculptures</div>
                <img src="{{ asset('images/dashboard__bg.png') }}" style="width:200px; border-radius:8px;">
            </div>
        </div>
    </div>

    <!-- Template Galleries Section -->
    <div style="margin-bottom: 40px;">
        <h4 style="margin-bottom: 20px;">Template galleries</h4>
        <div style="display: flex; gap: 24px;">
            @foreach([$templateTours[0], $templateTours[1]] as $i => $gallery)
                <div style="position: relative;">
                    <img src="{{ asset('images/gallery_' . ($i+1) . '.png') }}" style="width:350px; border-radius:12px;">
                    <!-- Plus Button -->
                    <button
                        onclick="handleGalleryButtonClick('{{ $gallery->name }}', {{ $gallery->id }}, {{ $gallery->isOwn ? 'true' : 'false' }})"
                        title="Add to company"
                        data-tour-id="{{ $gallery->id }}"
                        style="
                            position: absolute;
                            top: 12px;
                            right: 12px;
                            width: 36px;
                            height: 36px;
                            border-radius: 50%;
                            border: none;
                            background: #fff;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 24px;
                            cursor: pointer;
                            z-index: 2;
                        ">
                        {{ $gallery->isOwn ? '✓' : '+' }}
                    </button>
                    <div style="text-align:center; margin-top:8px;">{{ $gallery->name }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- FAQ / About Section -->
    <div>
        <h4>About</h4>
        <p>How can I compare layouts?</p>
    </div>
</div>

<!-- Add Company Modal -->
<div id="addCompanyModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:10; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:32px; min-width:640px; position:relative;">
        <button onclick="closeAddCompanyModal()" style="position:absolute; top:12px; right:12px; background:none; border:none; font-size:20px; cursor:pointer;">&times;</button>
        <h5>Add to Company</h5>
        <div id="modalGalleryName" style="margin-bottom:16px; color:#888;"></div>
        <input type="hidden" id="modalTourId" value="">
        <!-- Your form or content here -->
        <x-backend::inputs.select2 id="companySelect" name="company_id" label="Company" :multiple="true">
            @foreach($companies as $company)
                <x-backend::inputs.select-option
                    :value="$company->id"
                    :text="$company->name"
                />
            @endforeach
        </x-backend::inputs.select2>
        <div style="text-align: right; margin-top:16px;">
            <button
                id="addCompanyButton"
                onclick="handleAddCompany()"
                type="submit"
                style="background:#007bff; color:#fff; border:none; padding:8px 16px; border-radius:4px;">
                Add
            </button>
        </div>
    </div>
</div>

<!-- Remove Gallery Modal -->
<div id="removeGalleryModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:20; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:32px; min-width:400px; position:relative; text-align:center;">
        <h4>Are you sure you want to remove this gallery?</h4>
        <div id="removeGalleryName" style="font-weight:bold; margin: 12px 0;"></div>
        <div style="color:#d97706; margin-bottom:16px;">
            <span style="font-size:24px; vertical-align:middle;">&#9888;</span>
            If this template has been used in any projects or layouts, all linked information will be permanently removed.
        </div>
        <div style="display:flex; justify-content:center; gap:24px;">
            <button id="removeGalleryButton" style="background:#d32f2f; color:#fff; border:none; padding:8px 32px; border-radius:4px;">Remove</button>
            <button onclick="closeRemoveGalleryModal()" style="background:#304ffe; color:#fff; border:none; padding:8px 32px; border-radius:4px;">Cancel</button>
        </div>
    </div>
</div>

<link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="light-style" />
<script src="{{ asset('backend/assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/app.min.js') }}"></script>

<script>

var companies = @json($companies);
var templateTours = @json($templateTours);

console.log(templateTours);

function openAddCompanyModal(galleryName, tourId) {
    document.getElementById('addCompanyModal').style.display = 'flex';
    document.getElementById('modalGalleryName').innerText = galleryName;
    document.getElementById('modalTourId').value = tourId;
}
function closeAddCompanyModal() {
    document.getElementById('addCompanyModal').style.display = 'none';
}
function handleAddCompany() {   
    var select = document.getElementById('companySelect');
    var selectedCompanyNames = Array.from(select.selectedOptions).map(option => option.text);
    var tourId = document.getElementById('modalTourId').value;


    // Send to backend via AJAX
    fetch("{{ route('resource.assignTourToCompanies') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            company_names: selectedCompanyNames,
            tour_id: tourId
        })
    })
    .then(response => response.json())
    .then(response => {
        if (response.success) {
            console.log(response);
           closeAddCompanyModal();
        } else {
            console.error('Error:', response.error);
        }
    });
}

function handleGalleryButtonClick(galleryName, tourId, isOwn) {
    if (isOwn) {
        openRemoveGalleryModal(galleryName, tourId);
    } else {
        openAddCompanyModal(galleryName, tourId);
    }
}

function openRemoveGalleryModal(galleryName, tourId) {
    document.getElementById('removeGalleryModal').style.display = 'flex';
    document.getElementById('removeGalleryName').innerText = galleryName;
    // Store tourId for removal action if needed
    document.getElementById('removeGalleryButton').onclick = function() {
        // Call your remove function here, e.g.:
        // removeGallery(tourId);
        alert('Remove gallery with ID: ' + tourId); // Replace with real logic
        closeRemoveGalleryModal();
    };
}

function closeRemoveGalleryModal() {
    document.getElementById('removeGalleryModal').style.display = 'none';
}
</script>
@endsection
