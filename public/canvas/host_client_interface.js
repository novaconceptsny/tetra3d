/**
 * This module contains functions for making network requests to create, retrieve, update and delete
 * assets & data used on artwork_assignment, spot_versions and spots_collection pages.
 **/


/** Makes a GET request for a spot's wall image, and data to display and apply on canvas, for artwork assignments.
 * @returns a JSON object containing background image asset and data to apply to canvas, and canvas parameters.
 */
async function getCanvasAssets() {
    let key = getWallParameter();
    const endpoint = `api/wall?background=${key}`;
    const resp = await generalRequest(endpoint);
    return resp[0];
}

/** Makes a GET request for all art selections to list in left menu as artwork assignment options for user.
 * @returns a list of art selection objects to populate left menu of artwork_assignment page.
 */
async function getArtworkSelections() {
    const endpoint = 'api/wall/artworks';
    return generalRequest(endpoint);
}

//todo::remove: not using anymore!
//這裡就是叫出artwork選取的地方
async function getPagedArtworkSelections(pageNum, locationId, artgroupId) {
    const endpoint = `api/wall/paged_artworks?page=${pageNum}&location=${locationId}&artgroup=${artgroupId}`;
    return await generalRequest(endpoint);
}

/** Makes a GET request for the user's collection of spots to display thumbnails on spots_collection page.
 * @param userId: id of the user whom this request is for.
 * @returns a list of JSON objects for each spot in collection, containing thumbnails and data for each spot.
 */
async function getUserSpotCollection(userId) {
    const endpoint = `api/collection?user=${userId}`;
    return await generalRequest(endpoint);
}

async function getUserLatestVersion() {
    let wall = getParameter('wall');
    const endpoint = `api/wall/latest?client=1&wall=${wall}`;
    return await generalRequest(endpoint);
}

async function getUserPreviousVersion() {
    let version = getParameter('version');
    const endpoint = `api/wall/previous?version=${version}`;
    return await generalRequest(endpoint);
}

/** Sends a POST request to server with data pertaining to a saved artwork assignment, for storage as a spot version.
 * @param filename: user designated string for saved version.
 * @param assignedArtwork: list of artwork selections added to canvas.
 * @param screenshots: object of two base64 encoded images: (thumbnail and hotspot), of the canvas.
 * @param canvasState: latest state of the canvas.
 * @returns JSON response from server of either the success or failure of the request, along with payload to reference.
 */
async function createSurfaceState(filename, assignedArtwork, screenshots, canvasState, reverseScale, userId) {
    const endpoint = updateCanvasRoute
    canvasState = JSON.parse(JSON.stringify(canvasState));
    canvasState.savedVersion = true;
    let payload = {
        "name": filename,
        "assigned_artwork": assignedArtwork,
        "thumbnail": screenshots['thumbnail'],
        "hotspot": screenshots['hotspot'],
        "canvasState": JSON.stringify(canvasState),
        "reverseScale": reverseScale,
        "user_id": userId,
        "spot_id": spot_id,
        "new": true,
    };

    var jsonPayload = JSON.stringify(payload);
    payload.assigned_artwork = JSON.stringify(payload.assigned_artwork);

    // simplePost(endpoint,payload);
    fakeFormPost(endpoint, payload);

    // return specificRequest(endpoint, 'POST', payload);
}

/**
 * Sends a PUT request to server with data of modifications made to the currently saved canvas.
 * @param versionId: id of most current saved version.
 * @param updates: object of added, removed and modified artworks
 * @param screenshots: object of two base64 encoded images: (thumbnail and hotspot), of the modified version.
 * @param canvasState: latest state of the canvas.
 * @returns {Promise<a>}
 */

//這邊就是將assignment存起來的地方了。重要!!!
function updateSurfaceState(versionId, updates, screenshots, canvasState, reverseScale, userId) {
    const endpoint = updateCanvasRoute;
    let payload = {
        "version_id": versionId,
        "assigned_artwork": updates['assignedArtwork'],
        "added": updates["added"],
        "removed": updates["removed"],
        "modified": updates["modified"],
        "thumbnail": screenshots['thumbnail'],
        "hotspot": screenshots['hotspot'],
        "canvasState": JSON.stringify(canvasState),
        "reverseScale": reverseScale,
        "userId": userId,
        "spot_id": spot_id,
    };
    payload.assigned_artwork = JSON.stringify(payload.assigned_artwork);
    payload.added = JSON.stringify(payload.added);
    payload.removed = JSON.stringify(payload.removed);
    fakeFormPost(endpoint, payload);
    // return specificRequest(endpoint, 'PUT', payload);
}

async function deleteUserSpotVersion(deletionData) {
    const endpoint = '/api/spot_versions/delete_version';
    return specificRequest(endpoint, 'DELETE', deletionData);
}


async function putUserActiveSpotVersion(activeVersionData) {
    const endpoint = '/api/spot_versions/put_active_version';
    return specificRequest(endpoint, 'PUT', activeVersionData);
}

async function getFilenameUsageForVersion(filename, canvasId) {
    let wall = getWallParameter();
    const endpoint = `/api/spot_versions/check_filename?filename=${filename}&clientId=${1}&canvasId=${canvasId}`;
    return await generalRequest(endpoint);
}

async function getAllUserActiveVersions() {
    const endpoint = '/api/spot_versions/get_all_active';
    return await generalRequest(endpoint);
}

//todo::remove:not using anymore
async function getSearchedArtworkResults(searchData, artgroupId) {
    const searchKey = searchData.searchKey;
    switch (searchData.searchOption) {
        case 'all':
            return getSearchedArtworksByAll(searchKey, artgroupId);
        case 'artist':
            return getSearchedArtworksByArtist(searchKey, artgroupId);
        case 'title' :
            return getSearchedArtworksByTitle(searchKey, artgroupId);
        default:
            break;
    }
}

async function getSearchedArtworksByArtist(searchKey, artgroupId) {
    const endpoint = `/filter/artist?key=${searchKey}&artgroup_id=${artgroupId}`;
    return await generalRequest(endpoint);
}

async function getSearchedArtworksByTitle(searchKey, artgroupId) {
    const endpoint = `/filter/title?key=${searchKey}&artgroup_id=${artgroupId}`;
    return await generalRequest(endpoint);
}

async function getSearchedArtworksByAll(searchKey, artgroupId) {
    const endpoint = `/filter/all?key=${searchKey}&artgroup_id=${artgroupId}`;
    return await generalRequest(endpoint);
}

/** Makes a basic GET request to retrieve fundamental data needed on all pages.
 * @param endpoint: URL for accessing a particular API route.
 * @returns a JSON response pertaining to the endpoint accessed.
 */
async function generalRequest(endpoint) {
    const response = await fetch(endpoint);
    return response.json();
}

/** Makes a request specific to the method and payload data provided for a particular API endpoint.
 * @param endpoint: URL for accessing an API route.
 * @param method: The type of HTTP request. Refer: https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
 * @param payload: The JSON data being sent to endpoint.
 * @returns a JSON response from endpoint.
 * **/
async function specificRequest(endpoint, method, payload) {
    const response = await fetch(endpoint, {
        method: method,
        body: JSON.stringify(payload),
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    return response.json();
}

function fakeFormPost(endpoint, payload) {
    var $form = $('<form>', {
        action: endpoint,
        method: 'post'
    });
    let token = $('meta[name="_token"]').attr('content');

    $.each(payload, function (key, val) {
        $('<input>').attr({
            type: "hidden",
            name: key,
            value: val
        }).appendTo($form);
    });
    $('<input>').attr({type: "hidden", name: "_token", value: token}).appendTo($form);
    $('<input>').attr({type: "hidden", name: "project_id", value: project_id}).appendTo($form);
    $('<input>').attr({type: "hidden", name: "hlookat", value: hlookat}).appendTo($form);
    $('<input>').attr({type: "hidden", name: "vlookat", value: vlookat}).appendTo($form);
    $form.appendTo('body').submit();
}


/**
 * A Spots Version page function. Retrieves versions for each wall for a spot to display on Spot Versions page.
 */
async function getSpotVersions() {
    let userId = getPathname().split('/')[2];
    let spotId = getPathname().split('/')[3];
    const endpoint = `/api/spot_versions/get_versions?user=${userId}&spot=${spotId}`;
    return generalRequest(endpoint);
}

async function getSpotVersionsByFloor(floorNumber) {
    const endpoint = `/api/spot_versions/get_versions_by_floor?floor=${floorNumber}`;
    return generalRequest(endpoint);
}

async function getSharedLinkForSpot(spotId, sharedVersions) {
    const endpoint = "/api/spot_versions/post_shared_versions";
    const payload = {'spotId': spotId, 'sharedVersions': sharedVersions};
    console.log("payload: ", payload);
    return specificRequest(endpoint, 'POST', payload);
}

async function getSharedLinkForFloor(sharedVersions) {
    const endpoint = "/api/spot_versions/post_shared_floor_versions";
    const payload = {'sharedVersions': sharedVersions};
    return specificRequest(endpoint, 'POST', payload);
}

async function postLastVtourLookLocation() {
    const endpoint = "/api/vtour/post_latest_lookat";
}

async function getLastVtourLookLocation() {
    const endpoint = "/api/vtour/get_latest_lookat";
}

/**
 * @returns {string | null}
 */
function getWallParameter() {
    return getParameter('wall');
}

/**
 *
 * @returns {string | null}
 */
function getUserParameter() {
    return getParameter('user');
}

function getPathname() {
    let url = new URL(window.location.href);
    return url.pathname;
}

/**
 * @param param
 * @returns {null}
 */
function getParameter(param) {
    let parameter = (new URL(window.location.href)).searchParams;
    let value = parameter.get(param);
    return (value !== null ? (value.length > 0 ? value : null) : null);
}

async function getAllFloors() {
    const endpoint = '/api/spot_versions/get_all_floors';
    return generalRequest(endpoint);
}

export {
    getCanvasAssets, getArtworkSelections, getUserParameter, getParameter,
    getUserSpotCollection, createSurfaceState, updateSurfaceState,
    getUserLatestVersion, getSpotVersions, deleteUserSpotVersion, putUserActiveSpotVersion,
    getUserPreviousVersion, getPagedArtworkSelections, getSearchedArtworkResults, getFilenameUsageForVersion,
    getSharedLinkForSpot, getAllUserActiveVersions, postLastVtourLookLocation, getLastVtourLookLocation, getAllFloors,
    getSpotVersionsByFloor, getSharedLinkForFloor,
};
