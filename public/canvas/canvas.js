import CanvasManager from "./CanvasManager.js";

Object.keys(canvases).forEach(surfaceStateId => {
    let canvasData = canvases[surfaceStateId];
    let canvas = new CanvasManager(canvasData)

    if (canvasData.surfaceStateId){
        if (canvasData.surfaceStateId === surfaceStateId){
            canvas.active = true;
        }
    } else {
        canvas.active = true;
    }

});
