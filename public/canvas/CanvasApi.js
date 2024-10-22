class CanvasApi {
    constructor(data) {
        this.updateEndpoint = data.updateEndpoint;
        this.layoutId = data.layoutId;
        this.hlookat = data.hlookat;
        this.vlookat = data.vlookat;
    }

    async createSurfaceState(data) {
        let canvasState = JSON.parse(JSON.stringify(data.canvasState));
        canvasState.savedVersion = true;

        let payload = {
            "name": data.filename,
            "assigned_artwork": data.assignedArtwork,
            "thumbnail": data.screenshots['thumbnail'],
            "hotspot": data.screenshots['hotspot'],
            "canvasState": JSON.stringify(canvasState),
            "reverseScale": data.reverseScale,
            "user_id": data.userId,
            "spot_id": data.spot_id,
            "new": true,
        };

        payload.assigned_artwork = JSON.stringify(payload.assigned_artwork);

        this.fakeFormPost(this.updateEndpoint, payload);

    }

    updateSurfaceState(data) {
        let updates = data.updates;
        let screenshots = data.screenshots;

        let payload = {
            "surface_state_id": data.surfaceStateId,
            "assigned_artwork": updates['assignedArtwork'],
            "added": updates["added"],
            "removed": updates["removed"],
            "modified": updates["modified"],
            "thumbnail": screenshots['thumbnail'],
            "hotspot": screenshots['hotspot'],
            "canvasState": JSON.stringify(data.canvasState),
            "reverseScale": data.reverseScale,
            "userId": data.userId,
            "spot_id": data.spotId,
        };
        payload.assigned_artwork = JSON.stringify(payload.assigned_artwork);
        payload.added = JSON.stringify(payload.added);
        payload.removed = JSON.stringify(payload.removed);
        this.updateSurfaceStatePost(this.updateEndpoint, payload);
    }

    fakeFormPost(endpoint, payload) {

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
        $('<input>').attr({ type: "hidden", name: "_token", value: token }).appendTo($form);
        $('<input>').attr({ type: "hidden", name: "layout_id", value: this.layoutId }).appendTo($form);
        $('<input>').attr({ type: "hidden", name: "hlookat", value: this.hlookat }).appendTo($form);
        $('<input>').attr({ type: "hidden", name: "vlookat", value: this.vlookat }).appendTo($form);
        $form.appendTo('body').submit();
    }

    updateSurfaceStatePost(endpoint, payload) {

        let token = $('meta[name="_token"]').attr('content');

        $.ajax({
            url: endpoint,
            type: 'POST',
            data: {
                ...payload,
                _token: token,
                layout_id: this.layoutId,
                hlookat: this.hlookat,
                vlookat: this.vlookat,
            },
            success: function (data) {
                console.log(data);
            },
            error: function (data) {
                console.log(data);
            }
        })
    }
}

export default CanvasApi;
