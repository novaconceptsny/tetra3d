import { ArtSelection } from './artwork_assignment_classes.js';
import { getCanvasAssets, getArtworkSelections, postUserArtAssignments, putUserArtAssignmentUpdates, getParameter,
    getUserLatestVersion, getUserPreviousVersion, getPagedArtworkSelections, getSearchedArtworkResults,
    getFilenameUsageForVersion } from './host_client_interface.js';

// window.onpaint = decodeUrl();
//這邊的assignedArtwork是所有放置了的artwork的集合。若對任一artwork有變動，就更改這裡面的數值。最後就將之送出就讓php處理
const canvasState = {
    savedVersion: false, isOverlap : false, defaultScale: 1.0, actualWidthInch: 0, background: null,
    currentVersionData: null, assignedArtwork:[],
    modifiedVersion: { addedArtwork: [], removedArtwork: [], modifiedArtwork: [] },
    recentSelection: null
};

//這邊initiate fabric js canvas
const artworkCanvas = new fabric.Canvas('artwork_canvas', {
    enableRetinaScaling: true,
    skipOffscreen: false,
});

const filterState = {
    searchOption: 'all', searchKey: null, results: null,
    isValidInput: function () { return this.searchKey !== null && this.searchKey !== "" }
};

let listItems = null;
let bookmark_icons = null;
let latestPage = 1;
let userBookmarks = [];
let boundingBox;
let filterMenu;
let searchButton;
let searchInput;
let filterButton;
let imgWidth = canvasDBArr.data.img_width;
let imgHeight = canvasDBArr.data.img_height;
let baseWidth;
let baseScale;

let reverseScale; //這個reverseScale，在整個artassignment裡面只會有一個數值。取決於canvase縮小放大的倍數


$(function(){

    // some commonly manipulated page elements
    const leftMenu = document.querySelector('.left_menu');
    const menuTop = document.getElementById('left_menu_top');
    const mainContent = document.querySelector('.main_content');    // canvas containing div

    //建立預設的數值
    // main content's dimensions for the canvas to fill.
    let mainHeight = mainContent.offsetHeight,
        mainWidth = mainContent.offsetWidth;
    // disable manual XY scaling by user.
    // fabric.Group.prototype.lockScalingX = true;
    // fabric.Group.prototype.lockScalingY = true;
    // Fabric.js canvas, main work area for user artwork assignments.
    artworkCanvas.setWidth(mainWidth);
    artworkCanvas.setHeight(mainHeight);


    //這邊要寫一個程式來計算是要scale height還是width。目前只有scale width
    if ((imgWidth / imgHeight)>=(mainWidth / mainHeight) ){
        //圖片比螢幕比例來得寬
        //依照寬度來縮
        console.log("wide:"+(mainWidth+":"+mainHeight+":"+imgWidth+":"+imgHeight));
        baseScale = mainWidth / imgWidth; //這邊就是螢幕縮放時的相對數據
        reverseScale = 1 / baseScale;
    }else {
        //圖片比螢幕比例來得窄
        //依照高度來縮
        baseScale = mainHeight / imgHeight; //這邊就是螢幕縮放時的相對數據
        reverseScale = 1 / baseScale;
        console.log("narrow"+(mainWidth+":"+mainHeight+":"+imgWidth+":"+imgHeight));
    }

    //下面這是舊的方式
    // baseWidth=1500; //這是我們一開始換算時。nara用來計算所有東西的長度。
    // baseScale=mainWidth/baseWidth; //這邊就是螢幕縮放時的相對數據
    // reverseScale = 1 / baseScale;


///************這邊長寬高的數字是假的，之後會改掉****************
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
    artworkCanvas.add(boundingBox); //這是fabric物件

    //按鈕連結的event大部份在下面這邊
    initPagination();
    // listener for deleting assigned artwork on 'Delete' key press
    $(document).on('keydown', handleDeleteKeyOnArtwork);
    // listener for confirming saved art assignments on 'Enter' key press
    $('#file_name').on('keydown', handleEnterKeyOnSaveAs);
    // listeners for button clicks in the 'edit tools' section.
    $('#remove_btn').on('click', removeSelectedArtwork);
    $('#cancel_btn').on('click', removeAllArtwork);
    $('#save_btn').on('click', updateSavedVersion);
    $('#save_as_btn').on('click', saveNewVersion);
    $('#confirm_save_btn').on('click', confirmSave);
    $('#crop_btn').hide();
    // $('#vtour_btn').on('click', makeVtourRoute);
    disableSaveButton();


    $('#confirmation_modal').on('show.bs.modal', function (event) {
        $('#crop_btn').hide();
    }).on('hidden.bs.modal',function(event){
        $('#crop_btn').show();
    })


    // listener for on-scroll event in left menu.
    // leftMenu.onscroll = addLeftMenuScrollEvents;  // disabled for demo
    addCanvasEvents();

    // makes request to backend to retrieve all necessary data for a new canvas.
    //canvasDBArr是從該頁面一開始來的
    requestCanvasBackgroundProperties(canvasDBArr,baseScale);

	//叫出所有的artwork。並顯示在版面上
    //artworkArr陣列是從該頁面一開始來的。
    if (artworkArr) { //null equal to false

        artworkArr.forEach((art) => {
            art= remapArtVariable(art);
            art = deserializeArtSelection(art);
	        placeSelectedImage(art, (art.getTopPosition()*baseScale), (art.getLeftPosition()*baseScale));
	    });
	    canvasState['savedVersion'] = true;
        canvasState['currentVersionData'] = latestState;
        canvasState['currentVersionData'].version_id;
	    setTitle(latestState.version_name);
	    disableSaveButton();
	    addSavedVersionEvents();
	}

    filterMenu = $('#filter_menu');
    searchButton = $('#search_button');
    filterButton = $('#filter_button');
    searchInput = document.getElementById('search_input');



    filterMenu.click(function(event) {
        const target = event.target;
        filterButton.text(target.innerText);
        filterState.searchOption = target.dataset.option;
        resetLeftMenuView();
        updateSearchBarPlaceholder();
    });

    searchButton.click(function ()  {
        updateSearchKey();
        printFilterState();
        makeSearchRequest().catch(error => {
            console.log("error on artwork filtration: ", error)
            }
        );
    });

    /**
     * sets events for detecting updates from the search/filter text-input field.
     * @param event: triggered when a key is released from being pressed.
     */
    searchInput.onkeyup = function handleSearchInput(event) {
        updateSearchKey();
        if (event.key === "Enter") {
            printFilterState();
            if (!filterState.isValidInput()) {
                displayInvalidSearchMessage(false);
                loadPagedArtworkSelections(latestPage,locationId,artgroupId);
            } else {
                makeSearchRequest().catch(error => console.log("error on artwork filtration: ", error));
            }
        }
        if (event.key === "Backspace" || event.key === "Delete") {
            // automatically displays previously loaded page items when the search bar is cleared.
            if (!filterState.isValidInput()) {
                resetLeftMenuView();
            }
        }
    };
});


function remapArtVariable(art) {
    let tempArr=[];
    tempArr['title'] = art['title'];
    tempArr['imgUrl'] = art['image_url'];
    tempArr['artworkId'] = art['artwork_id'];
    tempArr['topPosition'] = art['top_position'];
    tempArr['leftPosition'] = art['left_position'];
    tempArr['cropData'] = JSON.parse(art['crop_data']);
    tempArr['overrideScale'] = art['override_scale'];
    console.log('her for overrideScale', art['override_scale']);
    return tempArr;
}

/**
 * Makes a GET request to server for a list of artwork selection objects, based on page offset, as per pagination.
 * @param page: the page number corresponding to a set artwork selections to display.
 */
function loadPagedArtworkSelections(page,location, artgroupId) {
    getPagedArtworkSelections(page, location, artgroupId).then((jsonResponse) => {
        const data = jsonResponse['page']['data'];
        clearArtworkList();
        renderArtworkList(data);
        resetScrollPosition();
    });
}

/** removes all arwork selections from view. **/
function clearArtworkList() {
    let artList = document.getElementById('artwork_list');
    while (artList.firstChild) artList.removeChild(artList.firstChild);
}

/** positions the scrollbar at the top of the left menu's bottom section, after page is chaged via pagination. **/
function resetScrollPosition() {
    const leftMenuBottom = document.getElementById('left_menu_bottom');
    if (leftMenuBottom.scrollTop > 0) leftMenuBottom.scrollTop = 0;
}


/** initializes pagination functionality */
function initPagination() {

    $(function () {

        console.log('artworkTotalNum:'+artworkTotalNum);
        let totalPageNum=Math.ceil(artworkTotalNum/20);
        const load = loadPagedArtworkSelections;
        window.pagObj = $('#pagination').twbsPagination({
            totalPages: totalPageNum,
            visiblePages: 3,
            onPageClick: function (event, page) {
                console.log('locationId-initial:'+locationId);
                load(page,locationId);
                latestPage = page;
            }
        }).on('page', function (event, page) {
            displayInvalidSearchMessage(false);
            console.log(`page ${page} loaded.`);
        });
    });
}

/**
 * 這邊就是列出artwork來讓我們挑選的地方。
 * Displays artwork selection cards in the lower section of left menu when page loads, pagination is changed,
 * or filtered search results are resolved.
 * @param artworks: a list of artwork objects requested from server.
 */
function renderArtworkList(artworks) {
    document.getElementById('artwork_list').append(
        ...artworks.map(art => {
            let li = document.createElement('li');
            let selection = getArtworkSelectionTemplate(
                art['title'], art['artist'], art['artwork_id'], art['thumbnail_url'], art['image_url']
                ,art['width_inch'],art['height_inch']
            );
            addArtworkSelectionListener(selection.querySelector('.card'));
            li.append(selection);
            return li;
        })
    );
    listItems = document.querySelectorAll('.card');
}

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

function requestCanvasBackgroundProperties(canvasAssetData, baseScale) {
///************where changes may be required****************
    let data = canvasAssetData['data'];

    renderCanvasBackground(canvasAssetData['background_url'], baseScale);
    canvasState.background = canvasAssetData['background_url'];
    canvasState['actualWidthInch'] = data['actual_width_inch'];
    if (canvasAssetData['overlay_url'] !== null) {
        setCanvasOverlay(canvasAssetData['overlay_url']);
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
function renderCanvasBackground(imgUrl,baseScale) {
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

/** listens for mouse scroll events in the left menu to relocate the search bar to top of menu, for ease of use.  **/
function addLeftMenuScrollEvents() {

    let originalParent = document.getElementById('left_menu_bottom').children[0];
    let searchBar = document.getElementById('search_bar');
    let btnAppend = document.getElementById('btn-append');
    try {
        if (leftMenu.scrollTop >= 45) {              // if scrolling down
            originalParent.removeChild(searchBar);
            searchBar.classList.remove('animated', 'fadeInDown', 'faster', 'pt-4');
            searchBar.classList.add('animated', 'fadeInUp', 'faster', 'ml-3', 'mr-3', 'pt-3');
            btnAppend.classList.add('mr-1');
            btnAppend.classList.remove('mr-3');
            menuTop.appendChild(searchBar);
        } else if (leftMenu.scrollTop <= 45) {       // if scrolling up
            menuTop.removeChild(searchBar);
            originalParent.insertBefore(searchBar, originalParent.childNodes[0]);
            searchBar.classList.remove('animated', 'fadeInUp', 'faster', 'ml-3', 'mr-3');
            searchBar.classList.add('animated', 'fadeInDown', 'faster', 'pt-4');
            btnAppend.classList.remove('mr-1');
            btnAppend.classList.add('mr-3');
        }
    } catch (DOMException) { }
}

/** adds events for mouse hover, exit, click and release, to artwork card list items.
 *  @param cardElement: the element representing a listed artwork to be selected by user.
 */
function addArtworkSelectionListener(cardElement) {
    const defaultAttribute = "card text-center";
    const hoverAttribute = `${defaultAttribute} shadow`;
    const clickAttribute = `${defaultAttribute} border-secondary shadow-sm`;

    cardElement.addEventListener('mouseover', (event) => { event.currentTarget.setAttribute('class', hoverAttribute) });
    cardElement.addEventListener('mouseleave', (event) => { event.currentTarget.setAttribute('class', defaultAttribute) });

    cardElement.addEventListener('mousedown', (event) => {
        if (!event.target.matches('.bookmark_icon')) {      //check to avoid conflict with '.bookmark_icon' click event
            event.currentTarget.setAttribute('class', clickAttribute);
        } else {
            // eliminate card selection effect to distinguish between bookmark toggle states
            event.currentTarget.style.transform = "scale(1.02)";
        }
    });
    cardElement.addEventListener('mouseup', (event) => {
        if (!event.target.matches('.bookmark_icon')) {      //check to avoid conflict with '.bookmark_icon' click event
            event.currentTarget.setAttribute('class', clickAttribute);
        } else {
            // reverts to show previous effect for card clicks.
            event.currentTarget.style.transform = null;
        }
    });
    cardElement.addEventListener('click', (event) => {
        if  (!event.target.matches('.bookmark_icon')) {     //check to avoid conflict with '.bookmark_icon' click event
            let target = event.currentTarget;
            let newSelection = newArtworkSelection(target);
            placeSelectedImage(newSelection);
        }
    });
}

/** Extracts all relevant data from the selected artwork's HTML element.
 * @param selectedElement: the element which triggered user click event.
 * @returns object: containing values of the artwork's title: str, image URL: str, artwork ID: str, whether bookmarked: bool.
 */
function getSelectionData (selectedElement) {
    let title = selectedElement.dataset.title;
    let imgUrl = selectedElement.dataset.imgUrl;
    let artworkId = selectedElement.dataset.artworkId;
    let scale = selectedElement.dataset.scale;
    return { title , imgUrl, artworkId, scale };
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

/** adds event listeners to each bookmark icon for toggling selected (filled) and unselected (unfilled) styles, on click. **/
function addBookmarkIconEvents() {
    for (let i = 0 ; i < bookmark_icons.length ; i++) {
        bookmark_icons[i].addEventListener('click', (event) => {
            let targetElement = event.currentTarget;
            let containingElement = event.currentTarget.parentElement.parentElement;
            if (targetElement.classList.contains('far')) {        // selected
                targetElement.classList.replace('far', 'fas');
                addBookmark(containingElement);
            } else {
                targetElement.classList.replace('fas', 'far');    // unselected
                removeBookmark(containingElement);
            }
        });
    }
}

/** adds event listener for when images are moved in the canvas' bounding box area. **/
function addCanvasEvents() {
    // images being moved are tried against the bounding box to prevent inappropriate placements within canvas.

    artworkCanvas.on('object:moving', function (options) {
        if (options.target) {
            let object = options.target;
            // const xyScale = canvas State.defaultScale;c
            $('#crop_btn').css('top', object.top);
            let locLeft = object.left + ($('.left_menu').width() - $('#crop_btn').width()/2-20);
            $('#crop_btn').css('left', locLeft);
            // target object's right and bottom edge boundaries (originally not provided)
            let xyScale=defaultScaleArr[object.id];
            const objectBottom = Math.abs(object.top + (object.height * xyScale)),
                objectRight  = Math.abs(object.left + (object.width * xyScale));
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
                object.set({top: y });
            }
            if (objectRight > rightBound) {
                let x = rightBound - Math.abs(object.width * xyScale);
                object.set({left: x });
            }
        }
    });
}

/** Initializes events for the purpose of detecting changes to the canvas after a save has been made. */
function addSavedVersionEvents() {
    let handleModification = (event, msg) => {
        if (canvasState.savedVersion && canvasState.currentVersionData !== null) {
            console.log('object changed on canvas post save: ', event.target);
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

    let adaptedScale = a * (b/c);

    // image.scaleToHeight(adaptedScale, false);
    image.scaleToWidth(adaptedScale, false);
    canvasState.defaultScale = image.scaleX ;   // XY scale changes when calling scaleToWidth() on image.
    defaultScaleArr[image.id]=image.scaleX

    if (overrideScale != null) {
        c = canvasState.actualWidthInch * overrideScale;
    }
    adaptedScale = a * (b / c);
    image.scaleToWidth(adaptedScale, false);

    console.log('adaptedScale:', adaptedScale);
    console.log('canvasState.defaultScale:', image.scaleX);
}


/** Marks an artwork card as selected on user click, and places its image in the canvas.
 * @param artSelection: an ArtSelection object initialized from a user selected artwork to place on canvas.
 * @param topPos: the image's starting position from the top in pixels, to place relative to canvas.
 * @param leftPos: the image's starting position from the left in pixels, to place relative to canvas.
 */
function placeSelectedImage(artSelection, topPos=boundingBox.top, leftPos=boundingBox.left) {

    let imgUrl = artSelection.imgUrl;
    let notSelected = !isAlreadySelected(artSelection.artworkId);
    if (notSelected) {
        // $('#loadingModal').modal('show');
        fabric.Image.fromURL(imgUrl, function(myImg) {
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
            console.log('scale: ', artSelection.scale)

            let overrideScale = artSelection.overrideScale;

            if (scale==null){scale =96;}
            applyAdaptiveRescale(img1,scale,overrideScale);
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
function isAlreadySelected (artId) {
    let canvasObjects = artworkCanvas.getObjects();
    for (let i = 1 ; i < canvasObjects.length ; i++) {
        let presentId = canvasObjects[i].id;
        if (artId === presentId) {
            artworkCanvas.setActiveObject(canvasObjects[i]);
            artworkCanvas.renderAll();
            return true;
        }
    }
    return false;
}

/** Invoked when a bookmark icon is clicked (toggled on) for an artwork image; adds the image data onto bookmarks list.
 * @param targetElement: the element for which a bookmark click applies to; containing data about the selection to
 * bookmark.
 */
function addBookmark (targetElement) {
    let selected = newArtworkSelection(targetElement);
    for (let i = 0 ; i < userBookmarks.length ; i++) {
        if (userBookmarks[i].getTitle() === selected.getTitle()) {
            return;
        }
    }
    userBookmarks.push(selected);
    console.log("current bookmarks: ", userBookmarks);
}


/** Invoked when a bookmark icon is clicked (toggled off) for an artwork image; removes image's data from the bookmarks list.
 *  @param targetElement: the element for which a bookmark click applies to; containing data of an un-bookmarked
 *  selection.
 */
function removeBookmark (targetElement) {
    let unselected = newArtworkSelection(targetElement);
    userBookmarks = userBookmarks.filter(obj => obj.getTitle() !== unselected.getTitle());
    console.log("current bookmarks: ", userBookmarks);
}


/** Removes all selected artwork in the canvas from view. **/
function removeSelectedArtwork() {
    const selectedObjs = artworkCanvas.getActiveObjects();
    selectedObjs.forEach((obj) => {
        artworkCanvas.remove(obj);
        canvasState.assignedArtwork = canvasState.assignedArtwork.filter(art => art.getArtworkId() !== obj.id);
    });
    console.log("currently selected objects: ", canvasState.assignedArtwork);
}


/** Removes all artwork present on canvas from view. **/
function removeAllArtwork() {
    artworkCanvas.getObjects()
        .filter((obj) => { return obj.type === 'image' })
        .map((obj) => { artworkCanvas.remove(obj) });
    canvasState.assignedArtwork = [];
}


/** Invoked by 'SAVE AS' button when clicked to save new assignments to the user edited canvas.
 * @param event: the event that triggered this function. Used for identifying clicked buttons.
 */
function saveNewVersion(event) {
    checkOverlapping(event);
}


/**
 * Applies changes made since last save to an artwork assignment version.
 * @param event: The event fired from a 'SAVE' button press.
 * @returns {Promise<void>}
 */


function updateSavedVersion(event) {
    checkOverlapping(event);
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
        let theVersionId=versionId; //get from global
        // const response = await putUserArtAssignmentUpdates(versionId, updates, screenshots, canvasState);
        putUserArtAssignmentUpdates(theVersionId, updates, screenshots, canvasState, reverseScale,userId);

        // confirmSaveSuccess(response, document.getElementById('assignment_title').textContent);
        // clearTransientData();
    }
}


/**
 * @param art
 * @returns {ArtSelection}
 */
function deserializeArtSelection (art) {
    return new ArtSelection(art['title'], art['imgUrl'], art['artworkId'], art['topPosition'],
        art['leftPosition'], art['cropData'],art['overrideScale']);
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




//這邊就是存檔的地方了//這邊就是存檔的地方了
//這邊就是存檔的地方了//這邊就是存檔的地方了
//這邊就是存檔的地方了//這邊就是存檔的地方了
//這邊就是存檔的地方了//這邊就是存檔的地方了
//這邊就是存檔的地方了//這邊就是存檔的地方了
//這邊就是存檔的地方了//這邊就是存檔的地方了
//這邊就是存檔的地方了//這邊就是存檔的地方了
/** Invoked by 'CONFIRM SAVE' button within the 'Save Canvas' modal dialog box. Verifies criteria before saving. **/
async function confirmSave() {
    let filenameInput = document.getElementById("file_name");
    let confirmBtn = document.getElementById('confirm_save_btn');

    let filename = filenameInput.value.trim();
    // let filenameExists = await isExistingFilename(filename);
    confirmBtn.setAttribute('data-dismiss', '');

    if (filename.length !== 0) {
        if (filenameInput.classList.contains('is-invalid')) {
            filenameInput.classList.remove('is-invalid');
        }
        $('#confirmation_modal').modal('hide');
        let screenshots = exportArtAssignments(filename);
        filenameInput.value = "";
        setAssignmentProperties(reverseScale);


        if (canvasState.assignedArtwork.length!=0){
            const response = await postUserArtAssignments(
                filename, canvasState.assignedArtwork, screenshots, canvasState,reverseScale,userId
            );
            // confirmSaveSuccess(response, filename);
        } else {
            console.log('you do not have any artwork assigned');
        }

    } else {
        filenameInput.classList.add('is-invalid');
        let invalidFeedback = document.querySelector('.invalid-feedback');
        // let roomName = document.getElementById('room_name').innerText.split(':')[0];
        console.log('you are in the wrong location');
        invalidFeedback.innerText = filenameExists ?
            `File name: "${filename}" already exists for a version of ${roomName}.` : "No file name provided.";
    }

}

/**
 * creates route for KRPano 360 preview page to redirect on successful art assignment save.
 */
 ///這個之後應該移到php來進行。
function makeVtourRoute() {

    window.location.href = "/vtour/post/"+spotId+"?vlookat="+vlookat+"&hlookat="+hlookat;
}

/**
 *  Checks if user saved artwork assignments have been successfully sent to, and processed on the server-side.
 * @param response: JSON data received from latest version save or update.
 * @param versionName: User designated name of the saved or updated version.
 */
function confirmSaveSuccess(response, versionName) {
    if (response['response_code'] === 200) {
        let successMsg = document.getElementById('success_alert');
        canvasState['savedVersion'] = true;
        canvasState['currentVersionData'] = response['current_version_data'];
        setTitle(versionName);
        successMsg.classList.add('show');
        let finalize = () => {
            successMsg.classList.replace('show', 'hide');
            makeVtourRoute();
        };
        executeDelayedAction(finalize, 1500);
    }
    console.log("current version data: "+JSON.stringify(canvasState.currentVersionData));
}


/** Enables 'SAVE' button after a new version has been saved, to allow any changes to the saved version to be updated. **/
function enableSaveButton() {
    let saveBtn = document.getElementById('save_btn');
    saveBtn.disabled = false;
}


function disableSaveButton() {
    document.getElementById('save_btn').disabled = true;
}

/** Captures and saves two screenshots: one of the entire canvas, and another of the bounding area where art is displayed.
 *  @param filename: user designated name of the file to save.
 *  @returns object with keys of 'thumbnail' and 'hotspot' mapped to base64 encoded image values
 *           of both screenshots respectively.
 */
 ///這邊來製作screenshot ///這邊來製作screenshot ///這邊來製作screenshot ///這邊來製作screenshot
  ///這邊來製作screenshot ///這邊來製作screenshot ///這邊來製作screenshot ///這邊來製作screenshot
function exportArtAssignments(filename) {

    function captureCanvas() {
        return artworkCanvas.toDataURL({
            format: 'png',
        });
    }
    function captureHotspot()  {
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

    return { 'thumbnail': captureCanvas(), 'hotspot': captureHotspot()};
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
        for (let i = 1 ; i < assignedArt.length ; i++) {
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
 * Checks whether any artwork images assigned in the canvas are overlapping.
 * Prevents saving assignments if so, allows otherwise.
 * @param event: event fired after 'SAVE' or 'SAVE AS' button is pressed.
 */

 // overlap function
function checkOverlapping(event) {
    let btn = event.target;
    let errorMsg = document.getElementById('error_alert');
    // updateOverlapDetection();
    if (canvasState.isOverlap) {
        btn.setAttribute('data-target', 'none');
        errorMsg.classList.add('show');
        console.log("Cannot save! \nOverlap detected on canvas between 2 or more images. ");
    } else {
        btn.setAttribute('data-target', '#confirmation_modal');
        errorMsg.classList.replace('show', 'hide');
    }
}

function executeDelayedAction(action, delay) {
    setTimeout(() => { action() }, delay);
}

/** Updates boolean value for whether any artwork images within the canvas overlap. Prevents erroneous layouts. **/
function updateOverlapDetection() {
    let allObjects = artworkCanvas.getObjects();
    canvasState.isOverlap=false;
    let startNum=2;
    for (let i = startNum ; i < allObjects.length ; i++) {
        for (let j = allObjects.length - 1 ; j >= startNum ;  j--) {
            canvasState.isOverlap = allObjects[i] !== allObjects[j] &&
                allObjects[i].intersectsWithObject(allObjects[j]);
            if (canvasState.isOverlap === true) { return; }
        }
    }
}


//每個artwork卡片的地方
//每個artwork卡片的地方
//每個artwork卡片的地方
//每個artwork卡片的地方
/** @returns DocumentFragment template for an individual artwork selection card in the left menu pane.
 *  @params title: title of the artwork selection.
 *  @params id: unique id number of the artwork selection.
 *  @params img: url path to image of the artwork selection.
 */
function getArtworkSelectionTemplate(title, artist, id, thumb_url, img_url,width,height) {
    return document.createRange().createContextualFragment(
        `<div class="card text-center animated fadeIn faster"
                data-img-url="${img_url}"
                data-title="${title}"
                data-thumb-url="${thumb_url}"
                data-artwork-id="${id}">
                <img src=${thumb_url} class="card-img-top img-fluid" width="100" height="auto" alt="...">
                <div class="card-body card_txt pb-3">
                    <h5 class="card-title artwork_title mt-3"
                    style="${title.length > 17 ? 'font-size: 14.5px' : ''}">${title}</h5>
                    <span class="card-text artwork_id" style="${title.length > 17 ? 'font-size: 14.5px' : ''}">${artist}</span>
                    <br>
                    <span>[${width}x${height}]</span>
                </div>
            </div>`
    );
}

/** @returns DocumentFragment template for invalid search result placeholder. **/
function getPlaceholderTextMarkup() {
    const format = () => {
        return filterState.searchKey.length >= 12 ? `<br><i>"${filterState.searchKey}"</i><br>`
            : `<i>"${filterState.searchKey}"</i><br>`;
    };
    const getSearchType = () => {
        return filterState.searchOption === "title" ? "title" : filterState.searchOption === "artist" ? "artist" : "";
    };
    return document.createRange().createContextualFragment(`
            <div class="mx-auto mt-5 pt-5 animated fadeIn faster" id="invalid_search_msg">
                <div class="mx-auto mb-2 pt-5 mt-5" style="width: fit-content;">
                    <i class="fas fa-exclamation-circle fa-4x"></i>
                </div>
                <p class="text-break" style="font-family: cadiz black;">Search for ${getSearchType()}: ${format()} found no results</p>
            </div>
    `);
}

/**
 * displays an indicator of the number of results retrieved from a search.
 * @param flag: boolan value that toggles search result counter display (true: show, false: hide).
 */
function displaySearchResultCounter(flag) {
    let bottomNav = document.getElementById('artwork_navbar');
    let counterMarkup = null;

    if (flag && filterState.results !== null) {
        displaySearchResultCounter(false); // to clear already appended markup
        counterMarkup = getSearchResultCounterMarkup(filterState.results.length);
        bottomNav.appendChild(counterMarkup);
    } else {
        counterMarkup = document.getElementById('result_counter');
        if (counterMarkup !== null) {
            bottomNav.removeChild(counterMarkup);
        }
    }
}

function getSearchResultCounterMarkup(numResults) {
    let resultTxt = numResults > 1 ? `${numResults} results found` : `${numResults} result found`;
    return document.createRange().createContextualFragment(
        `<p class="mt-2 animated fadeIn faster" id="result_counter">${resultTxt}</p>`
    );
}

// function displayLoadingImageModal() {
//     let loading = () => {
//         if (canvasState.recentSelection.isOnScreen(true)) { clear() }
//     };
//     let interval = setInterval(loading.bind(null), 500);
//     let clear = () => {
//         $('#loadingModal').modal('hide');
//         clearInterval(interval)
//     }
// }

function timeIt(funct) {
    let start = window.performance.now();
    funct();
    let end = window.performance.now();
    return end - start;
}

//整理URL應該不需要了
function decodeUrl() {
    let link = window.location.href;
    if (link.includes('&amp;')) {
        link = link.replace('&amp;', '&');
        window.location.href = link;
    }
}

/** below is code for search/filter functionalia **/




function printFilterState() {

    console.log("filter data: ", filterState);
}


/**
 * displays indication for no results being found for an artwork search.
 * @param flag: boolan value that toggles invalid search message display (true: show, false: hide).
 */
function displayInvalidSearchMessage(flag) {
    let displayedMsg = document.getElementById('invalid_search_msg');
    let artListDiv = document.getElementById('left_menu_bottom');
    if (flag) {
        if (displayedMsg === null) {
            artListDiv.appendChild(getPlaceholderTextMarkup());
        } else {
            displayInvalidSearchMessage(false);
            artListDiv.appendChild(getPlaceholderTextMarkup());
        }
    } else if (displayedMsg !== null) {
        artListDiv.removeChild(displayedMsg);
        displaySearchResultCounter(false);
    }
}

/** Renders artwork retrieved from a valid search result, or an error message indicating no results for a search. */
function displayArtworkSearchResults() {
    clearArtworkList();
    hidePagination(true);
    if (filterState.results !== null) {
        displayInvalidSearchMessage(false);
        displaySearchResultCounter(true);
        renderArtworkList(filterState.results);
    } else {
        displayInvalidSearchMessage(true);
        displaySearchResultCounter(false);
    }
}

/**
 * Makes a server request to search for user specified keyword in search-bar.
 */
async function makeSearchRequest() {
    if (filterState.isValidInput()) {
        const response = await getSearchedArtworkResults(filterState,artgroupId);
        filterState.results = response[0].length > 0 ? response[0] : null;
        displayArtworkSearchResults();
        console.log("filter response: ", response, "\nresults: ", filterState);
    }
}

/** invoked when contents in the search-bar is modified by user, or when search is pressed. **/
function updateSearchKey() {
    filterState.searchKey = searchInput.value;
}

/** changes search-bar placeholder text based on filter criteria selection. */
function updateSearchBarPlaceholder() {
    const placeholders = [
        'Enter artwork title to search', 'Enter artist name to search', 'Enter artwork title or artist name to search'
    ];
    let searchbar = $("#search_input");
    if (filterState.searchOption === 'title') {
        searchbar.attr('placeholder', placeholders[0]);
    } else if (filterState.searchOption === 'artist') {
        searchbar.attr('placeholder', placeholders[1]);
    } else {
        searchbar.attr('placeholder', placeholders[2]);
    }
}


function hidePagination(flag) {
    let pagination = $('#pagination');
    flag ? pagination.hide() : pagination.show();
}

function resetLeftMenuView() {
    console.log('locationId-resetleftmenu:'+locationId);
    displayInvalidSearchMessage(false);
    displaySearchResultCounter(false);
    loadPagedArtworkSelections(latestPage,locationId,artgroupId);
    hidePagination(false);
    document.getElementById('search_input').value = "";
}


export { artworkCanvas, canvasState, newArtworkSelection, placeSelectedImage };
