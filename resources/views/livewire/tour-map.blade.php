<div>
    @if($selectedMap)
        <div class="tour-map-container d-flex">
            <div class="toolbar me-3" style="width: 250px">
                @if($tourModel)
                    <div class="btn-group-vertical w-100 mb-3">
                        <button class="btn btn-outline-primary" id="drawModeBtn">
                            <i class="fas fa-pen"></i> Add Wall
                        </button>
                        <button class="btn btn-outline-primary" id="editModeBtn">
                            <i class="fas fa-edit"></i> Edit Wall
                        </button>
                    </div>
                @endif
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
            // Add camera to global scope at the top level
            let camera, scene, renderer;
            let drawnLines = [];
            let currentMode = 'none';
            let selectedLine = null;
            let isInitialized = false;

            // Add these variables near the top where other state variables are declared
            let isDraggingLine = false;
            let dragStartPoint = null;
            let dragOffset = null;

            // Add this variable near other state variables at the top
            let currentDrawingLabel = null;

            document.querySelector('.view-map-btn a i').addEventListener('click', () => {
                if (isInitialized) {
                    return;
                }

                setTimeout(() => {
                    initThreeJS();
                }, 500);
            });
            let isDrawing = false;

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

                let isDragging = false;
                let draggedPoint = null;
                let endPoints = [];

                // Get the actual dimensions
                const containerWidth = floorPlanContainer.clientWidth;
                const containerHeight = floorPlanContainer.clientHeight;

                if (containerWidth === 0 || containerHeight === 0) {
                    console.error('Container has zero dimensions');
                    return;
                }

                // Create scene
                scene = new THREE.Scene();
                scene.background = new THREE.Color(0xf0f0f0);

                // Set up orthographic camera
                camera = new THREE.OrthographicCamera(
                    containerWidth / -2,
                    containerWidth / 2,
                    containerHeight / 2,
                    containerHeight / -2,
                    1,
                    1000
                );
                camera.position.z = 500;

                // Set up renderer
                renderer = new THREE.WebGLRenderer();
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

                    const mesh = new THREE.Mesh(thickGeometry, material);
                    mesh.userData.isDrawnLine = true;
                    return mesh;
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
                        linewidth: 1
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

                // Mode handling
                const drawModeBtn = document.getElementById('drawModeBtn');
                const editModeBtn = document.getElementById('editModeBtn');

                function updateModeButtons() {
                    // Reset all buttons
                    drawModeBtn.classList.remove('active');
                    editModeBtn.classList.remove('active');

                    // Clean up based on previous mode
                    if (currentMode === 'draw') {
                        if (drawingLine) {
                            scene.remove(drawingLine);
                            drawingLine = null;
                        }
                        if (guideLine) {
                            scene.remove(guideLine);
                            guideLine = null;
                        }
                        isDrawing = false;
                        startPoint = null;
                        points = [];
                    } else if (currentMode === 'edit') {
                        if (selectedLine) {
                            resetLineColor(selectedLine);
                            removeEndPoints();
                            selectedLine = null;
                        }
                        isDragging = false;
                        draggedPoint = null;
                    }

                    // Set new mode and update UI
                    if (currentMode === 'draw') {
                        drawModeBtn.classList.add('active');
                    } else if (currentMode === 'edit') {
                        editModeBtn.classList.add('active');
                    }
                }

                if(drawModeBtn){
                    drawModeBtn.addEventListener('click', () => {
                        // Toggle draw mode
                        if (currentMode === 'draw') {
                            currentMode = 'none';
                        } else {
                            currentMode = 'draw';
                        }
                        updateModeButtons();
                    });
                }

                if(editModeBtn){
                    editModeBtn.addEventListener('click', () => {
                        // Toggle edit mode
                        if (currentMode === 'edit') {
                            currentMode = 'none';
                        } else {
                            currentMode = 'edit';
                        }
                        updateModeButtons();
                    });
                }

                // Mouse event handlers
                floorPlanContainer.addEventListener('mousedown', (event) => {
                    if (currentMode === 'draw') {
                        const intersectionPoint = getIntersectionPoint(event);
                        if (intersectionPoint) {
                            isDrawing = true;
                            startPoint = intersectionPoint;
                            points = [startPoint];
                            drawingLine = createThickLine([startPoint, startPoint.clone()]);
                            scene.add(drawingLine);
                        }
                    } else if (currentMode === 'edit') {
                        const intersects = getIntersects(event);
                        if (intersects.length > 0) {
                            const hitObject = intersects[0].object;
                            
                            // Reset previous selection
                            if (selectedLine && selectedLine !== hitObject) {
                                resetLineColor(selectedLine);
                                removeEndPoints();
                            }
                            
                            // Set new selection
                            selectedLine = hitObject;
                            setLineColor(selectedLine, 0xff0000);
                            showRemoveButton(selectedLine);
                            
                            // Start line dragging
                            isDraggingLine = true;
                            dragStartPoint = getIntersectionPoint(event);
                            
                            // Calculate offset from click point to line center
                            const lineCenter = new THREE.Vector3();
                            selectedLine.geometry.computeBoundingBox();
                            selectedLine.geometry.boundingBox.getCenter(lineCenter);
                            dragOffset = new THREE.Vector3().subVectors(lineCenter, dragStartPoint);
                        }
                    }
                });

                floorPlanContainer.addEventListener('mousemove', (event) => {
                    if (currentMode === 'draw' && isDrawing) {
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

                            // Calculate length
                            const length = calculateLineLength(points);
                            
                            // Update or create label
                            if (!currentDrawingLabel) {
                                currentDrawingLabel = document.createElement('div');
                                currentDrawingLabel.className = 'length-label';
                                floorPlanContainer.appendChild(currentDrawingLabel);
                            }
                            
                            // Update label position and content
                            updateLengthLabel(currentDrawingLabel, drawingLine, length);
                        }
                    } else if (currentMode === 'edit') {
                        if (isDraggingLine && selectedLine) {
                            const currentPoint = getIntersectionPoint(event);
                            if (currentPoint && dragStartPoint) {
                                // Calculate movement delta
                                const delta = new THREE.Vector3().subVectors(currentPoint, dragStartPoint);
                                
                                // Move the line
                                const positions = selectedLine.geometry.attributes.position;
                                for (let i = 0; i < positions.count; i++) {
                                    const x = positions.array[i * 3];
                                    const y = positions.array[i * 3 + 1];
                                    const z = positions.array[i * 3 + 2];
                                    
                                    positions.array[i * 3] = x + delta.x;
                                    positions.array[i * 3 + 1] = y + delta.y;
                                    positions.array[i * 3 + 2] = z;
                                }
                                positions.needsUpdate = true;
                                
                                // Update stored line data
                                const lineIndex = drawnLines.findIndex(line => 
                                    line.points[0].x === dragStartPoint.x &&
                                    line.points[0].y === dragStartPoint.y
                                );
                                
                                if (lineIndex !== -1) {
                                    drawnLines[lineIndex].points = drawnLines[lineIndex].points.map(point => ({
                                        x: point.x + delta.x,
                                        y: point.y + delta.y,
                                        z: point.z
                                    }));
                                }
                                
                                // Update drag start point for next movement
                                dragStartPoint.copy(currentPoint);
                            }
                        }
                    }
                });

                floorPlanContainer.addEventListener('mouseup', () => {
                    if (currentMode === 'draw' && isDrawing) {
                        if (points.length >= 2) {
                            drawnLines.push({
                                points: points.map(p => ({ x: p.x, y: p.y, z: p.z }))
                            });
                            
                            // Create permanent label for the finished line
                            showLengthLabel(drawingLine, calculateLineLength(points));
                        }
                        
                        // Remove the temporary drawing label
                        if (currentDrawingLabel) {
                            currentDrawingLabel.remove();
                            currentDrawingLabel = null;
                        }
                        
                        isDrawing = false;
                    } else if (currentMode === 'edit') {
                        isDraggingLine = false;
                        dragStartPoint = null;
                        dragOffset = null;
                    }
                });

                // Add this function to handle line color changes
                function setLineColor(line, color) {
                    const material = line.material;
                    material.color.setHex(color);
                    material.needsUpdate = true;

                    // Show length when line is selected
                    if (color === 0xff0000) { // Selected state
                        const positions = line.geometry.attributes.position.array;
                        const points = [
                            { x: positions[0], y: positions[1], z: positions[2] },
                            { x: positions[positions.length - 3], y: positions[positions.length - 2], z: positions[positions.length - 1] }
                        ];
                        const length = calculateLineLength(points);
                        showLengthLabel(line, length);
                    } else {
                        // Remove length label when deselected
                        const label = document.querySelector(`[data-line-id="${line.uuid}"]`);
                        if (label) {
                            label.remove();
                        }
                    }
                }

                function resetLineColor(line) {
                    setLineColor(line, 0x000000);
                }

                // Initialize with no mode active
                updateModeButtons();

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

                // Add these functions for endpoint management
                function createEndPoint(position, pointType, parentLine) {
                    const geometry = new THREE.SphereGeometry(5);
                    const material = new THREE.MeshBasicMaterial({ color: 0xff0000 });
                    const point = new THREE.Mesh(geometry, material);
                    point.position.copy(position);
                    point.userData.isEndPoint = true;
                    point.userData.pointType = pointType;
                    point.userData.parentLine = parentLine;
                    return point;
                }

                function createEndPoints(line) {
                    const positions = line.geometry.attributes.position;
                    const startPoint = createEndPoint(
                        new THREE.Vector3(positions.array[0], positions.array[1], positions.array[2]),
                        'start',
                        line
                    );
                    const endPoint = createEndPoint(
                        new THREE.Vector3(
                            positions.array[positions.array.length - 3],
                            positions.array[positions.array.length - 2],
                            positions.array[positions.array.length - 1]
                        ),
                        'end',
                        line
                    );
                    endPoints = [startPoint, endPoint];
                    endPoints.forEach(point => scene.add(point));
                }

                function removeEndPoints() {
                    endPoints.forEach(point => {
                        if (point && point.parent) {
                            scene.remove(point);
                        }
                    });
                    endPoints = [];
                }

                function updateLine(oldLine, startPoint, endPoint) {
                    // Remove old line
                    scene.remove(oldLine);
                    
                    // Create new line with updated points
                    const newLine = createThickLine([startPoint, endPoint]);
                    scene.add(newLine);
                    
                    // Update the stored line data
                    const lineIndex = drawnLines.findIndex(line => 
                        line.points[0].x === oldLine.geometry.attributes.position.array[0] &&
                        line.points[0].y === oldLine.geometry.attributes.position.array[1]
                    );
                    
                    if (lineIndex !== -1) {
                        drawnLines[lineIndex] = {
                            points: [
                                { x: startPoint.x, y: startPoint.y, z: startPoint.z },
                                { x: endPoint.x, y: endPoint.y, z: endPoint.z }
                            ]
                        };
                    }
                    
                    return newLine;
                }

                // Add this function to check if a point is near the line
                function isPointNearLine(point, lineStart, lineEnd, threshold = 10) {
                    const line = new THREE.Line3(lineStart, lineEnd);
                    const closestPoint = new THREE.Vector3();
                    line.closestPointToPoint(point, true, closestPoint);
                    return point.distanceTo(closestPoint) < threshold;
                }

                // Add this constant near the top of your script with other constants
                const PIXELS_PER_METER = 100; // Adjust this value based on your floor plan scale

                // Update the calculateLineLength function to return meters
                function calculateLineLength(points) {
                    if (points.length < 2) return 0;
                    const start = new THREE.Vector3(points[0].x, points[0].y, points[0].z);
                    const end = new THREE.Vector3(points[1].x, points[1].y, points[1].z);
                    const lengthInPixels = start.distanceTo(end);
                    return lengthInPixels / PIXELS_PER_METER; // Convert to meters
                }

                // Update the showLengthLabel function
                function showLengthLabel(line, length) {
                    // Remove existing label if any
                    const existingLabel = document.querySelector(`[data-line-id="${line.uuid}"]`);
                    if (existingLabel) {
                        existingLabel.remove();
                    }

                    // Create label
                    const label = document.createElement('div');
                    label.className = 'length-label';
                    label.setAttribute('data-line-id', line.uuid);
                    label.textContent = `${length.toFixed(2)}m`; // Display with 2 decimal places
                    label.style.position = 'absolute';
                    label.style.zIndex = '1000';
                    label.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
                    label.style.color = 'white';
                    label.style.padding = '2px 6px';
                    label.style.borderRadius = '3px';
                    label.style.fontSize = '12px';

                    // Find the start and end points of the line
                    const positions = line.geometry.attributes.position.array;
                    const startPoint = new THREE.Vector3(positions[0], positions[1], positions[2]);
                    const endPoint = new THREE.Vector3(
                        positions[positions.length - 3],
                        positions[positions.length - 2],
                        positions[positions.length - 1]
                    );

                    // Calculate the middle point of the line
                    const middlePoint = new THREE.Vector3()
                        .addVectors(startPoint, endPoint)
                        .multiplyScalar(0.5);

                    // Convert middle point to screen coordinates
                    const vector = middlePoint.clone();
                    vector.project(camera);
                    
                    const floorPlanContainer = document.querySelector('.floorPlan.tour-map');
                    const rect = floorPlanContainer.getBoundingClientRect();
                    
                    const x = (vector.x + 1) / 2 * rect.width;
                    const y = (-vector.y + 1) / 2 * rect.height;

                    // Position label at the middle point, slightly above the line
                    label.style.left = `${x}px`;
                    label.style.top = `${y - 10}px`; // 10px above the line
                    label.style.transform = 'translate(-50%, -50%)';

                    // Add a small connecting line from the label to the measurement line
                    const connector = document.createElement('div');
                    connector.className = 'length-label-connector';
                    connector.style.position = 'absolute';
                    connector.style.left = '50%';
                    connector.style.top = '100%';
                    connector.style.width = '1px';
                    connector.style.height = '5px';
                    connector.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
                    label.appendChild(connector);

                    floorPlanContainer.appendChild(label);
                }

                // Create a separate function for updating label position and content
                function updateLengthLabel(label, line, length) {
                    label.textContent = `${length.toFixed(2)}m`;
                    label.style.position = 'absolute';
                    label.style.zIndex = '1000';
                    label.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
                    label.style.color = 'white';
                    label.style.padding = '2px 6px';
                    label.style.borderRadius = '3px';
                    label.style.fontSize = '12px';

                    // Calculate middle point
                    const positions = line.geometry.attributes.position.array;
                    const startPoint = new THREE.Vector3(positions[0], positions[1], positions[2]);
                    const endPoint = new THREE.Vector3(
                        positions[positions.length - 3],
                        positions[positions.length - 2],
                        positions[positions.length - 1]
                    );

                    const middlePoint = new THREE.Vector3()
                        .addVectors(startPoint, endPoint)
                        .multiplyScalar(0.5);

                    // Convert to screen coordinates
                    const vector = middlePoint.clone();
                    vector.project(camera);
                    
                    const rect = label.parentElement.getBoundingClientRect();
                    const x = (vector.x + 1) / 2 * rect.width;
                    const y = (-vector.y + 1) / 2 * rect.height;

                    label.style.left = `${x}px`;
                    label.style.top = `${y - 10}px`;
                    label.style.transform = 'translate(-50%, -50%)';
                }
            }

                        // Add this function to create and show remove button
            function showRemoveButton(line) {
                // Remove existing button if any
                const existingButton = document.querySelector('.remove-line-btn');
                if (existingButton) {
                    existingButton.remove();
                }

                // Create remove button
                const removeBtn = document.createElement('button');
                removeBtn.className = 'remove-line-btn btn btn-danger btn-sm';
                removeBtn.innerHTML = '<i class="fas fa-trash"></i>';
                removeBtn.style.position = 'absolute';
                removeBtn.style.zIndex = '1000';

                // Calculate center position of the line
                const positions = line.geometry.attributes.position.array;
                let centerX = 0, centerY = 0;
                let count = 0;
                for (let i = 0; i < positions.length; i += 3) {
                    centerX += positions[i];
                    centerY += positions[i + 1];
                    count++;
                }
                centerX /= count;
                centerY /= count;

                // Convert 3D position to screen coordinates
                const vector = new THREE.Vector3(centerX, centerY, 0);
                vector.project(camera);
                
                const floorPlanContainer = document.querySelector('.floorPlan.tour-map');
                const rect = floorPlanContainer.getBoundingClientRect();
                
                const x = (vector.x + 1) / 2 * rect.width;
                const y = (-vector.y + 1) / 2 * rect.height;

                // Position button above the line
                removeBtn.style.left = `${x}px`;
                removeBtn.style.top = `${y - 30}px`; // 30px above the center

                // Add click handler
                removeBtn.addEventListener('click', () => {
                    // Remove the line from the scene
                    scene.remove(line);
                    
                    // Remove from stored lines array
                    const lineIndex = drawnLines.findIndex(l => l === line);
                    if (lineIndex !== -1) {
                        drawnLines.splice(lineIndex, 1);
                    }
                    
                    // Remove the button
                    removeBtn.remove();
                    
                    // Reset selection
                    selectedLine = null;
                });

                // Add button to container
                floorPlanContainer.appendChild(removeBtn);
            }

            // Update getIntersects function to use the global camera and scene
            function getIntersects(event) {
                const floorPlanContainer = document.querySelector('.floorPlan.tour-map');
                const rect = floorPlanContainer.getBoundingClientRect();
                const x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
                const y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

                const raycaster = new THREE.Raycaster();
                raycaster.setFromCamera(new THREE.Vector2(x, y), camera);
                
                // Filter only drawn lines from scene children
                const drawnObjects = scene.children.filter(obj => obj.userData.isDrawnLine);
                return raycaster.intersectObjects(drawnObjects);
            }
        });

    </script>

    <style>
    .btn.active {
        background-color: #0d6efd;
        color: white;
    }

    .toolbar {
        background-color: #f8f9fa;
        padding: 1rem;
        border-right: 1px solid #dee2e6;
    }

    .remove-line-btn {
        position: absolute;
        padding: 4px 8px;
        border-radius: 4px;
        cursor: pointer;
        transform: translate(-50%, -50%);
        z-index: 1000;
    }

    .remove-line-btn:hover {
        opacity: 0.9;
    }

    .length-label {
        pointer-events: none;
        user-select: none;
        white-space: nowrap;
        position: relative; /* For connector positioning */
    }

    .length-label-connector {
        pointer-events: none;
    }

    </style>
@endpush

