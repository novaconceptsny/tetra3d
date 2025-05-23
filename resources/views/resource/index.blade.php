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
            <div style="position: relative;">
                <img src="{{ asset('images/gallery_1.png') }}" style="width:350px; border-radius:12px;">
                <!-- Plus Button -->
                <button
                    onclick="openAddCompanyModal('Gallery 12')"
                    title="Add to company"
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
                    +
                </button>
                <div style="text-align:center; margin-top:8px;">Gallery 12</div>
            </div>
            <div style="position: relative;">
                <img src="{{ asset('images/gallery_2.png') }}" style="width:350px; border-radius:12px;">
                <button
                    onclick="openAddCompanyModal('Gallery 10')"
                    title="Add to company"
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
                    +
                </button>
                <div style="text-align:center; margin-top:8px;">Gallery 10</div>
            </div>
        </div>
    </div>

    <!-- FAQ / About Section -->
    <div>
        <h4>About</h4>
        <p>How can I compare layouts?</p>
    </div>
</div>

<!-- Add Company Modal -->
<div id="addCompanyModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:32px; min-width:320px; position:relative;">
        <button onclick="closeAddCompanyModal()" style="position:absolute; top:12px; right:12px; background:none; border:none; font-size:20px; cursor:pointer;">&times;</button>
        <h5>Add to Company</h5>
        <div id="modalGalleryName" style="margin-bottom:16px; color:#888;"></div>
        <!-- Your form or content here -->
        <form>
            <label>Company Name:</label>
            <select id="multiple-select" multiple>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
            <button type="submit" style="background:#007bff; color:#fff; border:none; padding:8px 16px; border-radius:4px;">Add</button>
        </form>
    </div>
</div>

<script>
function openAddCompanyModal(galleryName) {
    document.getElementById('addCompanyModal').style.display = 'flex';
    document.getElementById('modalGalleryName').innerText = galleryName;
}
function closeAddCompanyModal() {
    document.getElementById('addCompanyModal').style.display = 'none';
}
</script>
@endsection
