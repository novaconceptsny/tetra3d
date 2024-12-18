<div>
    @if($selectedMap)
        <div class="tour-map-container">
            <div class="me-2" style="width: 250px">
                <ul class="list-group">
                    @foreach($tour->maps as $map)
                        <a href="javascript:void(0)" class="list-group-item {{ $map->id == $selectedMap->id ? 'active' : '' }}"
                           wire:click="selectMap({{ $map }})" wire:loading.class="disabled">{{ $map->name }}
                        </a>
                    @endforeach
                </ul>
            </div>
            <div class="floorPlan tour-map" 
                 x-intersect="setMapScale" 
                 wire:loading.remove
                 style="width: 100%; min-height: 500px;"
                 defaultwidth="{{$selectedMap->width}}" 
                 defaultheight="{{$selectedMap->height}}" 
                 data-image-url="{{ $selectedMap->getFirstMediaUrl('image') }}">

                 
                @php
                    $parameters = [
                        $tour,
                         'layout_id' => $layoutId,
                         'shared' => Route::is('shared-tours.show'),
                         'shared_tour_id' => $shared_tour_id ?? null
                    ]
               @endphp
                @foreach($selectedMap->spots as $spot)
                    <a href="{{ route('tours.show', array_merge($parameters, ['spot_id' => $spot->id]) )}}">
                        <div class="pin {{ $spot->id == $spot_id ? 'selected' : '' }}" top="{{ $spot->pivot->y }}" left="{{ $spot->pivot->x }}"
                             style="top: {{ $spot->pivot->y }}px; left: {{ $spot->pivot->x }}px;">
                        </div>
                        <div class="spotname {{ $spot->id == $spot_id ? 'selected' : '' }}" top="{{ $spot->pivot->y +80 }}" left="{{ $spot->pivot->x }}"
                             style="top: {{ $spot->pivot->y + 80 }}px; left: {{ $spot->pivot->x }}px;" >
                            {{ $spot->display_name }}
                        </div>
                    </a>
                @endforeach

            </div>
            <div wire:loading class="bg-light rounded-2" 
                 style="height: {{$selectedMap->height}}px; width: {{$selectedMap->width}}px">
            </div>
        </div>
    @else
        <div>
            <p class="text-center mb-0">Map not available</p>
        </div>
    @endif
</div>

@push('scripts')


    <script type="module">
        
        import * as THREE from 'three';

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('.view-map-btn a i').addEventListener('click', () => {
                setTimeout(() => {
                    initThreeJS();
                }, 500);
            });

            function initThreeJS() {
                // Get the container
                const floorPlanContainer = document.querySelector('.floorPlan.tour-map');
                
                if (!floorPlanContainer) {
                    console.error('Floor plan container not found');
                    return;
                }

                // Get the actual dimensions
                const containerWidth = floorPlanContainer.clientWidth;
                const containerHeight = floorPlanContainer.clientHeight;

                if (containerWidth === 0 || containerHeight === 0) {
                    console.error('Container has zero dimensions');
                    return;
                }

                // Create scene
                const scene = new THREE.Scene();
                scene.background = new THREE.Color(0xf0f0f0);

                // Set up orthographic camera
                const camera = new THREE.OrthographicCamera(
                    containerWidth / -2,
                    containerWidth / 2,
                    containerHeight / 2,
                    containerHeight / -2,
                    1,
                    1000
                );
                camera.position.z = 500;

                // Set up renderer
                const renderer = new THREE.WebGLRenderer();
                renderer.setSize(containerWidth, containerHeight);
                floorPlanContainer.appendChild(renderer.domElement);

                // Get map dimensions and image URL
                const mapWidth = parseInt(floorPlanContainer.getAttribute('defaultwidth'));
                const mapHeight = parseInt(floorPlanContainer.getAttribute('defaultheight'));
                const imageUrl = floorPlanContainer.getAttribute('data-image-url');

                // Create texture loader
                const textureLoader = new THREE.TextureLoader();
                
                // Load the texture and create plane
                textureLoader.load(imageUrl, (texture) => {
                    const geometry = new THREE.PlaneGeometry(mapWidth, mapHeight);
                    const material = new THREE.MeshBasicMaterial({ 
                        map: texture,
                        side: THREE.DoubleSide
                    });
                    const plane = new THREE.Mesh(geometry, material);
                    scene.add(plane);

                    // Adjust camera to fit plane
                    const scale = Math.max(mapWidth / containerWidth, mapHeight / containerHeight);
                    camera.left = (-containerWidth * scale) / 2;
                    camera.right = (containerWidth * scale) / 2;
                    camera.top = (containerHeight * scale) / 2;
                    camera.bottom = (-containerHeight * scale) / 2;
                    camera.updateProjectionMatrix();
                });

                // Animation loop
                function animate() {
                    requestAnimationFrame(animate);
                    renderer.render(scene, camera);
                }
                animate();

                // Handle window resize
                window.addEventListener('resize', () => {
                    const newWidth = floorPlanContainer.clientWidth;
                    const newHeight = floorPlanContainer.clientHeight;
                    
                    // Update renderer
                    renderer.setSize(newWidth, newHeight);

                    // Update camera
                    const scale = Math.max(mapWidth / newWidth, mapHeight / newHeight);
                    camera.left = (-newWidth * scale) / 2;
                    camera.right = (newWidth * scale) / 2;
                    camera.top = (newHeight * scale) / 2;
                    camera.bottom = (-newHeight * scale) / 2;
                    camera.updateProjectionMatrix();
                });
            }
        });

    </script>
@endpush
