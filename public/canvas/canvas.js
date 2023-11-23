import CanvasManager from "./CanvasManager.js";

Object.keys(canvases).forEach(canvasId => {
    let canvas = new CanvasManager(canvases[canvasId])
    if (canvasId == '866'){
        canvas.active = true;
    }

});
