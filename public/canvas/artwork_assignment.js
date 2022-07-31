import ArtSelection from './ArtSelection.js';
import {
    createSurfaceState,
    updateSurfaceState,
} from './host_client_interface.js';

// The assignedArtwork here is a collection of all placed artworks.
// If there is any change to any artwork, change the value here.
// Finally, send it out and let php process it
const canvasState = {
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
        modifiedArtwork: []
    },
    recentSelection: null
};

//initiate fabric js canvas
const artworkCanvas = new fabric.Canvas('artwork_canvas', {
    enableRetinaScaling: true,
    skipOffscreen: false,
});


let boundingBox;
let imgWidth = surface.data.img_width;
let imgHeight = surface.data.img_height;
let baseWidth;
let baseScale;
let defaultScales = [];

let $save_btn = $('#save_btn');
let $remove_btn = $('#remove_btn');
let $crop_btn = $('#crop_btn');

let reverseScale; //這個reverseScale，在整個artassignment裡面只會有一個數值。取決於canvase縮小放大的倍數


$(function () {

    // some commonly manipulated page elements
    const mainContent = document.querySelector('.main_content');    // canvas containing div

    // Create preset values
    // main content's dimensions for the canvas to fill.
    let mainHeight = mainContent.offsetHeight,
        mainWidth = mainContent.offsetWidth;
    // disable manual XY scaling by user.
    // fabric.Group.prototype.lockScalingX = true;
    // fabric.Group.prototype.lockScalingY = true;
    // Fabric.js canvas, main work area for user artwork assignments.
    artworkCanvas.setWidth(mainWidth);
    artworkCanvas.setHeight(mainHeight);


    // Here we need to write a program to calculate whether to scale height or width. Currently only scale width
    if ((imgWidth / imgHeight) >= (mainWidth / mainHeight)) {
        //The image is wider than the screen ratio
        //shrink by width
        baseScale = mainWidth / imgWidth; //Here is the relative data when the screen is zoomed
        reverseScale = 1 / baseScale;
    } else {
        //The picture is narrower than the screen ratio
        //shrink by height
        baseScale = mainHeight / imgHeight; //Here is the relative data when the screen is zoomed
        reverseScale = 1 / baseScale;
    }

    // Here's the old way
    // baseWidth=1500; //這是我們一開始換算時。nara用來計算所有東西的長度。
    // baseScale=mainWidth/baseWidth; //Here is the relative data when the screen is zoomed
    // reverseScale = 1 / baseScale;


///************ The numbers of length, width and height here are fake and will be changed later ****************
    boundingBox = new fabric.Rect({
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
    artworkCanvas.add(boundingBox); //This is the fabric object

    // Most of the events linked to the buttons are on the following code
    // listener for deleting assigned artwork on 'Delete' key press
    $(document).on('keydown', handleDeleteKeyOnArtwork);
    // listener for confirming saved art assignments on 'Enter' key press
    $('#file_name').on('keydown', handleEnterKeyOnSaveAs);
    // listeners for button clicks in the 'edit tools' section.
    $remove_btn.on('click', removeSelectedArtwork);
    $('#cancel_btn').on('click', removeAllArtwork);
    $save_btn.on('click', updateSavedVersion);
    $('#save_as_btn').on('click', saveNewVersion);
    $('#confirm_save_btn').on('click', confirmSave);
    $crop_btn.hide();
    disableSaveButton();


    $('#confirmation_modal').on('show.bs.modal', function (event) {
        $crop_btn.hide();
    }).on('hidden.bs.modal', function (event) {
        $crop_btn.show();
    })


    addCanvasEvents();

    // makes request to backend to retrieve all necessary data for a new canvas.
    requestCanvasBackgroundProperties(surface, baseScale);

    // Call out all the artwork. and display on the page
    if (assignedArtworks) {

        assignedArtworks.forEach((art) => {
            art = remapArtVariable(art);
            art = deserializeArtSelection(art);
            placeSelectedImage(art, (art.getTopPosition() * baseScale), (art.getLeftPosition() * baseScale));
        });
        canvasState['savedVersion'] = true;
        canvasState['currentVersionData'] = latestState;
        //setTitle(latestState.version_name);
        disableSaveButton();
        addSavedVersionEvents();
    }

});


function remapArtVariable(art) {
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

//todo::remove_this:not using anymore
/**
 * Loads the title of a saved artwork image if it's a previously saved version.
 * @param versionName: user designated name for a particular saved version.
 */
function setTitle(versionName) {
    let title = document.getElementById('assignment_title');
    title.textContent = canvasState['savedVersion'] ? versionName : "untitled";
}

/** Makes a GET request to the server to get the background image URL and bounding box properties of a canvas
 *  for art assignments.
 */

function requestCanvasBackgroundProperties(surface, baseScale) {
    let data = surface['data'];

    renderCanvasBackground(surface['background_url'], baseScale);
    canvasState.background = surface['background_url'];
    canvasState['actualWidthInch'] = data['actual_width_inch'];
    if (surface['overlay_url'] !== null) {
        setCanvasOverlay(surface['overlay_url']);
    }
    setBoundingBoxProperties(
        data['bounding_box_top'] * baseScale, data['bounding_box_left'] * baseScale,
        data['bounding_box_height'] * baseScale, data['bounding_box_width'] * baseScale
    );
}

/** Sets geometrical properties of the bounding box pertaining to a wall, based on parameters sent in the URL.
 *  @param top: number of pixels top of bounding box is offset from the top of browser viewport.
 *  @param left: number of pixels left of the bounding box is offset from the left of browser viewport.
 *  @param height: the height of the bounding box in pixels.
 *  @param width: the width of bounding box in pixels.
 */
function setBoundingBoxProperties(top, left, height, width) {
    boundingBox.top = top;
    boundingBox.left = left;
    boundingBox.height = height;
    boundingBox.width = width;
}

/** renders an image of a wall, identified by parameters sent in URL, as a canvas background.
 *  @param imgUrl: url of the background image pertaining to a wall, being placed on canvas.
 */
function renderCanvasBackground(imgUrl, baseScale) {
    artworkCanvas.setBackgroundImage(imgUrl, artworkCanvas.renderAll.bind(artworkCanvas), {
        originX: 'left',
        originY: 'top',
        scaleX: baseScale,
        scaleY: baseScale,
        centeredScaling: true,
    });
}

/** sets overlay image over the canvas background for depth of field effect on placed artwork.
 *  @param imgUrl: url of the overlay image being placed on canvas.
 */
function setCanvasOverlay(imgUrl) {
    let overlay = new Image();
    overlay.src = imgUrl;

    overlay.onload = function () {
        artworkCanvas.setOverlayImage(imgUrl, artworkCanvas.renderAll.bind(artworkCanvas), {
            originX: 'left',
            originY: 'top',
            scaleX: 1.0,
            scaleY: 1.0,
        });
    };
}

/** Extracts all relevant data from the selected artwork's HTML element.
 * @param selectedElement: the element which triggered user click event.
 * @returns object: containing values of the artwork's title: str, image URL: str, artwork ID: str, whether bookmarked: bool.
 */
function getSelectionData(selectedElement) {
    let title = selectedElement.dataset.title;
    let imgUrl = selectedElement.dataset.imgUrl;
    let artworkId = selectedElement.dataset.artworkId;
    let scale = selectedElement.dataset.scale;
    return {title, imgUrl, artworkId, scale};
}

/** Creates a new JSON serializable object from a user selected artwork HTML element.
 * @params targetElement: the element representing a user selected artwork.
 * @returns ArtSelection: a new ArtSelection object representing targetElement.
 */
function newArtworkSelection(targetElement) {
    let data = getSelectionData(targetElement);
    /*return new ArtSelection(data.title, data.imgUrl, data.artId);*/
    return new ArtSelection(data);
}

/** adds event listener for when images are moved in the canvas' bounding box area. **/
function addCanvasEvents() {
    // images being moved are tried against the bounding box to prevent inappropriate placements within canvas.

    artworkCanvas.on('object:selected', function (options) {
        if (options.target) {
            $remove_btn.show();
        }
    });

    artworkCanvas.on('object:deselected', function (options) {
        console.log('i am hidden')
        if (options.target) {
            $remove_btn.hide();
            console.log('i am hidden')
        }
    });

    artworkCanvas.on('object:moving', function (options) {
        if (options.target) {
            let object = options.target;
            // const xyScale = canvas State.defaultScale;c
            $crop_btn.css('top', object.top);
            let locLeft = object.left + ($('.left_menu').width() - $crop_btn.width() / 2 - 20);
            $crop_btn.css('left', locLeft);
            // target object's right and bottom edge boundaries (originally not provided)
            let xyScale = defaultScales[object.id];
            const objectBottom = Math.abs(object.top + (object.height * xyScale)),
                objectRight = Math.abs(object.left + (object.width * xyScale));
            // bounding box's edge boundaries
            const topBound = boundingBox.top,
                leftBound = boundingBox.left,
                bottomBound = topBound + boundingBox.height,
                rightBound = leftBound + boundingBox.width;
            // the object's placement is re-assigned with an offset of 2, accounting for border width of the bounding box.
            if (object.top < topBound) {
                object.set({top: topBound + 2});
            }
            if (object.left < leftBound) {
                object.set({left: leftBound + 2});
            }
            if (objectBottom > bottomBound) {
                let y = bottomBound - Math.abs(object.height * xyScale);
                object.set({top: y});
            }
            if (objectRight > rightBound) {
                let x = rightBound - Math.abs(object.width * xyScale);
                object.set({left: x});
            }
        }
    });
}

/** Initializes events for the purpose of detecting changes to the canvas after a save has been made. */
function addSavedVersionEvents() {
    let handleModification = (event, msg) => {
        if (canvasState.savedVersion && canvasState.currentVersionData !== null) {
            enableSaveButton();
        }
    };
    artworkCanvas.on({
        'object:modified': handleModification,
        'object:removed': handleModification,
    });
}


/** Resizes placed artwork image by scale for displaying the art's size relative to actual KRPano hotspot dimensions.
 *  @param image: the target image being resized for canvas placement.
 */
function applyAdaptiveRescale(image, scale, overrideScale) {
    let a = image.width;
    let b = boundingBox.width;
    let c = canvasState.actualWidthInch * scale;

    let adaptedScale = a * (b / c);

    // image.scaleToHeight(adaptedScale, false);
    image.scaleToWidth(adaptedScale, false);
    canvasState.defaultScale = image.scaleX;   // XY scale changes when calling scaleToWidth() on image.
    defaultScales[image.id] = image.scaleX

    if (overrideScale != null) {
        c = canvasState.actualWidthInch * overrideScale;
    }
    adaptedScale = a * (b / c);
    image.scaleToWidth(adaptedScale, false);
}


/** Marks an artwork card as selected on user click, and places its image in the canvas.
 * @param artSelection: an ArtSelection object initialized from a user selected artwork to place on canvas.
 * @param topPos: the image's starting position from the top in pixels, to place relative to canvas.
 * @param leftPos: the image's starting position from the left in pixels, to place relative to canvas.
 */
function placeSelectedImage(artSelection, topPos = boundingBox.top, leftPos = boundingBox.left) {

    let imgUrl = artSelection.imgUrl;
    let notSelected = !isAlreadySelected(artSelection.artworkId);
    if (notSelected) {
        // $('#loadingModal').modal('show');
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
                // lockScalingX: true,
                // lockScalingY: true,
                borderColor: 'red',
                borderScaleFactor: 1,
                hasRotatingPoint: true,
                noScaleCache: false
            });
            /*let scale = scaleArr[artSelection.artworkId];*/
            let scale = artSelection.scale;

            let overrideScale = artSelection.overrideScale;

            if (scale == null) {
                scale = 96;
            }
            applyAdaptiveRescale(img1, scale, overrideScale);
            artworkCanvas.add(img1);

            artworkCanvas.renderAll();
            // displayLoadingImageModal();
        }.bind(this), {
            crossOrigin: 'anonymous'
        });

        //在這地方canvasState拿到它需要的assignedArwork資料
        //一放置一個新的artwork，就將之推送到assignedartwork物件裡存起來
        canvasState.assignedArtwork.push(artSelection);
    }
}


/** removes a selected image from the canvas when delete key is pressed.
 * @param key: the pressed key which triggered the event.
 */
function handleDeleteKeyOnArtwork(key) {
    if (key.code === 'Delete') {
        removeSelectedArtwork();
    }
}

/**
 * Reassigns default 'Enter' key press action within text field, to confirm changes rather than refresh page.
 * @param key: key event fired when a key is pressed.
 * @returns {boolean}
 */
function handleEnterKeyOnSaveAs(key) {
    if (key.code === 'Enter' || key.code === 'NumpadEnter') {
        key.preventDefault();
        document.getElementById('confirm_save_btn').click();
        return false;
    }
}

/**
 * Detects whether or not a selected image is already present within the canvas.
 * @param artId: artwork_id of the image selected by user, to identify the image on canvas.
 * @returns boolean: true if the selected artwork is already present within the canvas, false otherwise.
 */
function isAlreadySelected(artId) {
    let canvasObjects = artworkCanvas.getObjects();
    for (let i = 1; i < canvasObjects.length; i++) {
        let presentId = canvasObjects[i].id;
        if (artId === presentId) {
            artworkCanvas.setActiveObject(canvasObjects[i]);
            artworkCanvas.renderAll();
            return true;
        }
    }
    return false;
}


/** Removes all selected artwork in the canvas from view. **/
function removeSelectedArtwork() {
    const selectedObjs = artworkCanvas.getActiveObjects();
    selectedObjs.forEach((obj) => {
        artworkCanvas.remove(obj);
        canvasState.assignedArtwork = canvasState.assignedArtwork.filter(art => art.getArtworkId() !== obj.id);
    });
}


/** Removes all artwork present on canvas from view. **/
function removeAllArtwork() {
    artworkCanvas.getObjects()
        .filter((obj) => {
            return obj.type === 'image'
        })
        .map((obj) => {
            artworkCanvas.remove(obj)
        });
    canvasState.assignedArtwork = [];
}


/** Invoked by 'SAVE AS' button when clicked to save new assignments to the user edited canvas.
 * @param event: the event that triggered this function. Used for identifying clicked buttons.
 */
function saveNewVersion(event) {
    openSaveFileModal(event);
}


/**
 * Applies changes made since last save to an artwork assignment version.
 * @param event: The event fired from a 'SAVE' button press.
 */


function updateSavedVersion(event) {
    if (canvasHasOverlap()) {
        window.alert('There is an overlap');
        return;
    }
    let clearTransientData = () => {
        canvasState.modifiedVersion.addedArtwork = [];
        canvasState.modifiedVersion.removedArtwork = [];
        canvasState.modifiedVersion.modifiedArtwork = [];
    };
    if (!canvasState.isOverlap) {
        setAssignmentProperties(reverseScale);
        let updates = trackChanges();

        disableSaveButton();

        let screenshots = exportArtAssignments(canvasState['currentVersionData'].version_name);
        let theVersionId = versionId; //get from global
        // const response = await updateSurfaceState(versionId, updates, screenshots, canvasState);
        updateSurfaceState(theVersionId, updates, screenshots, canvasState, reverseScale, user_id);
    }
}


/**
 * @param art
 * @returns {ArtSelection}
 */
function deserializeArtSelection(art) {
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


/**
 * Keeps track of all assigned images either removed from, added to, or modified on the canvas, after a version save.
 * @returns {{removed: *, added: *, modified: *, assignedArtwork: *}} object of lists pertaining to modifications made.
 */
function trackChanges() {

    // run through all saved artworks within currentVersionData and turn into ArtAssignment objects.
    const latestDeserialized = canvasState.assignedArtwork.map(deserializeArtSelection);
    // compare with newly updated list of assignedArtworks to filter those that were removed.
    canvasState.modifiedVersion.removedArtwork = latestDeserialized.filter(
        obj1 => canvasState.assignedArtwork.filter(obj2 => obj2.getArtworkId() === obj1.getArtworkId()).length === 0);
    // compare with newly updated list of assignedArtworks to filter those that were added.
    canvasState.modifiedVersion.addedArtwork = canvasState.assignedArtwork.filter(
        obj1 => latestDeserialized.filter(obj2 => obj2.getArtworkId() === obj1.getArtworkId()).length === 0);
    // compare with newly updated list of assignedArtworks to filter those that were modified (moved).
    latestDeserialized.forEach(obj1 => {
        canvasState.assignedArtwork.forEach(obj2 => {
            if (obj1.getArtworkId() === obj2.getArtworkId()) {
                if (obj1.getTopPosition() !== obj2.getTopPosition() ||
                    obj1.getLeftPosition() !== obj2.getLeftPosition() ||
                    obj1.getCropData() !== obj2.getCropData()) {
                    canvasState.modifiedVersion.modifiedArtwork.push(obj2);
                }
            }
        });
    });

    //move button––


    return {
        "assignedArtwork": canvasState.assignedArtwork,
        "removed": canvasState.modifiedVersion.removedArtwork,
        "added": canvasState.modifiedVersion.addedArtwork,
        "modified": canvasState.modifiedVersion.modifiedArtwork
    };
}


//This is where the archive is
/** Invoked by 'CONFIRM SAVE' button within the 'Save Canvas' modal dialog box. Verifies criteria before saving. **/
async function confirmSave() {
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
        let screenshots = exportArtAssignments(filename);
        filenameInput.value = "";
        setAssignmentProperties(reverseScale);


        if (canvasState.assignedArtwork.length != 0) {
            const response = await createSurfaceState(
                filename, canvasState.assignedArtwork, screenshots, canvasState, reverseScale, user_id
            );
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

/**
 * creates route for KRPano 360 preview page to redirect on successful art assignment save.
 */
// This should be moved to php after this
function makeVtourRoute() {
    window.location.href = "/vtour/post/" + spot_id + "?vlookat=" + vlookat + "&hlookat=" + hlookat;
}

/** Enables 'SAVE' button after a new version has been saved, to allow any changes to the saved version to be updated. **/
function enableSaveButton() {
    $save_btn.show();
}


function disableSaveButton() {
    $save_btn.hide();
}

/** Captures and saves two screenshots: one of the entire canvas, and another of the bounding area where art is displayed.
 *  @param filename: user designated name of the file to save.
 *  @returns object with keys of 'thumbnail' and 'hotspot' mapped to base64 encoded image values
 *           of both screenshots respectively.
 */
// crate screenshot
function exportArtAssignments(filename) {

    function captureCanvas() {
        return artworkCanvas.toDataURL({
            format: 'png',
        });
    }

    function captureHotspot() {
        boundingBox.opacity = 0;                    // hide bounding box
        artworkCanvas.backgroundImage.opacity = 0;  // hide background image
        let href = artworkCanvas.toDataURL({
            format: 'png',
            left: boundingBox.left,
            top: boundingBox.top,
            width: boundingBox.width + 2,
            height: boundingBox.height + 2,
        });
        boundingBox.opacity = 0.2;
        artworkCanvas.backgroundImage.opacity = 100;
        return href;
    }

    return {'thumbnail': captureCanvas(), 'hotspot': captureHotspot()};
}


/** Sets the location data of each assigned artwork, relative to the canvas.
 *  This keeps track of all artwork placements on canvas, for future reference.
 */
function setAssignmentProperties(reverseScale) {
    let assignedArt = artworkCanvas.getObjects();
    /**
     *  mapping function to apply left and top positions to each ArtSelection object as they are on canvas.
     * @param art: an ArtSelection object to apply top & left positions to.
     * @returns a modified ArtSelection object with updated top and left values.
     */
    let propertize = (art) => {
        for (let i = 1; i < assignedArt.length; i++) {
            if (art.getArtworkId() === assignedArt[i].id) {
                art.setTopPosition(parseInt(assignedArt[i].top * reverseScale));
                art.setLeftPosition(parseInt(assignedArt[i].left * reverseScale));
                return art;
            }
        }
    };

    canvasState.assignedArtwork = canvasState.assignedArtwork.map(propertize);

}

/**
 * Prevents saving assignments if canvas has overlap, allows otherwise.
 * @param event: event fired after 'SAVE' or 'SAVE AS' button is pressed.
 */

function openSaveFileModal(event) {
    event.preventDefault();

    let btn = event.target;
    let errorMsg = document.getElementById('error_alert');
    if (canvasHasOverlap()) {
        btn.setAttribute('data-bs-target', 'none');
        errorMsg.classList.add('show');
    } else {
        let modal = new bootstrap.Modal(document.getElementById("confirmation_modal"), {});
        modal.show();
        errorMsg.classList.replace('show', 'hide');
    }
    console.log(errorMsg)
}

/**
 * Checks whether any artwork images assigned in the canvas are overlapping.
 */

function canvasHasOverlap() {
    return canvasState.isOverlap;
}

export {artworkCanvas, canvasState, newArtworkSelection, placeSelectedImage};
