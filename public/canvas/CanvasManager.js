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
        this.artworkCanvas = new fabric.Canvas(this.canvasId, {
            enableRetinaScaling: true,
            skipOffscreen: false,
        });

        this.boundingBox = null;
        this.imgWidth = this.surface.data.img_width;
        this.imgHeight = this.surface.data.img_height;
        this.baseWidth = null;
        this.baseScale = null;

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

        this.initialize()
    }

    initialize() {
        const mainContent = document.querySelector('.main_content');    // canvas containing div

        let mainHeight = mainContent.offsetHeight,
            mainWidth = mainContent.offsetWidth;

        this.artworkCanvas.setWidth(mainWidth);
        this.artworkCanvas.setHeight(mainHeight);


        // Here we need to write a program to calculate whether to scale height or width. Currently only scale width
        if ((this.imgWidth / this.imgHeight) >= (mainWidth / mainHeight)) {
            //The image is wider than the screen ratio
            //shrink by width
            this.baseScale = mainWidth / this.imgWidth; //Here is the relative data when the screen is zoomed
            this.reverseScale = 1 / this.baseScale;
        } else {
            //The picture is narrower than the screen ratio
            //shrink by height
            this.baseScale = mainHeight / this.imgHeight; //Here is the relative data when the screen is zoomed
            this.reverseScale = 1 / this.baseScale;
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

        this.registerArtworkSelectionEvent();
        this.registerCanvasUpdateEvent();

        // Add guide-related initialization
        this.initializeGuides();
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

    registerArtworkSelectionEvent() {
        $('#site__body').on('click', '.artwork-img', (el) => {
            if (!this.active) {
                return false
            }

            let target = el.currentTarget;
            let newSelection = this.newArtworkSelection(target);
            this.placeSelectedImage(newSelection);
            this.unsavedChanges = true;
            this.toggleRemoveButton();
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

        fabric.Image.fromURL(imgUrl, function (myImg) {
            if (myImg._element == null) {
                alert("Image could not be loaded...");
            }

            let img1 = myImg.set({
                originX: "left",
                originY: "top",
                id: artSelection.artworkId,
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

        }.bind(this), {
            crossOrigin: 'anonymous'
        });

        this.canvasState.assignedArtwork.push(artSelection);
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
        let canvasObjects = this.artworkCanvas.getObjects();
        for (let i = 1; i < canvasObjects.length; i++) {
            let presentId = canvasObjects[i].id;
            if (artId === presentId) {
                this.artworkCanvas.setActiveObject(canvasObjects[i]);
                this.artworkCanvas.renderAll();
                return true;
            }
        }
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
            this.canvasState.assignedArtwork = this.canvasState.assignedArtwork.filter(art => art.getArtworkId() !== obj.id);
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
            obj1 => !latestDeserialized.some(obj2 => obj2.getArtworkId() === obj1.getArtworkId())
        );

        this.canvasState.modifiedVersion.addedArtwork = latestDeserialized.filter(
            obj1 => !this.canvasState.assignedArtwork.some(obj2 => obj2.getArtworkId() === obj1.getArtworkId())
        );

        this.canvasState.assignedArtwork.forEach(obj1 => {
            const obj2 = latestDeserialized.find(item => item.getArtworkId() === obj1.getArtworkId());

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
                if (art.getArtworkId() === assignedArt[i].id) {
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

        if (isHorizontal) {
            line = new fabric.Line([0, 0, boundingBoxWidth, 0], {
                stroke: '#FF4444',
                strokeWidth: 1,
                left: boundingBoxLeft,
                top: boundingBoxTop + boundingBoxHeight / 2,
                hasControls: false,
                hasBorders: false,
                lockRotation: true,
                lockScalingX: true,
                lockScalingY: true,
                selectable: true,
                hoverCursor: 'move',
                padding: 10,
                isGuide: true,
                visible: true  // Always visible when created
            });

            labelA = new fabric.Text('0', {
                fontSize: 12,
                fill: '#FF4444',
                backgroundColor: 'white',
                left: 10 + boundingBoxLeft,
                selectable: false,
                evented: false,
                visible: true  // Always visible when created
            });

            labelB = new fabric.Text('0', {
                fontSize: 12,
                fill: '#FF4444',
                backgroundColor: 'white',
                left: 10 + boundingBoxLeft,
                selectable: false,
                evented: false,
                visible: true  // Always visible when created
            });
        } else {
            line = new fabric.Line([0, 0, 0, boundingBoxHeight], {
                stroke: '#4444FF',
                strokeWidth: 1,
                left: boundingBoxLeft + boundingBoxWidth / 2,
                top: boundingBoxTop,
                hasControls: false,
                hasBorders: false,
                lockRotation: true,
                lockScalingX: true,
                lockScalingY: true,
                selectable: true,
                hoverCursor: 'move',
                padding: 10,
                isGuide: true,
                visible: true  // Always visible when created
            });

            labelA = new fabric.Text('0', {
                fontSize: 12,
                fill: '#4444FF',
                backgroundColor: 'white',
                selectable: false,
                evented: false,
                visible: true  // Always visible when created
            });

            labelB = new fabric.Text('0', {
                fontSize: 12,
                fill: '#4444FF',
                backgroundColor: 'white',
                selectable: false,
                evented: false,
                visible: true  // Always visible when created
            });
        }

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
    }

    updateGuide(line) {
        const isHorizontal = line.y1 === line.y2;
        const boundingBoxWidth = this.surfaceData['bounding_box_width'] * this.baseScale;
        const boundingBoxHeight = this.surfaceData['bounding_box_height'] * this.baseScale;
        const boundingBoxTop = this.surfaceData['bounding_box_top'] * this.baseScale;
        const boundingBoxLeft = this.surfaceData['bounding_box_left'] * this.baseScale;

        if (isHorizontal) {
            // Constrain vertical position within bounding box
            let y = Math.min(Math.max(line.top, boundingBoxTop), boundingBoxTop + boundingBoxHeight);
            
            line.set({
                x1: 0,
                y1: 0,
                x2: boundingBoxWidth,
                y2: 0,
                left: boundingBoxLeft,
                top: y
            });

            const distTop = this.pixelsToFeetInches(y - boundingBoxTop);
            const distBottom = this.pixelsToFeetInches(boundingBoxTop + boundingBoxHeight - y);

            line.labelA.set({
                text: distTop,
                left: 10 + boundingBoxLeft,
                top: y - 20
            });

            line.labelB.set({
                text: distBottom,
                left: 10 + boundingBoxLeft,
                top: y + 5
            });
        } else {
            // Constrain horizontal position within bounding box
            let x = Math.min(Math.max(line.left, boundingBoxLeft), boundingBoxLeft + boundingBoxWidth);
            
            line.set({
                x1: 0,
                y1: 0,
                x2: 0,
                y2: boundingBoxHeight,
                left: x,
                top: boundingBoxTop
            });

            const distLeft = this.pixelsToFeetInches(x - boundingBoxLeft);
            const distRight = this.pixelsToFeetInches(boundingBoxLeft + boundingBoxWidth - x);

            line.labelA.set({
                text: distLeft,
                left: x - 40,
                top: 10 + boundingBoxTop
            });

            line.labelB.set({
                text: distRight,
                left: x + 5,
                top: 10 + boundingBoxTop
            });
        }

        line.labelA.setCoords();
        line.labelB.setCoords();

        this.artworkCanvas.bringToFront(line);
        this.artworkCanvas.bringToFront(line.labelA);
        this.artworkCanvas.bringToFront(line.labelB);
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
}

export default CanvasManager;
