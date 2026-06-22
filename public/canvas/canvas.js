// Cache-busting: append ?v=<deploy version> (set by the page in window.__CANVAS_VER)
// so the browser fetches the latest module after a deploy instead of a stale cached copy.
// A static `import` URL can't carry the version, so we load it dynamically.
const __canvasVer = (typeof window !== 'undefined' && window.__CANVAS_VER) ? `?v=${window.__CANVAS_VER}` : '';
const { default: CanvasManager } = await import(`./CanvasManager.js${__canvasVer}`);

Object.keys(canvases).forEach(surfaceStateId => {
    let canvasData = canvases[surfaceStateId];
    let canvas = new CanvasManager(canvasData)

    if (canvasData.surfaceStateId){
        if (canvasData.surfaceStateId === selectedSurfaceStateId){
            canvas.active = true;
        }
    } else {
        canvas.active = true;
    }

});
