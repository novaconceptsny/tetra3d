@extends('layouts.redesign')

@section('content')


    <div class="px-5 mt-3">
        <div class="header d-flex justify-content-between align-items-center">
            <div style="font-size: 30px; width: 90%;" onclick="focusProjectName(event)">
                <input type="text" 
                       class="bg-transparent fw-bold project-name-input" 
                       style="font-size: 30px; width: auto; min-width: 100%; outline: none; border: none;" 
                       value="Project Name"
                       onchange="updateProjectName(this.value)"
                       readonly>
            </div>
            <button class="btn btn-primary"><i class="fa fa-plus"></i> Edit Project</button>
        </div>
    </div>
    <div class="px-5 mt-4 mb-5">
        <div class="row g-3">
            <div style="font-size: 20px;"><strong>Collection</strong></div>
            <div class="col-xl-2 col-lg-3 col-md-4">
                <div class="custom-card d-flex">
                    <img src="images/collection1.png" alt="Collection Image">
                    <div class="custom-card-content">
                        <h6 class="mb-0">Rob's Collection</h6>
                        <small class="text-muted">1024 items</small>
                    </div>
                    <div class="menu-container">
                        <i class="fas fa-ellipsis-v"></i>
                        <div class="menu">
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4">
                <div class="custom-card d-flex">
                    <div class="d-flex align-items-center p-3">
                        <i class="fas fa-image fa-2x text-secondary"></i>
                    </div>
                    <div class="custom-card-content">
                        <h6 class="mb-0">Sculptures</h6>
                        <small class="text-muted">12 items</small>
                    </div>
                    <div class="menu-container">
                        <i class="fas fa-ellipsis-v"></i>
                        <div class="menu">
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4">
                <div class="custom-card d-flex">
                    <div class="d-flex align-items-center p-3">
                        <i class="fas fa-image fa-2x text-secondary"></i>
                    </div>
                    <div class="custom-card-content">
                        <h6 class="mb-0">Digital Art</h6>
                        <small class="text-muted">18 items</small>
                    </div>
                    <div class="menu-container">
                        <i class="fas fa-ellipsis-v"></i>
                        <div class="menu">
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4">
                <div class="custom-card d-flex">
                    <div class="d-flex align-items-center p-3">
                        <i class="fas fa-image fa-2x text-secondary"></i>
                    </div>
                    <div class="custom-card-content">
                        <h6 class="mb-0">Photography</h6>
                        <small class="text-muted">31 items</small>
                    </div>
                    <div class="menu-container">
                        <i class="fas fa-ellipsis-v"></i>
                        <div class="menu">
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4">
                <div class="add-new-card">
                    <i class="fas fa-plus"></i>
                    <p class="mb-0 mt-1">Add Collection</p>
                </div>
            </div>
        </div>
    </div>
    <div class="px-5 mt-4 mb-5">
        <div class="row g-3">
            <div style="font-size: 20px;"><strong>Surfaces</strong></div>
            <div class="col-xl-2 col-lg-3 col-md-4">
                <div class="custom-card d-flex">
                    <div class="custom-card-content text-center">
                        <h6 class="mb-0"><strong>All</strong></h6>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4">
                <div class="custom-card d-flex">
                    <div class="custom-card-content text-center">
                        <h6 class="mb-0">North Wall</h6>
                    </div>
                    <div class="menu-container">
                        <i class="fas fa-ellipsis-v"></i>
                        <div class="menu">
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4">
                <div class="custom-card d-flex">
                    <div class="custom-card-content text-center">
                        <h6 class="mb-0">Main entry surface</h6>
                    </div>
                    <div class="menu-container">
                        <i class="fas fa-ellipsis-v"></i>
                        <div class="menu">
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4">
                <div class="add-new-card">
                    <i class="fas fa-plus"></i>
                    <p class="mb-0 mt-1">Add Surface</p>
                </div>
            </div>
        </div>
    </div>

    <div class="px-5 mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center">
            <div style="font-size: 20px;"><strong>Photos</strong></div>
            <button class="btn btn-primary"><i class="fa fa-plus"></i> Duplicate Space</button>
        </div>
        <div class="row g-3" id="imageGrid">
            <div class="col-xl-2 col-lg-3 col-md-4" data-bs-toggle="modal" data-bs-target="#imageModal">
                <div class="add-image-card">
                    <i class="fas fa-plus"></i>
                    <p class="mb-0 mt-1">Add Image</p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-4">
                <h5 class="modal-title" id="imageModalLabel">Add images</h5>
                <small>Add a name and upload a JPEG or PNG file. Max 2048 pixels on the long edge.</small>

                <!-- Image List -->
                <div id="imageList"></div>

                <!-- Add Image Button -->
                <button class="btn btn-secondary w-50 mt-2" onclick="document.getElementById('imageInput').click()">+
                    Select image</button>
                <input type="file" id="imageInput" class="d-none" accept="image/*" multiple>

                <!-- Save Button -->
                <button class="btn btn-primary w-100 mt-3" onclick="saveImages()">Save</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll(".menu-container i").forEach(icon => {
            icon.addEventListener("click", function (event) {
                let menuContainer = this.parentElement;
                document.querySelectorAll(".menu-container").forEach(container => {
                    if (container !== menuContainer) container.classList.remove("active");
                });
                menuContainer.classList.toggle("active");
                event.stopPropagation();
                });
            });

            document.addEventListener("click", function () {
                document.querySelectorAll(".menu-container").forEach(container => {
                    container.classList.remove("active");
                });
            });
        });

        let images = [];

        document.getElementById("imageInput").addEventListener("change", function (event) {
            const files = event.target.files;
            for (let file of files) {
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const imageUrl = e.target.result;
                        const imageName = file.name.split(".")[0]; // Default name

                        images.push({ name: imageName, url: imageUrl });
                        renderImages();
                    };
                    reader.readAsDataURL(file);
                }
            }
            event.target.value = ""; // Reset file input
        });

        function removeImage(index) {
            images.splice(index, 1);
            renderImages();
        }

        function updateName(index, newName) {
            images[index].name = newName;
        }

        function renderImages() {
            const imageList = document.getElementById("imageList");
            imageList.innerHTML = "";
            images.forEach((image, index) => {
                imageList.innerHTML += `
            <div class="image-preview">
                <input type="text" class="form-control me-2" value="${image.name}" oninput="updateName(${index}, this.value)">
                <img src="${image.url}" alt="Image">
                <span class="remove-btn" onclick="removeImage(${index})">X</span>
            </div>
        `;
            });
        }

        function saveImages() {
            const imageGrid = document.getElementById("imageGrid");
            // imageGrid.innerHTML = "";


            // Remove old uploaded images before adding new ones
            document.querySelectorAll(".new-image").forEach(el => el.remove());

            images.forEach((image) => {
                const imageCard = document.createElement("div");
                imageCard.classList.add("col-xl-2", "col-lg-3", "col-md-4", "new-image");
                imageCard.innerHTML = `
            <div class="photo-card">
                <img src="${image.url}" alt="${image.name}">
                <div class="photo-info d-flex justify-content-between align-items-center">
                    <p>${image.name}</p>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        `;
                imageGrid.insertBefore(imageCard, imageGrid.lastElementChild);
            });

            // Close modal after saving
            const modal = bootstrap.Modal.getInstance(document.getElementById('imageModal'));
            modal.hide();
        }

        function updateProjectName(newName) {
            // Here you can add AJAX call to update the project name in the backend
            console.log('Project name updated to:', newName);
        }

        function focusProjectName(event) {
            const input = event.currentTarget.querySelector('input');
            input.readOnly = false;
            input.focus();
            
            // Add blur event listener to make it readonly again when focus is lost
            input.addEventListener('blur', function() {
                input.readOnly = true;
            }, { once: true });
        }

    </script>
@endpush

@push('styles')
<style>

body {
background-color: #f8f9fa;
font-family: "Poppins", sans-serif;
}

.custom-navbar {
border-bottom: 1px solid lightgray;
background-color: rgb(0, 0, 0);
padding-bottom: 5px;
}

.nav-item {
position: relative;
}

.nav-link {
color: white;
}

.nav-link:hover {
color: #fff;
}

.nav-item:hover::after {
content: "";
position: absolute;
left: 0;
bottom: -6px;
width: 100%;
height: 2px;
background-color: #fff;
}

/* Navbar layout adjustments */
.navbar .container {
display: flex;
align-items: center;
justify-content: space-between;
}

/* Keep navbar links centered */
.navbar-nav {
flex-grow: 1;
justify-content: center;
}

/* Profile image styles */
.profile-img {
width: 40px;
height: 40px;
border-radius: 50%;
cursor: pointer;
}

/* Logo styling */
.logo-img {
width: 50px;
height: auto;
}

/* Avatar dropdown (Desktop) */
.avatar-container {
position: relative;
display: flex;
align-items: center;
}

.dropdown-menu {
position: absolute;
right: 0;
top: 50px;
display: none;
background-color: white;
box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
border-radius: 5px;
min-width: 150px;
z-index: 1000;
}

.dropdown-menu a {
padding: 10px;
display: block;
color: black;
text-decoration: none;
}

.dropdown-menu a:hover {
background-color: #f0f0f0;
}

/* Show dropdown on hover */
.avatar-container:hover .dropdown-menu {
display: block;
}

/* Mobile Avatar - Hidden by Default */
.mobile-avatar {
display: none;
text-align: center;
padding: 10px;
border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.mobile-avatar img {
width: 50px;
height: 50px;
border-radius: 50%;
}

/* Show mobile avatar when toggled */
@media (max-width: 991px) {
.desktop-avatar {
    display: none;
}

.mobile-avatar {
    display: block;
}
}

/* Change hamburger icon color to white */
.navbar-toggler {
border-color: rgba(255, 255, 255, 0.5); /* Light white border */
}

.navbar-toggler-icon {
filter: invert(1); /* Turns the black SVG icon to white */
}



.custom-card {
    position: relative;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    height: 100px;
    padding: 0;
    overflow: hidden;
    cursor: pointer;
}
.custom-card img {
    width: 70px;
    height: 100%;
    border-radius: 10px 0 0 10px;
    object-fit: cover;
}
.custom-card-content {
    flex-grow: 1;
    padding: 15px;
}
.menu-container {
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;

}

.menu-container .menu {
    display: none;
    position: absolute;
    top: 16px;
    right: 0;
    background: white;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    border-radius: 5px;
    padding: 0px;
    min-width: 100px;
    z-index: 1000;
}

/* Show menu on click */
.menu-container.active .menu {
    display: block;
}

/* Ensure it doesn't shift content */
.menu a {
    display: block;
    padding: 5px 12px;
    text-decoration: none;
    color: #333;
    white-space: nowrap;
    font-size: 16px;
}

.menu a:hover {
    background: #f1f1f1;
}

.add-new-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #fff;
    border-radius: 10px;
    height: 100px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
    cursor: pointer;
}
.add-new-card i {
    font-size: 20px;
    background: #e0e7ff;
    border-radius: 50%;
    padding: 10px;
}

.photo-card, .add-image-card {
    height: 245px; /* Set a fixed height for all cards */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 10px;
    overflow: hidden;
    cursor: pointer;
}

.photo-card img {
    width: 100%;
    height:80%;
    object-fit: cover;
    border-radius: 8px;
}

.photo-info {
    width: 100%;
    padding: 5px 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
}

.photo-info p {
    margin: 0;
    font-weight: 500;
}

.add-image-card {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: #f8f9fa;
    color: #6c757d;
    font-size: 18px;
    font-weight: 500;
    border: 2px dashed #ccc;
}

.add-image-card i {
    font-size: 24px;
}

.modal-content {
    /* color: white; */
    border-radius: 10px;
}
.image-preview {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}
.image-preview img {
    width: 50px;
    height: 50px;
    border-radius: 5px;
    object-fit: cover;
}
.remove-btn {
    cursor: pointer;
    color: red;
    font-weight: bold;
    margin-left: 10px;
}
.form-control {
    background: transparent;
    color: #000;
    border: 1px solid #444;
}
#savedImages {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 20px;
}
.saved-image-card {
    background: #333;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
}
.saved-image-card img {
    width: 100px;
    height: 100px;
    border-radius: 5px;
    object-fit: cover;
    display: block;
    margin: auto;
}

</style>
@endpush
