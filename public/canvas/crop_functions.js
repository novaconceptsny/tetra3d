import { artworkCanvas, canvasState } from "./artwork_assignment.js";


    let pos = [0, 0];
    let cropSelection = document.getElementById('artwork_canvas').getBoundingClientRect();
    pos[0] = cropSelection.left;
    pos[1] = cropSelection.top;

    let mouseX = 0;
    let mouseY = 0;
    let cropping = false;
    let disabled = true;
    let tempLock=false;
    let selectionRect = new fabric.Rect({
        fill: 'transparent',
        originX: 'left',
        originY: 'top',
        stroke: '#D4E9F7',
        strokeDashArray: [7,7],
        strokeWidth: 2.1,
        cornerSize: 12,
        opacity: 1,
    });

    selectionRect.visible = false;
    artworkCanvas.add(selectionRect);
    artworkCanvas.renderAll();

    const cropBtn = document.getElementById('crop_btn');
    // crop button click event
    cropBtn.addEventListener('mousedown', cropToggleHandler, false);
    const cropAlert = document.getElementById('crop_active_alert');
    $(cropAlert).hide();


    artworkCanvas.on("mouse:down", function (event) {
        if (disabled) return;
        selectionRect.set({
            left: event.e.pageX - pos[0],
            top: event.e.pageY - pos[1],
            height: 1,
            width: 1,
            visible: true,
        });
        mouseX = event.e.pageX;
        mouseY = event.e.pageY;
        cropping = true;
        artworkCanvas.bringToFront(selectionRect);
    });

    artworkCanvas.on("mouse:move", function (event) {
        if (!disabled && cropping) {
            selectionRect.set({
                width: event.e.pageX - mouseX,
                height: event.e.pageY - mouseY
            });
            artworkCanvas.renderAll();
        }
    });

    artworkCanvas.on("object:scaling",function(e){
        var scaledObject = e.target;
        let scaledArt = canvasState.assignedArtwork.filter(art => {
            return art.artworkId === scaledObject.id;
        })[0];
        var newScale = (defaultScaleArr[scaledObject.id] / scaledObject.scaleX  ) * scaleArr[scaledObject.id];
        console.log('defaultScale',defaultScaleArr[scaledObject.id]);
        console.log('scaleX', scaledObject.scaleX);
        console.log('scale', scaleArr[scaledObject.id]);
        console.log('OverrideScale', newScale);
        scaledArt.setOverrideScale(newScale);


    });


    artworkCanvas.on("mouse:up", function (event) {
        cropping = false;
    });

    let allCropDataApplied = false;
    let numArtsCropped = 0;

    artworkCanvas.on("before:render", () => { applyCropData() });

    /** Keeps track of all previously cropped assignments as they're rendered, to prevent redundant crop function calls. **/
    artworkCanvas.on("object:added", (rendered) => {
        if (!allCropDataApplied) {
            const croppedArtIds = getCroppedArtAssignments().map(art => { return art.artworkId });
            if (croppedArtIds.includes(rendered.target.id)) numArtsCropped++;
        }
    });



$('#crop_btn').on('mousedown',function(){
    toggleCropMode(disabled);
    console.log(disabled);
}).on('mouseover',function(){
    tempLock=true;
}).on('mouseout',function(){
    tempLock=false;
});

artworkCanvas.on('object:selected', function (options) {
    showAndMoveBtn(options);
}).on('selection:cleared', function (obj) {
    if (disabled && !tempLock) {
        $('#crop_btn').hide();
    }
}).on('selection:updated', function (options) {
    showAndMoveBtn(options);
});

function showAndMoveBtn(options) {
    if (disabled && !tempLock) {
        console.log('move');
        let object = options.target;
        $('#crop_btn').show();
        $('#crop_btn').css('top', object.top);
        var locLeft = object.left + ($('.left_menu').width() - $('#crop_btn').width() / 2 - 20);
        $('#crop_btn').css('left', locLeft);
    }
}

function cropToggleHandler(event) {
    console.log("Toggled crop mode on.");
    if (!disabled) cropArt();
    // toggleCropMode(disabled)
}

function toggleCropMode(isEnabled) {
    toggleArtCanvasFreeze(!isEnabled);
    disabled = !isEnabled;
    toggleCropModeUI();
    if (disabled) {
        selectionRect.visible = false;
        artworkCanvas.renderAll();
    }
}

function toggleCropModeUI() {
    if (!disabled) {
        $(cropBtn).html('<i class="fas fa-check-circle"></i>');
        $(cropAlert).show();
    } else {
        $(cropBtn).html('<i class="fas fa-crop-alt"></i>');
        $(cropAlert).hide();
    }
}


function toggleArtCanvasFreeze(flag) {
    artworkCanvas.getObjects()
        .filter((obj) => { return obj.type === 'image' })
        .map((obj) => { obj.selectable = flag });
    artworkCanvas.setActiveObject(selectionRect);
}

function getCropData(cropTarget) {
    let left = selectionRect.left - cropTarget.left;
    let top = selectionRect.top - cropTarget.top;
    let scale = cropTarget.scaleX;

    left *= 1 / scale;
    top *= 1 / scale;
    left -= cropTarget.width/2;
    top -=  cropTarget.height/2;

    let width = (selectionRect.width * 1 / scale);
    let height = selectionRect.height * 1  / scale;

    return { top, left, width, height };
}

/**
 * The following code below is responsible for re-applying crop to previously saved art assignments cropped by the user.
 * A preventative measure is implemented for extra calls to crop function afterwards.
 **/

function getCroppedArtAssignments() {
    return canvasState.assignedArtwork.filter(art => {
        return art.cropData !== null;
    });
}

function cropImg(cropTarget, cropData) {

    cropTarget.clipTo = function (ctx) {
        ctx.rect(cropData.left, cropData.top, cropData.width, cropData.height);
    };
    let croppedArt = canvasState.assignedArtwork.filter(art => {
        return art.artworkId === cropTarget.id;
    })[0];
    croppedArt.setCropData(cropData);
}

function cropArt() {
    //find the object being clipped: via the rect.
    let artworks = artworkCanvas.getObjects().filter((obj) => {
        return obj.type === 'image';
    });
    const targetIndex = artworks.findIndex((img) => {
        return img.containsPoint(selectionRect.getCenterPoint())
    });

    if (targetIndex < 0) return;

    let cropTarget = artworks[targetIndex];
    let cropData = getCropData(cropTarget);
    cropImg(cropTarget, cropData);
}


/** Invokes crop function on previously cropped art assignments. **/
function applyCropData() {
    if (!allCropDataApplied) {   // to prevent unnecessary calls to crop, after all cropped arts are rendered.
        let croppedArts = getCroppedArtAssignments();
        croppedArts.map((art, index) => {
            let idx = artworkCanvas.getObjects().findIndex(obj => {

                return art.artworkId === obj.id;

            });

            if (idx > -1) {
                // console.log(artworkCanvas.getObjects()[idx]);
                cropImg(artworkCanvas.getObjects()[idx], art.getCropData());
                allCropDataApplied = numArtsCropped === croppedArts.length
            }
        });
    }
}
