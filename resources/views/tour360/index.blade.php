@extends('layouts.redesign')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="favourites-section mb-4">
                <h5>Favourites</h5>
                <div class="favourite-items">
                    <!-- Favourite items will go here -->
                </div>
            </div>
            
            <div class="projects-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Tishman Speyer</h5>
                    <div class="sort-dropdown">
                        <select class="form-select">
                            <option>Recently added</option>
                            <!-- Add other sort options -->
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Project cards will go here -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="path_to_living_room_image" class="card-img-top" alt="Living Room">
                            <div class="card-body">
                                <h6>Living Room</h6>
                                <p class="text-muted">Created: June 11th, 2024</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Add more project cards as needed -->
                    
                    <div class="col-md-4 mb-4">
                        <div class="card new-project-card">
                            <div class="card-body text-center">
                                <i class="fas fa-plus"></i>
                                <h6>Create New Project</h6>
                                <p>Start a new tour project</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 