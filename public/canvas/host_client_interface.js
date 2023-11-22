/**
 * This module contains functions for making network requests to create, retrieve, update and delete
 * assets & data used on artwork_assignment, spot_versions and spots_collection pages.
 **/



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
 * @param surfaceStateId: id of most current saved version.
 * @param updates: object of added, removed and modified artworks
 * @param screenshots: object of two base64 encoded images: (thumbnail and hotspot), of the modified version.
 * @param canvasState: latest state of the canvas.
 * @returns {Promise<a>}
 */

//這邊就是將assignment存起來的地方了。重要!!!
function updateSurfaceState(surfaceStateId, updates, screenshots, canvasState, reverseScale, userId) {
    const endpoint = updateCanvasRoute;
    let payload = {
        "surface_state_id": surfaceStateId,
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


function fakeFormPost(endpoint, payload) {
    let $form = $('<form>', {
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
    $('<input>').attr({type: "hidden", name: "layout_id", value: layout_id}).appendTo($form);
    $('<input>').attr({type: "hidden", name: "hlookat", value: hlookat}).appendTo($form);
    $('<input>').attr({type: "hidden", name: "vlookat", value: vlookat}).appendTo($form);
    $form.appendTo('body').submit();
}


export {
    createSurfaceState, updateSurfaceState,
};
