@extends('layouts.redesign')

@section('content')
<div class="container" style="background: #f7f9fb; min-height: 100vh; padding: 40px 0;">
    <!-- About Section -->
    <div style="margin-bottom: 40px;">
        <h4>About</h4>
        <p>Tetra3d is a browser-based platform for building and sharing immersive virtual gallery tours. Designed for artists, curators, and organizations, it allows users to easily upload artwork, select from a variety of gallery templates, and design interactive 3D exhibitions that can be shared online.</p>

         <p>Beyond creating virtual tours, Tetra3d also serves as a workspace for managing artwork—helping you organize your digital collection, link pieces to specific projects, and experiment with different layouts and gallery designs. Whether you’re planning an upcoming exhibition, showcasing a portfolio, or collaborating with a team, Tetra3d provides the tools to bring your gallery vision to life.</p>
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
            @foreach($templateTours as $i => $gallery)
                <div style="position: relative;">
                    <img
                        src="{{ $gallery->getFirstMediaUrl('thumbnail') ?: asset('images/gallery_' . ($i > 1 ? 1 : ($i+1)) . '.png') }}"
                        style="width:350px; border-radius:12px; cursor: pointer;"
                        onclick="goToTour({{ $gallery->id }})"
                    >
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
        <h4>FAQ</h4>
        <p><strong>How do I get started with my first project?</strong></p>
        <p>
            Once you’ve uploaded artwork and added gallery templates to your workspace or company, you can create your first project.
            A project links your selected artworks and galleries in one place. Inside the project, you can start building layouts—each layout is a version of your gallery setup.
            Layouts can be saved, duplicated, edited, and deleted, allowing you to experiment with different arrangements and compare your ideas easily.
        </p>
        <p><strong>
            Do I need any special software or hardware to use Tetra3d?
        </strong></p>
        <p>
            No, you don’t need any special software. Tetra3d is fully browser-based, so you can access and use it directly through your web browser without any additional software or hardware requirements.
        </p>
        <p><strong>
            What are free template galleries, and how do I use them?
        </strong></p>
        <p>
            At Nova, we've designed a variety of pre-built gallery tours that users can explore and personalize by adding their own artwork.
            All available template galleries are listed on this page. You can browse through each gallery as a preview, and once you find one you like, simply add it to your company.
            From there, you can use the template in your project.
        </p>
        <p><strong>
            Is there a limit to the number of artworks I can display in a gallery?
        </strong></p>
        <p>
            The only limitation is the available wall space within the gallery. You can place multiple pieces of artwork on a single surface, allowing for flexible arrangement and display options.
        </p>
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

        <select id="companySelect"  name="states[]" multiple="multiple" style="width: 100%;">
            @foreach($companies as $company)
                @php
                    // Remove the _## suffix from "My Workspace_##" names
                    $displayName = $company->name;
                    if (str_contains($displayName, 'My Workspace') && preg_match('/^My Workspace_\d+$/', $displayName)) {
                        $displayName = preg_replace('/_\d+$/', '', $displayName);
                    }
                @endphp
                <option style="padding: 8px 16px;"
                    value="{{ $company->id }}">{{ $displayName }}
                </option>
            @endforeach
        </select>

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
    <div style="background:#fff; border-radius:12px; padding:32px; min-width:400px; max-width:680px; position:relative; text-align:center;">
        <h5>Are you sure you want to remove this gallery?</h5>
        <div id="removeGalleryName" style="font-weight:bold; margin: 12px 0;"></div>
        <input type="hidden" id="modalTourId" value="">
        <div style="color:#d97706; margin-bottom:16px;">
            <span style="font-size:24px; vertical-align:middle;">&#9888;</span>
            Any projects or layouts that use this template gallery will remain on your Projects page.
            If you'd like to remove them, you'll need to delete them separately.
        </div>
        <div style="display:flex; justify-content:center; gap:24px;">
            <button id="removeGalleryButton" style="background:#d32f2f; color:#fff; border:none; padding:8px 32px; border-radius:4px;" onclick="handleRemoveGallery()">Remove</button>
            <button onclick="closeRemoveGalleryModal()" style="background:#304ffe; color:#fff; border:none; padding:8px 32px; border-radius:4px;">Cancel</button>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
/* Custom Select2 tag style */
.select2-selection__choice {
    background: #8187f5 !important;
    color: #fff !important;
    border: none !important;
    border-radius: 6px !important;
    /* padding: 6px 14px !important;
    margin: 4px 4px 0 0 !important; */
    font-size: 16px !important;
}
.select2-selection__choice__remove {
    color: #fff !important;
    margin-right: 6px;
}

/* Remove the static first-child styles since we'll apply them dynamically */
</style>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

var companies = @json($companies);
var templateTours = @json($templateTours);
console.log(templateTours);

function openAddCompanyModal(galleryName, tourId) {
    document.getElementById('addCompanyModal').style.display = 'flex';
    document.getElementById('modalGalleryName').innerText = galleryName;
    document.getElementById('modalTourId').value = tourId;

    const select = $("#companySelect").select2();
    var gallery = templateTours.find(function(g) { return g.id == tourId; });

    // Move first assigned company to top
    if (gallery.assigned_company_ids && gallery.assigned_company_ids.length > 0) {
        const firstAssignedId = gallery.assigned_company_ids[0];
        const $firstOption = select.find(`option[value="${firstAssignedId}"]`);
        const $firstOptionClone = $firstOption.clone();

        $firstOption.remove();
        select.prepend($firstOptionClone);
    }

    select.val(gallery.assigned_company_ids).trigger('change');

    // Add dynamic styling based on mainCompanyIds
    const styleElement = document.createElement('style');
    if (gallery.mainCompanyIds && gallery.mainCompanyIds.length > 0) {
        styleElement.textContent = `
            .select2-selection__choice:first-child {
                background: #C11C84 !important;
            }
            .select2-selection__choice:first-child .select2-selection__choice__remove,
            .select2-selection__choice:first-child:hover .select2-selection__choice__remove {
                display: none !important;
            }
        `;
    } else {
        styleElement.textContent = `
            .select2-selection__choice:first-child {
                background: #8187f5 !important;
            }
            .select2-selection__choice:first-child .select2-selection__choice__remove {
                display: block !important;
            }
        `;
    }

    // Remove any previously added dynamic styles
    const oldStyle = document.getElementById('dynamic-select2-style');
    if (oldStyle) {
        oldStyle.remove();
    }

    // Add the new styles
    styleElement.id = 'dynamic-select2-style';
    document.head.appendChild(styleElement);

    // Ensure styles are applied after any changes
    select.on('change', function() {
        setTimeout(() => {
            const choices = document.querySelectorAll('.select2-selection__choice');
            if (choices.length > 0) {
                // Remove any existing custom styling
                choices.forEach(choice => {
                    choice.style.background = '#8187f5';
                    const removeBtn = choice.querySelector('.select2-selection__choice__remove');
                    if (removeBtn) removeBtn.style.display = 'block';
                });

                if (gallery.mainCompanyIds && gallery.mainCompanyIds.length > 0) {
                    // Apply special styling to first choice
                    const firstChoice = choices[0];
                    firstChoice.style.background = '#C11C84';
                    const removeButton = firstChoice.querySelector('.select2-selection__choice__remove');
                    if (removeButton) removeButton.style.display = 'none';
                }
            }
        }, 0);
    });
}
function closeAddCompanyModal() {
    document.getElementById('addCompanyModal').style.display = 'none';
}
function handleAddCompany() {
    var select = document.getElementById('companySelect');
    var selectedCompanyNames = select.selectedOptions ?
        Array.from(select.selectedOptions).map(option => option.text) :
        [];
    var selectedCompanyIds = select.selectedOptions ?
        Array.from(select.selectedOptions).map(option => option.value) :
        [];
    var tourId = document.getElementById('modalTourId').value;

    console.log(selectedCompanyNames);


    // Send to backend via AJAX
    fetch("{{ route('resource.assignTourToCompanies') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            company_ids: selectedCompanyIds,
            tour_id: tourId
        })
    })
    .then(response => response.json())
    .then(response => {
        if (response.success) {
            console.log(response.templateTours);
            templateTours = response.templateTours;
        //    closeAddCompanyModal();
           window.location.reload();
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
    document.getElementById('modalTourId').value = tourId;
}

function closeRemoveGalleryModal() {
    document.getElementById('removeGalleryModal').style.display = 'none';
}

function handleRemoveGallery() {
    var tourId = document.getElementById('modalTourId').value;

    // Send to backend via AJAX
    fetch("{{ route('resource.removeGallery') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            tour_id: tourId
        })
    })
    .then(response => response.json())
    .then(response => {
        if (response.success) {
            // Update the templateTours array
            templateTours = response.templateTours;
            // Refresh the page or update the UI as needed
            window.location.reload();
        } else {
            console.error('Error:', response.error);
            alert('Failed to remove gallery: ' + response.error);
        }
    });

    closeRemoveGalleryModal();
}

function goToTour(tourId) {
    window.open('/tours/' + tourId, '_blank');
}

$(document).ready(function() {
    $('#companySelect').select2();
});

</script>
@endsection
