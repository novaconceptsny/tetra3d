@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Tours" :active="true"/>
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="float-end">
                @can('create', \App\Models\Tour::class)
                <a href="{{ route('backend.tours.create') }}" class="btn btn-sm btn-outline-primary"><i
                        class="fal fa-plus"></i> {{ __('Add New') }}</a>
                @endcan
            </div>
            <h5 class="mb-0 ">{{ __('Tours') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="mb-3">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('3D Model') }}</th>
                        <th scope="col">{{ __('Spots') }}</th>
                        <th scope="col">{{ __('Surfaces') }}</th>
                        <th scope="col">{{ __('Company') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    @forelse($tours as $tour)
                        <tr>
                            <td><a href="{{ route('tours.show', $tour) }}" target="_blank">{{ $tour->name }}</a></td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" class="spot-toggle" data-spot-id="{{ $tour->id }}" {{ $tour['has_model'] ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                                
                                <!-- Add modal for each tour -->
                                <div class="modal fade" id="confirmModal{{ $tour->id }}" tabindex="-1" aria-labelledby="confirmModalLabel{{ $tour->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmModalLabel{{ $tour->id }}">Confirm Change</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body" id="modalBody{{ $tour->id }}">
                                                <!-- Modal message will be set dynamically -->
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" class="btn btn-primary confirm-toggle" data-tour-id="{{ $tour->id }}">Confirm</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td><a href="{{ route('backend.tours.spots.index', $tour) }}">{{ $tour->spots_count }} Spots</a></td>
                            <td><a href="{{ route('backend.tours.surfaces.index', $tour) }}">{{ $tour->surfaces_count }} Surfaces</a></td>
                            <td>{{ $tour->company->name }}</td>
                            <td>
                                <x-backend::dropdown.container permission="viewany|update|delete" :permission_params="$tour">
                                    <x-backend::dropdown.item
                                        permission="viewany" :permission_params="\App\Models\Surface::class"
                                        :route="route('backend.tours.surfaces.index', $tour)">
                                        <i class="fal fa-rectangle-landscape mr-1"></i> {{ __('Surfaces') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="viewany" :permission_params="\App\Models\Spot::class"
                                        :route="route('backend.tours.spots.index', $tour)">
                                        <i class="fal fa-circle-notch mr-1"></i> {{ __('Spots') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="update" :permission_params="$tour"
                                        :route="route('backend.tours.edit', $tour)">
                                        <i class="fal fa-pen mr-1"></i> {{ __('Edit') }}
                                    </x-backend::dropdown.item>

                                    <x-backend::dropdown.item
                                        permission="delete" :permission_params="$tour"
                                        class="text-danger" data-bs-toggle="modal"
                                        data-bs-target="#confirm_tour_{{ $tour->id }}">
                                        <i class="fa fa-trash mr-1"></i> {{ __('Delete') }}
                                    </x-backend::dropdown.item>
                                </x-backend::dropdown.container>
                            </td>
                            <x-backend::modals.confirm
                                permission="edit" :permission_params="$tour"
                                :route="route('backend.tours.destroy', $tour)"
                                :model="$tour" :button="false"
                            />
                        </tr>
                    @empty
                        <x-backend::layout.tr-no-record/>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-0"></div>
    </div>

    @section('scripts')
    <script>
        document.querySelectorAll('.spot-toggle').forEach(checkbox => {
            checkbox.addEventListener('change', function(e) {
                // Prevent the default checkbox behavior
                e.preventDefault();
                const tourId = this.dataset.spotId;
                const currentState = this.checked;
                
                // Show confirmation modal for both enabling and disabling
                const modal = new bootstrap.Modal(document.getElementById(`confirmModal${tourId}`));
                const modalBody = document.getElementById(`modalBody${tourId}`);
                
                // Set appropriate message based on the action
                modalBody.textContent = currentState 
                    ? "Are you sure you want to switch this tour to 3D?"
                    : "Are you sure you want to disable 3D mode for this tour?";
                
                // Reset checkbox to its original state when showing modal
                this.checked = !currentState;
                modal.show();
            });
        });

        // Add event listeners for confirm buttons
        document.querySelectorAll('.confirm-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const tourId = this.dataset.tourId;
                const checkbox = document.querySelector(`.spot-toggle[data-spot-id="${tourId}"]`);
                const modal = bootstrap.Modal.getInstance(document.getElementById(`confirmModal${tourId}`));
                
                updateTourModel(tourId, checkbox)
                    .then(success => {
                        if (success) {
                            modal.hide();
                        }
                    });
            });
        });

        function updateTourModel(tourId, checkbox) {
            return fetch(`/backend/tours/${tourId}/toggle-model`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    checkbox.checked = !checkbox.checked;
                    return true;
                }
                return false;
            })
            .catch(error => {
                console.error('Error:', error);
                return false;
            });
        }
    </script>
    @endsection
@endsection
