import ArtSelection from "./ArtSelection.js";
import CanvasApi from "./CanvasApi.js";

class CanvasManager {
    constructor(data) {
        this.active = false;
        this.unsavedChanges = false;

        this.canvasId = data.canvasId;

        this.spot_id = data.spotId;
        this.user_id = data.userId;
        this.assignedArtworks = data.assignedArtworks;
        this.surfaceStateId = data.surfaceStateId;
        console.log(this.surfaceStateId, "surfaceStateId")
        console.log(this.assignedArtworks, "assignedArtworks")
        this.surface = data.surface;
        this.surfaceData = this.surface.data;
        this.latestState = data.latestState;
        this.photoEditable = data.photoEditable || false;

        this.canvasApi = new CanvasApi({
            updateEndpoint: data.updateEndpoint,
            layoutId: data.layoutId,
            hlookat: data.hlookat,
            vlookat: data.vlookat,
        });

        this.restrictBoundaries = false;
        this.canvasState = {
            savedVersion: false,
            isOverlap: false,
            defaultScale: 1.0,
            actualWidthInch: 0,
            background: null,
            currentVersionData: null,
            assignedArtwork: [],
            modifiedVersion: {
                addedArtwork: [],
                removedArtwork: [],
                modifiedArtwork: [],
            },
            recentSelection: null,
        };
        
        // Track artwork counts for badge display
        this.artworkCounts = new Map(); // artworkId -> count
        this.artworkCanvas = new fabric.Canvas(this.canvasId, {
            enableRetinaScaling: true,
            skipOffscreen: false,
        });

        this.boundingBox = null;
        this.imgWidth = this.surface.data.img_width;
        this.imgHeight = this.surface.data.img_height;
        this.corners = this.surface.data.corners || [];
        this.areaSrcPoints = [];
        this.dstMat = null;
        this.M = null;
        this.baseWidth = null;
        this.baseScale = null;

        this.photoScale = 1;

        this.reverseScale = null;
        this.defaultScales = {};

        this.saveBtn = $('#save_btn');
        this.removeBtn = $('#remove_btn');
        this.cropBtn = $('#crop_btn');
        this.cancelBtn = $('#cancel_btn');
        this.saveAsBtn = $('#save_as_btn');
        this.confirmSaveBtn = $('#confirm_save_btn'); // save new button on modal
        this.return360Btn = $('#return_to_360');
        this.fileNameInput = $('#file_name');
        this.confirmationModal = $('#confirmation_modal');

        this.areaPolygon = null;

        this.initialize()
    }

    initialize() {
        const mainContent = document.querySelector('.main_content');    // canvas containing div

        let mainHeight = mainContent.offsetHeight,
            mainWidth = mainContent.offsetWidth;

        this.artworkCanvas.setWidth(mainWidth);
        this.artworkCanvas.setHeight(mainHeight);

        // Add drag and drop event listeners
        this.initializeDragAndDrop();

        // Here we need to write a program to calculate whether to scale height or width. Currently only scale width
        if ((this.imgWidth / this.imgHeight) >= (mainWidth / mainHeight)) {
            //The image is wider than the screen ratio
            //shrink by width
            this.baseScale = mainWidth / this.imgWidth; //Here is the relative data when the screen is zoomed
            this.reverseScale = 1 / this.baseScale;

            if (this.photoEditable) {
                this.photoScale = mainWidth / this.surfaceData.bounding_box_width;
            }
        } else {
            //The picture is narrower than the screen ratio
            //shrink by height
            this.baseScale = mainHeight / this.imgHeight; //Here is the relative data when the screen is zoomed
            this.reverseScale = 1 / this.baseScale;

            if (this.photoEditable) {
                this.photoScale = mainHeight / this.surfaceData.bounding_box_height;
            }
        }



        this.boundingBox = new fabric.Rect({
            originX: 'left',
            originY: 'top',
            left: 320,
            top: 290,
            width: 810,
            height: 470,
            fill: 'transparent',
            stroke: 'white',
            strokeWidth: 2,
            opacity: 0.2,
            hasControls: false,
            selectable: false,
            selection: false,
        });

        this.artworkCanvas.add(this.boundingBox);


        $(document).on('keydown', (event) => this.handleDeleteKeyOnArtwork(event));
        this.fileNameInput.on('keydown', (event) => this.handleEnterKeyOnSaveAs(event));
        this.removeBtn.on('click', () => this.removeSelectedArtwork());
        this.cancelBtn.on('click', () => this.removeAllArtwork());
        this.saveBtn.on('click', () => this.updateSavedVersion());
        this.saveAsBtn.on('click', () => this.saveNewVersion());
        this.confirmSaveBtn.on('click', () => this.confirmSave());
        this.return360Btn.on('click', (event) => this.returnTo360(event));
        this.cropBtn.hide();
        this.disableSaveButton();

        /*this.confirmationModal
            .on('show.bs.modal', () => this.cropBtn.hide())
            .on('hidden.bs.modal', () => this.cropBtn.show());*/


        this.addCanvasEvents();

        // Makes request to backend to retrieve all necessary data for a new canvas.
        this.requestCanvasBackgroundProperties(this.surface, this.baseScale);

        // Call out all the artwork and display it on the page.
        this.renderArtworks();

        this.registerArtworkSelectionEvent(this.photoEditable);
        this.registerCanvasUpdateEvent();
        
        // Initialize artwork counts from existing assigned artworks
        this.initializeArtworkCounts();
        
        // Make refreshArtworkBadges globally available
        window.refreshArtworkBadges = () => this.refreshArtworkBadges();

        // Add guide-related initialization
        this.initializeGuides();

        this.initializeArea(this.photoEditable, this.surfaceData);

        // Add area toggle button event listener
        document.getElementById('toggle-area')?.addEventListener('click', () => this.toggleArea());
    }

    initializeDragAndDrop() {
        // Handle drag and drop from artwork sidebar using Fabric.js events
        this.artworkCanvas.on('dragover', (event) => {
            // Fabric.js events have a different structure
            const e = event.e || event;
            if (e && e.preventDefault) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.artworkCanvas.setCursor('grab');
            
            // Add visual feedback to canvas
            this.showDropZone();
        });

        this.artworkCanvas.on('dragenter', (event) => {
            // Fabric.js events have a different structure
            const e = event.e || event;
            if (e && e.preventDefault) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.artworkCanvas.setCursor('grab');
            
            // Add visual feedback to canvas
            this.showDropZone();
        });

        this.artworkCanvas.on('dragleave', (event) => {
            // Fabric.js events have a different structure
            const e = event.e || event;
            // Only hide drop zone if we're actually leaving the canvas
            if (e && e.relatedTarget && !this.artworkCanvas.getElement().contains(e.relatedTarget)) {
                this.hideDropZone();
            }
        });

        this.artworkCanvas.on('drop', (event) => {
            // Fabric.js events have a different structure
            const e = event.e || event;
            
            if (e && e.preventDefault) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.artworkCanvas.setCursor('default');
            
            // Hide drop zone
            this.hideDropZone();

            // Get drop position relative to canvas
            const canvasRect = this.artworkCanvas.getElement().getBoundingClientRect();
            const dropX = (e ? e.clientX : 0) - canvasRect.left;
            const dropY = (e ? e.clientY : 0) - canvasRect.top;
            
            // Ensure drop position is within canvas bounds
            const boundedX = Math.max(0, Math.min(dropX, this.artworkCanvas.width));
            const boundedY = Math.max(0, Math.min(dropY, this.artworkCanvas.height));

            // Check if we're dropping an artwork from the sidebar
            const dataTransfer = e ? e.dataTransfer : null;
            if (dataTransfer) {
                const draggedArtwork = dataTransfer.getData('application/artwork');
                if (draggedArtwork) {
                    try {
                        const artworkData = JSON.parse(draggedArtwork);
                        this.handleArtworkDrop(artworkData, boundedX, boundedY);
                    } catch (error) {
                        console.error('Error parsing artwork data:', error);
                    }
                }   
            }
        });

        // Add drag start event listeners to artwork items in sidebar
        this.initializeArtworkDragListeners();
    }



    initializeArtworkDragListeners() {
        // Listen for drag start events on artwork items
        document.addEventListener('dragstart', (event) => {
            const artworkElement = event.target.closest('.artwork-img');
            if (artworkElement) {
                const artworkData = {
                    title: artworkElement.dataset.title,
                    imgUrl: artworkElement.dataset.imgUrl,
                    artworkId: artworkElement.dataset.artworkId,
                    scale: artworkElement.dataset.scale
                };
                
                event.dataTransfer.setData('application/artwork', JSON.stringify(artworkData));
                event.dataTransfer.effectAllowed = 'copy';
                
                // Add visual feedback
                artworkElement.classList.add('dragging');
                
                // Set drag image (optional - shows a preview while dragging)
                if (artworkElement.querySelector('img')) {
                    event.dataTransfer.setDragImage(artworkElement.querySelector('img'), 25, 25);
                }
            }
        });

        document.addEventListener('dragend', (event) => {
            const artworkElement = event.target.closest('.artwork-img');
            if (artworkElement) {
                artworkElement.classList.remove('dragging');
            }
        });
    }

    handleArtworkDrop(artworkData, dropX, dropY) {
        try {
            if (this.isInactive()) {
                console.log('Canvas is inactive, ignoring drop');
                return;
            }

            console.log('Dropping artwork:', artworkData, 'at position:', { x: dropX, y: dropY });

            if (!this.photoEditable) {
                // For non-photo editable surfaces, create regular artwork selection
                let newSelection = new ArtSelection(artworkData);
                this.placeSelectedImage(newSelection, dropY, dropX);
                this.unsavedChanges = true;
                this.toggleRemoveButton();
                
                // Show success feedback
             //   this.showDropSuccess(dropX, dropY);
            } else {
                // For photo editable surfaces, add warped artwork
                this.addWarpedArtwork(artworkData, dropX, dropY);
                
                // Show success feedback
                this.showDropSuccess(dropX, dropY);
            }
        } catch (error) {
            console.error('Error in handleArtworkDrop:', error);
        }
    }

    initializeArea(photoEditable, surfaceData) {
        const corners = surfaceData.corners;
        const boundingBoxLeft = surfaceData.bounding_box_left;
        const boundingBoxTop = surfaceData.bounding_box_top;

        if (photoEditable && corners.length > 0) {
            // Scale the corner points using baseScale
            const scaledPoints = corners.map(point => ({
                x: (point.x - boundingBoxLeft) * this.photoScale,
                y: (point.y - boundingBoxTop) * this.photoScale
            }));

            // Create a flat array of points for the polygon
            const points = [];
            scaledPoints.forEach(point => {
                points.push({ x: point.x, y: point.y });
                this.areaSrcPoints.push({ x: point.x, y: point.y });
            });

            // Create a polygon using the points
            this.areaPolygon = new fabric.Polygon(points, {
                fill: 'rgba(0, 255, 0, 0.1)',
                stroke: 'green',
                strokeWidth: 2,
                selectable: false,
                evented: false,
                objectCaching: false
            });

            // Add the polygon to the canvas
            this.artworkCanvas.add(this.areaPolygon);
            this.artworkCanvas.renderAll();

            // Initialize the toggle button state
            const button = document.getElementById('toggle-area');
            if (button) {
                button.setAttribute('data-hidden', 'false');
                button.innerHTML = '<i class="fal fa-eye"></i> Hide Area';
            }
        }
    }

    registerCanvasUpdateEvent() {
        document.addEventListener("canvasChanged", (event) => {
            this.active = event.detail.surfaceStateId === this.surfaceStateId;
            this.activeStateUpdated();
        });
    }

    renderArtworks() {
        if (!this.assignedArtworks) {
            return;
        }

        this.assignedArtworks.forEach((art) => {
            art = this.remapArtVariable(art);
            art = this.deserializeArtSelection(art);
            this.placeSelectedImage(art, (art.getTopPosition() * this.baseScale), (art.getLeftPosition() * this.baseScale));
        });
        this.canvasState['savedVersion'] = true;
        this.canvasState['currentVersionData'] = this.latestState;
        this.disableSaveButton();
        this.addSavedVersionEvents();
    }

    registerArtworkSelectionEvent(photoEditable) {
        $('#site__body').on('click', '.artwork-img', (el) => {
            if (!this.active) {
                return false
            }

            // Check if this was part of a drag operation
            if (el.currentTarget.classList.contains('dragging')) {
                return false;
            }

            let target = el.currentTarget;

            if (!photoEditable) {
                let newSelection = this.newArtworkSelection(target);
                this.placeSelectedImage(newSelection);
                this.unsavedChanges = true;
                this.toggleRemoveButton();
            } else {
                let imgData = this.getSelectionData(target);
                this.addWarpedArtwork(imgData);
            }

        })
    }

    remapArtVariable(art) {
        let tempArr = [];
        let canvas_data = art.pivot;
        tempArr['title'] = art['name'];
        tempArr['imgUrl'] = art['image_url'];
        tempArr['artworkId'] = art['id'];
        tempArr['scale'] = art.data.scale;
        tempArr['topPosition'] = canvas_data['top_position'];
        tempArr['leftPosition'] = canvas_data['left_position'];
        tempArr['cropData'] = canvas_data['crop_data'];
        tempArr['overrideScale'] = canvas_data['override_scale'];
        return tempArr;
    }

    requestCanvasBackgroundProperties(surface, baseScale) {
        let data = surface['data'];

        this.renderCanvasBackground(surface['background_url'], baseScale);
        this.canvasState.background = surface['background_url'];
        this.canvasState['actualWidthInch'] = data['actual_width_inch'];
        /*if (surface['overlay_url'] !== null) {
            setCanvasOverlay(surface['overlay_url']);
        }*/
        this.setBoundingBoxProperties(
            data['bounding_box_top'] * baseScale, data['bounding_box_left'] * baseScale,
            data['bounding_box_height'] * baseScale, data['bounding_box_width'] * baseScale
        );
    }

    setBoundingBoxProperties(top, left, height, width) {
        this.boundingBox.top = top;
        this.boundingBox.left = left;
        this.boundingBox.height = height;
        this.boundingBox.width = width;
    }

    renderCanvasBackground(imgUrl, baseScale) {
        this.artworkCanvas.setBackgroundImage(imgUrl, this.artworkCanvas.renderAll.bind(this.artworkCanvas), {
            originX: 'left',
            originY: 'top',
            scaleX: baseScale,
            scaleY: baseScale,
            centeredScaling: true,
        });
    }

    setCanvasOverlay(imgUrl) {
        let overlay = new Image();
        overlay.src = imgUrl;

        overlay.onload = function () {
            this.artworkCanvas.setOverlayImage(imgUrl, this.artworkCanvas.renderAll.bind(this.artworkCanvas), {
                originX: 'left',
                originY: 'top',
                scaleX: 1.0,
                scaleY: 1.0,
            });
        };
    }

    getSelectionData(selectedElement) {
        let title = selectedElement.dataset.title;
        let imgUrl = selectedElement.dataset.imgUrl;
        let artworkId = selectedElement.dataset.artworkId;
        let scale = selectedElement.dataset.scale;

        if (window.location.protocol === 'https:' && imgUrl.startsWith('http:')) {
            // Replace the http:// with the current website origin
            imgUrl = `${window.location.origin}${new URL(imgUrl).pathname}`;
        }

        console.log(imgUrl, "imgurl")
        return { title, imgUrl, artworkId, scale };
    }

    newArtworkSelection(targetElement) {
        let data = this.getSelectionData(targetElement);
        return new ArtSelection(data);
    }

    addCanvasEvents() {
        this.artworkCanvas.on('object:selected', (options) => {
            if (this.isInactive()) {
                return
            }

            if (options.target) {
                this.removeBtn.show();
            }
        });

        this.artworkCanvas.on('selection:cleared', (options) => {
            if (this.isInactive()) {
                return
            }

            this.removeBtn.hide();
        });

        this.artworkCanvas.on('object:moving', function (options) {
            let object = options.target;
            this.positionCropBtn(object);

            if (options.target && this.restrictBoundaries) {
                // check boundaries
                let xyScale = defaultScales[object.id];

                const objectBottom = Math.abs(object.top + (object.height * xyScale));
                const objectRight = Math.abs(object.left + (object.width * xyScale));

                const topBound = this.boundingBox.top,
                    leftBound = this.boundingBox.left,
                    bottomBound = topBound + this.boundingBox.height,
                    rightBound = leftBound + this.boundingBox.width;

                if (object.top < topBound) {
                    object.set({ top: topBound + 2 });
                }

                if (object.left < leftBound) {
                    object.set({ left: leftBound + 2 });
                }

                if (objectBottom > bottomBound) {
                    let y = bottomBound - Math.abs(object.height * xyScale);
                    object.set({ top: y });
                }

                if (objectRight > rightBound) {
                    let x = rightBound - Math.abs(object.width * xyScale);
                    object.set({ left: x });
                }
            }
        }.bind(this));
    }

    addSavedVersionEvents() {
        let handleModification = (event, msg) => {
            this.unsavedChanges = true;
            this.toggleSaveButton();
        };

        this.artworkCanvas.on({
            'object:modified': handleModification,
            'object:removed': handleModification,
        });
    }

    applyAdaptiveRescale(image, scale, overrideScale) {
        let a = image.width;
        let b = this.boundingBox.width;
        let c = this.canvasState.actualWidthInch * scale;

        let adaptedScale = a * (b / c);

        image.scaleToWidth(adaptedScale, false);
        this.canvasState.defaultScale = image.scaleX;
        this.defaultScales[image.id] = image.scaleX;

        if (overrideScale != null) {
            c = this.canvasState.actualWidthInch * overrideScale;
        }
        adaptedScale = a * (b / c);
        image.scaleToWidth(adaptedScale, false);
    }

    placeSelectedImage(artSelection, topPos = this.boundingBox.top, leftPos = this.boundingBox.left) {
        let imgUrl = artSelection.imgUrl;

        // Generate unique instance ID for each placement
        const uniqueInstanceId = `${artSelection.artworkId}_${Math.random().toString(36).substr(2, 9)}`;

        fabric.Image.fromURL(imgUrl, function (myImg) {
            if (myImg._element == null) {
                alert("Image could not be loaded...");
            }

            let img1 = myImg.set({
                originX: "left",
                originY: "top",
                id: uniqueInstanceId, // Use unique instance ID
                originalArtworkId: artSelection.artworkId, // Store original artwork ID
                top: topPos,
                left: leftPos,
                angle: 0,
                hasControls: true,
                selectable: true,
                selection: true,
                borderColor: 'red',
                borderScaleFactor: 1,
                hasRotatingPoint: true,
                noScaleCache: false
            });

            let scale = artSelection.scale;

            let overrideScale = artSelection.overrideScale;

            if (scale == null) {
                scale = 96;
            }

            this.applyAdaptiveRescale(img1, scale, overrideScale);

            img1.hasControls = false;
            this.artworkCanvas.add(img1);
            this.artworkCanvas.renderAll();

            // Create a unique art selection instance with the unique ID
            const uniqueArtSelection = new ArtSelection({
                title: artSelection.title,
                imgUrl: artSelection.imgUrl,
                artworkId: artSelection.artworkId,
                scale: artSelection.scale,
                uniqueInstanceId: uniqueInstanceId
            });
            uniqueArtSelection.setTopPosition(topPos);
            uniqueArtSelection.setLeftPosition(leftPos);
            uniqueArtSelection.setCropData(artSelection.cropData);
            uniqueArtSelection.setOverrideScale(artSelection.overrideScale);
            
            this.canvasState.assignedArtwork.push(uniqueArtSelection);
            
            // Update artwork count
            this.incrementArtworkCount(artSelection.artworkId);

        }.bind(this), {
            crossOrigin: 'anonymous'
        });
    }

    handleDeleteKeyOnArtwork(key) {
        if (key.code === 'Delete') {
            this.removeSelectedArtwork();
        }
    }

    handleEnterKeyOnSaveAs(key) {
        if (key.code === 'Enter' || key.code === 'NumpadEnter') {
            key.preventDefault();
            this.confirmSaveBtn.click();
            return false;
        }
    }

    isAlreadySelected(artId) {
        // This method is no longer needed since we now allow multiple instances
        // of the same artwork. Each placement gets a unique instance ID.
        return false;
    }

    removeSelectedArtwork() {
        if (this.isInactive()) {
            return;
        }

        const selectedObjs = this.artworkCanvas.getActiveObjects();
        selectedObjs.forEach((obj) => {
            if (obj.isGuide) {
                // Remove associated labels for guide lines
                this.artworkCanvas.remove(obj.labelA);
                this.artworkCanvas.remove(obj.labelB);
                // Remove the guide from our guides array
                this.guides = this.guides.filter(guide => guide.line !== obj);
            }
            this.artworkCanvas.remove(obj);
            
            // Get artwork ID before removing from assignedArtwork
            const artworkId = obj.originalArtworkId || obj.id;
            
            // Remove by unique instance ID instead of artwork ID
            this.canvasState.assignedArtwork = this.canvasState.assignedArtwork.filter(art => {
                // For new instances, check against the unique ID
                if (art.uniqueInstanceId) {
                    return art.uniqueInstanceId !== obj.id;
                }
                // For legacy instances, check against artwork ID
                return art.getArtworkId() !== obj.id;
            });
            
            // Update artwork count
            this.decrementArtworkCount(artworkId);
        });

        this.removeBtn.hide();
    }

    removeAllArtwork() {
        this.artworkCanvas.getObjects()
            .filter((obj) => {
                return obj.type === 'image'
            })
            .map((obj) => {
                this.artworkCanvas.remove(obj)
            });
        this.canvasState.assignedArtwork = [];
        
        // Clear all artwork counts and update badges
        this.artworkCounts.clear();
        this.updateArtworkBadges();
    }

    saveNewVersion(event) {
        this.openSaveFileModal(event);
    }

    returnTo360(event) {
        event.preventDefault();

        if (this.isInactive()) {
            return;
        }

        // Hide all guides before returning
        this.guides.forEach(guide => {
            guide.line.visible = false;
            guide.labelA.visible = false;
            guide.labelB.visible = false;
        });
        this.artworkCanvas.renderAll();

        // Update the toggle button state
        const button = document.getElementById('toggle-guides');
        if (button) {
            button.setAttribute('data-hidden', 'true');
            button.innerHTML = '<i class="fal fa-eye"></i> Show Guides';
        }

        if (!this.surfaceStateId) {
            let artworks = this.canvasState.assignedArtwork.length;

            if (artworks < 1) {
                window.location = event.currentTarget.attributes.href.value
                return;
            }

            if (confirm("You have unsaved changes. Are you sure you want to proceed to 360?") === true) {
                window.location = event.currentTarget.attributes.href.value
                return;
            } else {
                this.saveNewVersion(event);
                return;
            }
        }

        this.updateSavedVersion();
    }

    updateSavedVersion() {
        if (this.isInactive()) {
            return;
        }

        if (this.canvasHasOverlap()) {
            window.alert('There is an overlap');
            return;
        }
        let clearTransientData = () => {
            this.canvasState.modifiedVersion.addedArtwork = [];
            this.canvasState.modifiedVersion.removedArtwork = [];
            this.canvasState.modifiedVersion.modifiedArtwork = [];
        };
        if (!this.canvasState.isOverlap) {
            this.setAssignmentProperties(this.reverseScale);
            let updates = this.trackChanges();

            this.disableSaveButton();

            let screenshots = this.exportArtAssignments(this.canvasState['currentVersionData'].version_name);

            this.canvasApi.updateSurfaceState({
                surfaceStateId: this.surfaceStateId,
                updates,
                screenshots,
                canvasState: this.canvasState,
                reverseScale: this.reverseScale,
                userId: this.user_id,
                spotId: this.spot_id,
            });
        }
    }

    exportArtAssignments(filename) {

        const captureCanvas = () => this.artworkCanvas.toDataURL({
            format: 'png',
        });

        const captureHotspot = () => {
            this.boundingBox.opacity = 0;  // hide bounding box
            this.artworkCanvas.backgroundImage.opacity = 0;  // hide background image

            const href = this.artworkCanvas.toDataURL({
                format: 'png',
                left: this.boundingBox.left,
                top: this.boundingBox.top,
                width: this.boundingBox.width + 2,
                height: this.boundingBox.height + 2,
            });

            this.boundingBox.opacity = 0.2;
            this.artworkCanvas.backgroundImage.opacity = 100;
            return href;
        };

        return { 'thumbnail': captureCanvas(), 'hotspot': captureHotspot() };
    }

    trackChanges() {
        const latestDeserialized = this.canvasState.assignedArtwork.map(this.deserializeArtSelection);

        this.canvasState.modifiedVersion.removedArtwork = this.canvasState.assignedArtwork.filter(
            obj1 => !latestDeserialized.some(obj2 => {
                // Compare by unique instance ID if available, otherwise by artwork ID
                return (obj1.uniqueInstanceId && obj1.uniqueInstanceId === obj2.uniqueInstanceId) ||
                       (!obj1.uniqueInstanceId && obj2.getArtworkId() === obj1.getArtworkId());
            })
        );

        this.canvasState.modifiedVersion.addedArtwork = latestDeserialized.filter(
            obj1 => !this.canvasState.assignedArtwork.some(obj2 => {
                // Compare by unique instance ID if available, otherwise by artwork ID
                return (obj1.uniqueInstanceId && obj1.uniqueInstanceId === obj2.uniqueInstanceId) ||
                       (!obj1.uniqueInstanceId && obj2.getArtworkId() === obj1.getArtworkId());
            })
        );

        this.canvasState.assignedArtwork.forEach(obj1 => {
            const obj2 = latestDeserialized.find(item => {
                // Compare by unique instance ID if available, otherwise by artwork ID
                return (obj1.uniqueInstanceId && obj1.uniqueInstanceId === item.uniqueInstanceId) ||
                       (!obj1.uniqueInstanceId && item.getArtworkId() === obj1.getArtworkId());
            });

            if (obj2 &&
                (
                    obj1.getTopPosition() !== obj2.getTopPosition() ||
                    obj1.getLeftPosition() !== obj2.getLeftPosition() ||
                    obj1.getCropData() !== obj2.getCropData()
                )
            ) {
                this.canvasState.modifiedVersion.modifiedArtwork.push(obj1);
            }
        });

        return {
            "assignedArtwork": this.canvasState.assignedArtwork,
            "removed": this.canvasState.modifiedVersion.removedArtwork,
            "added": this.canvasState.modifiedVersion.addedArtwork,
            "modified": this.canvasState.modifiedVersion.modifiedArtwork
        };
    }

    async confirmSave() {
        if (this.isInactive()) {
            return;
        }

        let filenameInput = document.getElementById("file_name");
        let confirmBtn = document.getElementById('confirm_save_btn');

        let filename = filenameInput.value.trim();
        // let filenameExists = await isExistingFilename(filename);
        confirmBtn.setAttribute('data-bs-dismiss', '');

        if (filename.length !== 0) {
            if (filenameInput.classList.contains('is-invalid')) {
                filenameInput.classList.remove('is-invalid');
            }
            $('#confirmation_modal').modal('hide');
            let screenshots = this.exportArtAssignments(filename);
            filenameInput.value = "";
            this.setAssignmentProperties(this.reverseScale);


            if (this.canvasState.assignedArtwork.length !== 0) {
                const response = await this.canvasApi.createSurfaceState({
                    filename: filename,
                    assignedArtwork: this.canvasState.assignedArtwork,
                    screenshots: screenshots,
                    canvasState: this.canvasState,
                    reverseScale: this.reverseScale,
                    userId: this.user_id,
                    spotId: this.spot_id
                });
            } else {
                console.log('you do not have any artwork assigned');
            }

        } else {
            filenameInput.classList.add('is-invalid');
            let invalidFeedback = document.querySelector('.invalid-feedback');
            // let roomName = document.getElementById('room_name').innerText.split(':')[0];
            invalidFeedback.innerText = filenameExists ?
                `File name: "${filename}" already exists for a version of ${roomName}.` : "No file name provided.";
        }
    }

    toggleSaveButton() {
        if (this.isInactive()) {
            return
        }

        if (!this.surfaceStateId) {
            this.saveBtn.hide();
            return;
        }

        if (this.unsavedChanges === true) {
            const onCanvasUpdatedEvent = new CustomEvent("onCanvasUpdated", {
                detail: {
                    surfaceStateId: this.surfaceStateId
                },
                bubbles: true,
                cancelable: true,
                composed: false,
            });
            document.dispatchEvent(onCanvasUpdatedEvent)

            this.saveBtn.show();
            // auto  save feature
            // this.updateSavedVersion();
        } else {
            this.saveBtn.hide();
        }
    }

    disableSaveButton() {
        this.saveBtn.hide();
    }

    openSaveFileModal(event) {
        event.preventDefault();

        let btn = event.target;
        let errorMsg = document.getElementById('error_alert');
        if (this.canvasHasOverlap()) {
            btn.setAttribute('data-bs-target', 'none');
            errorMsg.classList.add('show');
        } else {
            let modal = new bootstrap.Modal(document.getElementById("confirmation_modal"), {});
            modal.show();
            errorMsg.classList.replace('show', 'hide');
        }
    }

    canvasHasOverlap() {
        return this.canvasState.isOverlap;
    }

    positionCropBtn(object) {
        // disable crop
        return false;

        this.cropBtn.show();

        let headerHeight = $('#header').height();
        this.cropBtn.css('top', object.top + headerHeight);

        let sidebarWidth = $('.editor .side-col').width();
        let locLeft = object.left + (sidebarWidth - this.cropBtn.width() / 2 - 20);
        this.cropBtn.css('left', locLeft);
    }

    deserializeArtSelection(art) {
        let imageProperties = [];
        imageProperties['title'] = art['title'];
        imageProperties['imgUrl'] = art['imgUrl'];
        imageProperties['artworkId'] = art['artworkId'];
        imageProperties['scale'] = art['scale'];
        imageProperties['uniqueInstanceId'] = art['uniqueInstanceId'];

        return new ArtSelection(
            imageProperties,
            art['topPosition'],
            art['leftPosition'],
            art['cropData'],
            art['overrideScale']
        );
    }

    setAssignmentProperties(reverseScale) {
        let assignedArt = this.artworkCanvas.getObjects();

        let propertize = (art) => {
            for (let i = 1; i < assignedArt.length; i++) {
                // Check both unique instance ID and legacy artwork ID
                if (art.uniqueInstanceId === assignedArt[i].id || 
                    art.getArtworkId() === assignedArt[i].id) {
                    art.setTopPosition(parseInt(assignedArt[i].top * reverseScale));
                    art.setLeftPosition(parseInt(assignedArt[i].left * reverseScale));
                    return art;
                }
            }
        };

        this.canvasState.assignedArtwork = this.canvasState.assignedArtwork.map(propertize);
    }

    toggleRemoveButton() {
        if (this.isInactive()) {
            return
        }

        const selectedObjs = this.artworkCanvas.getActiveObjects();
        if (selectedObjs.length > 0) {
            this.removeBtn.show()
        } else {
            this.removeBtn.hide()
        }
    }

    activeStateUpdated() {
        this.toggleSaveButton();
        this.toggleRemoveButton();
    }

    isInactive() {
        return !this.isActive()
    }

    isActive() {
        return this.active;
    }

    initializeGuides() {
        // Add a guides array to track all guide lines
        this.guides = [];

        document.getElementById('add-horz-guide')?.addEventListener('click', () => this.createGuide(true));
        document.getElementById('add-vert-guide')?.addEventListener('click', () => this.createGuide(false));
        document.getElementById('toggle-guides')?.addEventListener('click', () => this.toggleGuides());

        // Listen for guide movement
        this.artworkCanvas.on('object:moving', (e) => {
            const obj = e.target;
            if (obj && obj.type === 'line' && obj.isGuide) {
                this.updateGuide(obj);
            }
        });
    }

    pixelsToFeetInches(pixels) {
        // Convert pixels to feet/inches based on your canvas's actual width
        const inchesPerPixel = this.canvasState.actualWidthInch / this.boundingBox.width;
        const totalInches = pixels * inchesPerPixel;

        const feet = Math.floor(totalInches / 12);
        const inches = Math.round(totalInches % 12);

        return feet > 0 ? `${feet}'${inches}"` : `${inches}"`;
    }

    createGuide(isHorizontal) {
        if (this.isInactive()) return;

        const boundingBoxWidth = this.surfaceData['bounding_box_width'] * this.baseScale;
        const boundingBoxHeight = this.surfaceData['bounding_box_height'] * this.baseScale;
        const boundingBoxTop = this.surfaceData['bounding_box_top'] * this.baseScale;
        const boundingBoxLeft = this.surfaceData['bounding_box_left'] * this.baseScale;

        let line, labelA, labelB;

        // Reset toggle button state to show all guides
        const button = document.getElementById('toggle-guides');
        button.setAttribute('data-hidden', 'false');
        button.innerHTML = '<i class="fal fa-eye"></i> Hide Guides';

        // Make all existing guides visible
        this.guides.forEach(guide => {
            guide.line.visible = true;
            guide.labelA.visible = true;
            guide.labelB.visible = true;
        });

        // Create the guide line with proper initial properties
        line = new fabric.Line([0, 0, isHorizontal ? boundingBoxWidth : 0, isHorizontal ? 0 : boundingBoxHeight], {
            stroke: isHorizontal ? '#FF4444' : '#4444FF',
            strokeWidth: 1,
            left: isHorizontal ? boundingBoxLeft : boundingBoxLeft + boundingBoxWidth / 2,
            top: isHorizontal ? boundingBoxTop + boundingBoxHeight / 2 : boundingBoxTop,
            hasControls: false,
            hasBorders: false,
            lockRotation: true,
            lockScalingX: true,
            lockScalingY: true,
            selectable: true,
            evented: true,
            lockMovementX: isHorizontal ? true : false,
            lockMovementY: isHorizontal ? false : true,
            hoverCursor: 'move',
            padding: 10,
            isGuide: true,
            visible: true
        });

        labelA = new fabric.Textbox('0', {
            fontSize: 12,
            fill: isHorizontal ? '#FF4444' : '#4444FF',
            backgroundColor: 'white',
            left: 10 + (isHorizontal ? boundingBoxLeft : boundingBoxLeft + boundingBoxWidth / 2),
            selectable: true,
            editable: true,
            width: 50,
            height: 20,
            visible: true,
            hasControls: false,
            hasBorders: false,
            lockMovementX: true,  // Lock all movement
            lockMovementY: true,  // Lock all movement
            isGuideLabel: true,
            hoverCursor: 'text',
            moveCursor: 'text',
            cursorColor: 'black'
        });

        labelB = new fabric.Textbox('0', {
            fontSize: 12,
            fill: isHorizontal ? '#FF4444' : '#4444FF',
            backgroundColor: 'white',
            left: 10 + (isHorizontal ? boundingBoxLeft : boundingBoxLeft + boundingBoxWidth / 2),
            selectable: true,
            editable: true,
            width: 50,
            height: 20,
            visible: true,
            hasControls: false,
            hasBorders: false,
            lockMovementX: true,  // Lock all movement
            lockMovementY: true,  // Lock all movement
            isGuideLabel: true,
            hoverCursor: 'text',
            moveCursor: 'text',
            cursorColor: 'black'
        });

        line.labelA = labelA;
        line.labelB = labelB;

        // Add the guide and its labels to our guides array
        this.guides.push({
            line: line,
            labelA: labelA,
            labelB: labelB
        });

        this.artworkCanvas.add(line);
        this.artworkCanvas.add(labelA);
        this.artworkCanvas.add(labelB);

        this.updateGuide(line);
        this.artworkCanvas.renderAll();
        // Try adding editing:exited event as backup
        labelA.on('editing:exited', (opt) => {
            console.log('Label A editing exited:', opt);  // Debug log
            this.handleLabelInput(labelA, line, 'A');
        });

        labelB.on('editing:exited', (opt) => {
            console.log('Label B editing exited:', opt);  // Debug log
            this.handleLabelInput(labelB, line, 'B');
        });

        // Add keydown handlers for Enter key
        labelA.on('keydown', (e) => {
            if (e.keyCode === 13) { // Enter key
                this.handleLabelInput(labelA, line, 'A');
                labelA.exitEditing();
                this.artworkCanvas.renderAll();
            }
        });

        labelB.on('keydown', (e) => {
            if (e.keyCode === 13) { // Enter key
                this.handleLabelInput(labelB, line, 'B');
                labelB.exitEditing();
                this.artworkCanvas.renderAll();
            }
        });
    }

    updateGuide(line) {
        const isHorizontal = line.y1 === line.y2;
        const boundingBoxWidth = this.surfaceData['bounding_box_width'] * this.baseScale;
        const boundingBoxHeight = this.surfaceData['bounding_box_height'] * this.baseScale;
        const boundingBoxTop = this.surfaceData['bounding_box_top'] * this.baseScale;
        const boundingBoxLeft = this.surfaceData['bounding_box_left'] * this.baseScale;

        if (isHorizontal) {
            let y = Math.min(Math.max(line.top, boundingBoxTop), boundingBoxTop + boundingBoxHeight);

            line.set({
                x1: 0,
                y1: 0,
                x2: boundingBoxWidth,
                y2: 0,
                left: boundingBoxLeft,
                top: y,
                selectable: true,
                evented: true,
                hasControls: false,
                hasBorders: false,
                lockMovementX: true,
                lockMovementY: false,
                hoverCursor: 'move'
            });

            const distTop = this.pixelsToFeetInches(y - boundingBoxTop);
            const distBottom = this.pixelsToFeetInches(boundingBoxTop + boundingBoxHeight - y);

            line.labelA.set({
                text: distTop,
                left: 10 + boundingBoxLeft,
                top: y - 20,
                backgroundColor: 'white',
                lockMovementX: true,
                lockMovementY: true,
                hoverCursor: 'text',
                moveCursor: 'text'
            });

            line.labelB.set({
                text: distBottom,
                left: 10 + boundingBoxLeft,
                top: y + 5,
                backgroundColor: 'white',
                lockMovementX: true,
                lockMovementY: true,
                hoverCursor: 'text',
                moveCursor: 'text'
            });
        } else {
            let x = Math.min(Math.max(line.left, boundingBoxLeft), boundingBoxLeft + boundingBoxWidth);

            line.set({
                x1: 0,
                y1: 0,
                x2: 0,
                y2: boundingBoxHeight,
                left: x,
                top: boundingBoxTop,
                selectable: true,
                evented: true,
                hasControls: false,
                hasBorders: false,
                lockMovementX: false,
                lockMovementY: true,
                hoverCursor: 'move'
            });

            const distLeft = this.pixelsToFeetInches(x - boundingBoxLeft);
            const distRight = this.pixelsToFeetInches(boundingBoxLeft + boundingBoxWidth - x);

            line.labelA.set({
                text: distLeft,
                left: x - 40,
                top: 10 + boundingBoxTop,
                backgroundColor: 'white',
                lockMovementX: true,
                lockMovementY: true,
                hoverCursor: 'text',
                moveCursor: 'text'
            });

            line.labelB.set({
                text: distRight,
                left: x + 5,
                top: 10 + boundingBoxTop,
                backgroundColor: 'white',
                lockMovementX: true,
                lockMovementY: true,
                hoverCursor: 'text',
                moveCursor: 'text'
            });
        }

        line.labelA.setCoords();
        line.labelB.setCoords();

        this.artworkCanvas.bringToFront(line);
        this.artworkCanvas.bringToFront(line.labelA);
        this.artworkCanvas.bringToFront(line.labelB);
    }

    handleLabelInput(textbox, guideLine, labelType) {
        if (this.isInactive()) return;

        const value = textbox.text || textbox.get('text');

        if (!value) {
            console.warn('No text value found in textbox');
            return;
        }

        const pixels = this.feetInchesToPixels(value);

        if (pixels === null) {
            console.warn('Invalid measurement format. Use format like "5\'6\"" or "5\'" or "6\""');
            this.updateGuide(guideLine);
            return;
        }

        const boundingBoxTop = this.surfaceData['bounding_box_top'] * this.baseScale;
        const boundingBoxLeft = this.surfaceData['bounding_box_left'] * this.baseScale;
        const boundingBoxHeight = this.surfaceData['bounding_box_height'] * this.baseScale;
        const boundingBoxWidth = this.surfaceData['bounding_box_width'] * this.baseScale;

        const isHorizontal = guideLine.y1 === guideLine.y2;

        if (isHorizontal) {
            let newY;
            if (labelType === 'A') {
                newY = boundingBoxTop + pixels;
            } else {
                newY = (boundingBoxTop + boundingBoxHeight) - pixels;
            }

            newY = Math.min(Math.max(newY, boundingBoxTop), boundingBoxTop + boundingBoxHeight);
            guideLine.set({
                top: newY,
                selectable: true,
                evented: true,
                hasControls: false,
                hasBorders: false,
                lockMovementX: true,
                lockMovementY: false,
                hoverCursor: 'move'
            });
        } else {
            let newX;
            if (labelType === 'A') {
                newX = boundingBoxLeft + pixels;
            } else {
                newX = (boundingBoxLeft + boundingBoxWidth) - pixels;
            }

            newX = Math.min(Math.max(newX, boundingBoxLeft), boundingBoxLeft + boundingBoxWidth);
            guideLine.set({
                left: newX,
                selectable: true,
                evented: true,
                hasControls: false,
                hasBorders: false,
                lockMovementX: false,
                lockMovementY: true,
                hoverCursor: 'move'
            });
        }

        this.updateGuide(guideLine);

        // Ensure the guide remains selectable and draggable
        guideLine.setCoords();
        this.artworkCanvas.setActiveObject(guideLine);
        this.artworkCanvas.renderAll();
    }

    feetInchesToPixels(value) {
        // Accept input in format: "5'6"" or "5'" or "6""
        const regex = /^(?:(\d+)')?(?:(\d+)")?$/;
        const match = value.trim().match(regex);

        if (!match) return null;

        const feet = parseInt(match[1] || 0);
        const inches = parseInt(match[2] || 0);

        const totalInches = (feet * 12) + inches;
        const inchesPerPixel = this.canvasState.actualWidthInch / this.boundingBox.width;

        return totalInches / inchesPerPixel;
    }

    toggleGuides() {
        const button = document.getElementById('toggle-guides');
        const isHidden = !(button.getAttribute('data-hidden') === 'true');
        this.guides.forEach(guide => {
            guide.line.visible = !isHidden;
            guide.labelA.visible = !isHidden;
            guide.labelB.visible = !isHidden;
        });

        button.setAttribute('data-hidden', (isHidden).toString());
        button.innerHTML = `<i class="fal fa-eye${isHidden ? '' : '-slash'}"></i> ${isHidden ? 'Show' : 'Hide'} Guides`;

        this.artworkCanvas.renderAll();
    }

    addWarpedArtwork(imgData, dropX = null, dropY = null) {
        const { imgUrl, artworkId } = imgData;
        
        // Generate unique instance ID for warped artwork
        const uniqueInstanceId = `${artworkId}_${Math.random().toString(36).substr(2, 9)}`;
        
        // Clean up existing matrices
        if (this.dstMat) this.dstMat.delete();
        if (this.M) this.M.delete();

        // Get the polygon area dimensions
        const bounds = this.calculatePolygonBounds(this.areaSrcPoints);
        const paddedWidth = bounds.width;
        const paddedHeight = bounds.height;
        
        // Create source points from the wall area corners
        let srcTri = cv.matFromArray(4, 1, cv.CV_32FC2, this.areaSrcPoints.flatMap(p => [p.x, p.y]));

        // Create destination points as a rectangle
        let dstTri = cv.matFromArray(4, 1, cv.CV_32FC2, [
            0, 0,
            paddedWidth, 0,
            paddedWidth, paddedHeight,
            0, paddedHeight
        ]);

        // Calculate transformation matrices
        this.M = cv.getPerspectiveTransform(dstTri, srcTri);

        // Store initial position for reference - use drop position if provided
        this.initialWarpPosition = {
            x: dropX || 0,
            y: dropY || 0
        };

        // Clean up temporary matrices
        srcTri.delete();
        dstTri.delete();

        // Load the image and apply transformation
        fabric.Image.fromURL(imgUrl, (img) => {
            this.updateTransformedArtwork(img, this.M, bounds, artworkId, uniqueInstanceId);
        }, { crossOrigin: 'anonymous' });
    }

    updateTransformedArtwork(fabricImage, transformMatrix, bounds, artworkId, uniqueInstanceId) {
        try {
            if (!fabricImage || !transformMatrix) {
                console.error('Missing required parameters');
                return;
            }

            // Calculate scale to fit artwork within the wall area
            const scale = Math.min(
                bounds.width / fabricImage.width,
                bounds.height / fabricImage.height
            ) * 0.5;

            // Create temporary canvas for the original image
            const tempCanvas = document.createElement('canvas');
            const scaledWidth = fabricImage.width * scale;
            const scaledHeight = fabricImage.height * scale;
            tempCanvas.width = scaledWidth;
            tempCanvas.height = scaledHeight;
            
            // Draw the original image onto temp canvas
            const tempCtx = tempCanvas.getContext('2d');
            tempCtx.drawImage(fabricImage.getElement(), 0, 0, scaledWidth, scaledHeight);

            // Convert to OpenCV matrix
            let srcMat = cv.imread(tempCanvas);
            let dstMat = new cv.Mat();
            
            // Apply perspective transform
            cv.warpPerspective(
                srcMat,
                dstMat,
                transformMatrix,
                new cv.Size(this.artworkCanvas.width, this.artworkCanvas.height),
                cv.INTER_LINEAR,
                cv.BORDER_TRANSPARENT,
                new cv.Scalar()
            );

            // Convert result back to canvas
            cv.imshow(tempCanvas, dstMat);

            // Create new Fabric image from warped result
            fabric.Image.fromURL(tempCanvas.toDataURL(), (warpedImage) => {
                warpedImage.set({
                    id: uniqueInstanceId, // Use unique instance ID
                    originalArtworkId: artworkId, // Store original artwork ID
                    left: this.initialWarpPosition.x,
                    top: this.initialWarpPosition.y,
                    selectable: true,
                    hasControls: true,
                    hasBorders: true,
                    lockRotation: false,
                    lockScalingX: false,
                    lockScalingY: false,
                    lockMovementX: false,
                    lockMovementY: false,
                    cornerStyle: 'circle',
                    transparentCorners: false,
                    cornerColor: 'rgba(102,153,255,0.5)',
                    cornerSize: 8,
                    padding: 5,
                    originalMatrix: transformMatrix.clone(), // Store original transformation
                    originalPosition: { ...this.initialWarpPosition }
                });
                
                // Add movement constraints
                warpedImage.on('moving', (evt) => {
                    const obj = evt.target;
                    // Keep the artwork within the polygon area
                    const polygon = this.areaPolygon;
                    if (polygon) {
                        const points = polygon.points;
                        const minX = Math.min(...points.map(p => p.x));
                        const maxX = Math.max(...points.map(p => p.x));
                        const minY = Math.min(...points.map(p => p.y));
                        const maxY = Math.max(...points.map(p => p.y));

                        obj.left = Math.min(Math.max(obj.left, minX), maxX - obj.width);
                        obj.top = Math.min(Math.max(obj.top, minY), maxY - obj.height);
                    }
                });

                // Add to main canvas
                this.artworkCanvas.add(warpedImage);
                this.artworkCanvas.setActiveObject(warpedImage);
                this.artworkCanvas.renderAll();
                
                // Update artwork count for warped artwork
                this.incrementArtworkCount(artworkId);

                // Clean up
                tempCanvas.remove();
            });

            // Clean up OpenCV resources
            srcMat.delete();
            dstMat.delete();

        } catch (error) {
            console.error('Error in updateTransformedArtwork:', error);
        }
    }

    showDropZone() {
        // Create or show drop zone indicator
        if (!this.dropZoneIndicator) {
            this.dropZoneIndicator = new fabric.Rect({
                left: 0,
                top: 0,
                width: this.artworkCanvas.width,
                height: this.artworkCanvas.height,
                fill: 'rgba(0, 123, 255, 0.1)',
                stroke: '#007bff',
                strokeWidth: 2,
                strokeDashArray: [10, 5],
                selectable: false,
                evented: false,
                hasControls: false,
                hasBorders: false,
                lockMovementX: true,
                lockMovementY: true,
                lockRotation: true,
                lockScalingX: true,
                lockScalingY: true
            });
            this.artworkCanvas.add(this.dropZoneIndicator);
        }
        
        this.dropZoneIndicator.visible = true;
        this.artworkCanvas.renderAll();
    }

    hideDropZone() {
        if (this.dropZoneIndicator) {
            this.dropZoneIndicator.visible = false;
            this.artworkCanvas.renderAll();
        }
    }

    showDropSuccess(x, y) {
        try {
            // Create a temporary success indicator
            const successIndicator = new fabric.Circle({
                left: x,
                top: y,
                radius: 10,
                fill: 'rgba(40, 167, 69, 0.8)',
                stroke: '#28a745',
                strokeWidth: 2,
                selectable: false,
                evented: false,
                hasControls: false,
                hasBorders: false,
                lockMovementX: true,
                lockMovementY: true,
                lockRotation: true,
                lockScalingX: true,
                lockScalingY: true
            });

            this.artworkCanvas.add(successIndicator);
            this.artworkCanvas.renderAll();

            // Remove the indicator after a short delay
            setTimeout(() => {
                try {
                    this.artworkCanvas.remove(successIndicator);
                    this.artworkCanvas.renderAll();
                } catch (error) {
                    console.error('Error removing success indicator:', error);
                }
            }, 1000);
        } catch (error) {
            console.error('Error showing drop success:', error);
        }
    }

    // Helper function to calculate polygon bounds
    calculatePolygonBounds(points) {
        const xs = points.map(p => p.x);
        const ys = points.map(p => p.y);
        const minX = Math.min(...xs);
        const maxX = Math.max(...xs);
        const minY = Math.min(...ys);
        const maxY = Math.max(...ys);
        return {
            width: maxX - minX,
            height: maxY - minY,
            x: minX,
            y: minY
        };
    }

    toggleArea() {
        if (!this.areaPolygon) return;
        
        const button = document.getElementById('toggle-area');
        const isHidden = !(button.getAttribute('data-hidden') === 'true');
        
        this.areaPolygon.visible = !isHidden;
        
        button.setAttribute('data-hidden', isHidden.toString());
        button.innerHTML = `<i class="fal fa-eye${isHidden ? '' : '-slash'}"></i> ${isHidden ? 'Show' : 'Hide'} Area`;
        
        this.artworkCanvas.renderAll();
    }

    // Initialize artwork counts from existing assigned artworks
    initializeArtworkCounts() {
        this.artworkCounts.clear();
        this.canvasState.assignedArtwork.forEach(art => {
            const artworkId = art.getArtworkId();
            const currentCount = this.artworkCounts.get(artworkId) || 0;
            this.artworkCounts.set(artworkId, currentCount + 1);
        });
        this.updateArtworkBadges();
    }

    // Update artwork count when artwork is added
    incrementArtworkCount(artworkId) {
        const currentCount = this.artworkCounts.get(artworkId) || 0;
        this.artworkCounts.set(artworkId, currentCount + 1);
        this.updateArtworkBadges();
    }

    // Update artwork count when artwork is removed
    decrementArtworkCount(artworkId) {
        const currentCount = this.artworkCounts.get(artworkId) || 0;
        if (currentCount > 0) {
            this.artworkCounts.set(artworkId, currentCount - 1);
            this.updateArtworkBadges();
        }
    }

    // Update the badge display for all artwork items
    updateArtworkBadges() {
        // Find all artwork elements in the sidebar
        const artworkElements = document.querySelectorAll('.artwork-img');
        
        artworkElements.forEach(element => {
            const artworkId = element.dataset.artworkId;
            const count = this.artworkCounts.get(artworkId) || 0;
            
            // Remove existing badge if it exists
            const existingBadge = element.querySelector('.artwork-count-badge');
            if (existingBadge) {
                existingBadge.remove();
            }
            
            // Add badge if count > 0
            if (count > 0) {
                const badge = document.createElement('div');
                badge.className = 'artwork-count-badge';
                badge.textContent = count;
                element.style.position = 'relative';
                element.appendChild(badge);
            }
        });
    }

    // Method to refresh badges when artwork list is updated (e.g., after search/filter)
    refreshArtworkBadges() {
        // Use a small delay to ensure DOM is updated
        setTimeout(() => {
            this.updateArtworkBadges();
        }, 100);
    }

    // Public method to get artwork count for a specific artwork
    getArtworkCount(artworkId) {
        return this.artworkCounts.get(artworkId) || 0;
    }
}

export default CanvasManager;
