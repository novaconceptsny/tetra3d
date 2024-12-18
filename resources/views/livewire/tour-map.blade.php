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
            // Store lines data globally
            let drawnLines = [];
            let isInitialized = false;

            document.querySelector('.view-map-btn a i').addEventListener('click', () => {
                if (isInitialized) {
                    return;
                }

                setTimeout(() => {
                    initThreeJS();
                }, 500);
            });

            function initThreeJS() {
                const floorPlanContainer = document.querySelector('.floorPlan.tour-map');
                
                if (!floorPlanContainer) {
                    console.error('Floor plan container not found');
                    return;
                }

                // Remove existing canvas but keep line data
                const existingCanvas = floorPlanContainer.querySelector('canvas');
                if (existingCanvas) {
                    existingCanvas.remove();
                }

                let isDrawing = false;
                let drawingLine = null;
                let startPoint = null;
                let points = [];
                const LINE_THICKNESS = 10;

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

                // Function to recreate stored lines
                function recreateStoredLines() {
                    drawnLines.forEach(lineData => {
                        const line = createThickLine(lineData.points);
                        scene.add(line);
                    });
                }

                // Function to create thick line
                function createThickLine(points) {
                    const geometry = new THREE.BufferGeometry();
                    const positions = new Float32Array(points.length * 3);
                    
                    for (let i = 0; i < points.length; i++) {
                        positions[i * 3] = points[i].x;
                        positions[i * 3 + 1] = points[i].y;
                        positions[i * 3 + 2] = points[i].z;
                    }
                    
                    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

                    // Create vertices for the thick line
                    const vertices = [];
                    for (let i = 0; i < points.length - 1; i++) {
                        const p1 = points[i];
                        const p2 = points[i + 1];
                        
                        // Calculate direction vector
                        const direction = new THREE.Vector3()
                            .subVectors(p2, p1)
                            .normalize();
                        
                        // Calculate perpendicular vector
                        const perpendicular = new THREE.Vector3(-direction.y, direction.x, 0)
                            .multiplyScalar(LINE_THICKNESS);
                        
                        // Create rectangle vertices
                        vertices.push(
                            p1.x + perpendicular.x, p1.y + perpendicular.y, p1.z,
                            p1.x - perpendicular.x, p1.y - perpendicular.y, p1.z,
                            p2.x + perpendicular.x, p2.y + perpendicular.y, p2.z,
                            
                            p1.x - perpendicular.x, p1.y - perpendicular.y, p1.z,
                            p2.x - perpendicular.x, p2.y - perpendicular.y, p2.z,
                            p2.x + perpendicular.x, p2.y + perpendicular.y, p2.z
                        );
                    }

                    const thickGeometry = new THREE.BufferGeometry();
                    thickGeometry.setAttribute(
                        'position',
                        new THREE.Float32BufferAttribute(vertices, 3)
                    );

                    const material = new THREE.MeshBasicMaterial({
                        color: 0x000000,
                        side: THREE.DoubleSide
                    });

                    return new THREE.Mesh(thickGeometry, material);
                }

                // Function to get intersection point with plane
                function getIntersectionPoint(event) {
                    const floorPlanContainer = document.querySelector('.floorPlan.tour-map');
                    const rect = floorPlanContainer.getBoundingClientRect();
                    const x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
                    const y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

                    const raycaster = new THREE.Raycaster();
                    raycaster.setFromCamera(new THREE.Vector2(x, y), camera);

                    const plane = scene.children.find(child => child instanceof THREE.Mesh);
                    if (!plane) return null;

                    const intersects = raycaster.intersectObject(plane);
                    return intersects.length > 0 ? intersects[0].point : null;
                }

                let guideLine = null;
                const SNAP_THRESHOLD = 5; // Degrees threshold for snapping
                
                // Function to create guide line
                function createGuideLine(start, end, color = 0x666666) {
                    const geometry = new THREE.BufferGeometry().setFromPoints([start, end]);
                    const material = new THREE.LineDashedMaterial({
                        color: color,
                        dashSize: 5,
                        gapSize: 5,
                    });
                    const line = new THREE.Line(geometry, material);
                    line.computeLineDistances(); // Required for dashed lines
                    return line;
                }

                // Function to calculate angle between two points
                function calculateAngle(start, end) {
                    const dx = end.x - start.x;
                    const dy = end.y - start.y;
                    return Math.atan2(dy, dx) * 180 / Math.PI;
                }

                // Function to check if angle is near vertical or horizontal
                function checkGuideAngle(angle) {
                    // Normalize angle to 0-360
                    angle = ((angle % 360) + 360) % 360;
                    
                    // Check vertical (90° or 270°)
                    if (Math.abs(angle - 90) < SNAP_THRESHOLD || 
                        Math.abs(angle - 270) < SNAP_THRESHOLD) {
                        return 'vertical';
                    }
                    
                    // Check horizontal (0° or 180°)
                    if (Math.abs(angle) < SNAP_THRESHOLD || 
                        Math.abs(angle - 180) < SNAP_THRESHOLD) {
                        return 'horizontal';
                    }
                    
                    return null;
                }

                // Function to create snapped point
                function createSnappedPoint(startPoint, currentPoint, guideType) {
                    if (guideType === 'vertical') {
                        return new THREE.Vector3(startPoint.x, currentPoint.y, currentPoint.z);
                    } else if (guideType === 'horizontal') {
                        return new THREE.Vector3(currentPoint.x, startPoint.y, currentPoint.z);
                    }
                    return currentPoint;
                }

                // Mouse event handlers
                floorPlanContainer.addEventListener('mousedown', (event) => {
                    const intersectionPoint = getIntersectionPoint(event);
                    if (intersectionPoint) {
                        isDrawing = true;
                        startPoint = intersectionPoint;
                        points = [startPoint];

                        drawingLine = createThickLine([startPoint, startPoint.clone()]);
                        scene.add(drawingLine);
                    }
                });

                floorPlanContainer.addEventListener('mousemove', (event) => {
                    if (isDrawing && drawingLine) {
                        const intersectionPoint = getIntersectionPoint(event);
                        if (intersectionPoint) {
                            // Remove previous guide line
                            if (guideLine) {
                                scene.remove(guideLine);
                                guideLine = null;
                            }

                            // Calculate angle and check for guides
                            const angle = calculateAngle(startPoint, intersectionPoint);
                            const guideType = checkGuideAngle(angle);
                            
                            let endPoint = intersectionPoint;
                            
                            if (guideType) {
                                // Create snapped point
                                endPoint = createSnappedPoint(startPoint, intersectionPoint, guideType);
                                
                                // Create guide line
                                const guideStart = new THREE.Vector3(
                                    guideType === 'vertical' ? startPoint.x : -1000,
                                    guideType === 'horizontal' ? startPoint.y : -1000,
                                    0
                                );
                                const guideEnd = new THREE.Vector3(
                                    guideType === 'vertical' ? startPoint.x : 1000,
                                    guideType === 'horizontal' ? startPoint.y : 1000,
                                    0
                                );
                                guideLine = createGuideLine(guideStart, guideEnd);
                                scene.add(guideLine);
                            }

                            // Update drawing line
                            scene.remove(drawingLine);
                            points = [startPoint, endPoint];
                            drawingLine = createThickLine(points);
                            scene.add(drawingLine);
                        }
                    }
                });

                floorPlanContainer.addEventListener('mouseup', () => {
                    if (isDrawing) {
                        // Store the line data
                        drawnLines.push({
                            points: points.map(p => ({ x: p.x, y: p.y, z: p.z }))
                        });
                        
                        // Remove guide line
                        if (guideLine) {
                            scene.remove(guideLine);
                            guideLine = null;
                        }
                        
                        isDrawing = false;
                        drawingLine = null;
                        points = [];
                    }
                });

                // Load texture and create plane
                const textureLoader = new THREE.TextureLoader();
                textureLoader.load(floorPlanContainer.getAttribute('data-image-url'), (texture) => {
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

                    // Recreate stored lines after plane is created
                    recreateStoredLines();
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
                    
                    renderer.setSize(newWidth, newHeight);

                    const scale = Math.max(mapWidth / newWidth, mapHeight / newHeight);
                    camera.left = (-newWidth * scale) / 2;
                    camera.right = (newWidth * scale) / 2;
                    camera.top = (newHeight * scale) / 2;
                    camera.bottom = (-newHeight * scale) / 2;
                    camera.updateProjectionMatrix();
                });

                isInitialized = true;

                // Clean up when modal is closed
                const modal = document.getElementById('tourMapModal');
                if (modal) {
                    modal.addEventListener('hidden.bs.modal', () => {
                        const canvas = floorPlanContainer.querySelector('canvas');
                        if (canvas) {
                            canvas.remove();
                        }
                        isInitialized = false;
                        // Note: We don't clear drawnLines here to preserve the data
                    });
                }
            }
        });

    </script>
@endpush
