const mainContent = document.querySelector('.main_content');
const mainHeight = mainContent.offsetHeight;
const mainWidth = mainContent.offsetWidth;
const MAX_ARTWORK_DIMENSION = 200;

let srcPoints = [];

let warpedArtworkPosition = { x: 0, y: 0 };

let Minv = null;
let dstMat = null;
let M = null;
let isArtworkDragging = false;
let warpedArtwork = null; //

let dragOffset = { x: 0, y: 0 };
// let artworkPosition = { x: 0, y: 0 };

let dragTransformMatrix = null;
let lastMousePos = { x: 0, y: 0 };

// Add this variable at the top of your file
let lastDragOperation = null;

let saveAndReturnBtn = document.getElementById('save-and-return');
// Add this variable at the top of your file
let isAreaVisible = true;

function getId(item = "artwork") {
    return `${item}-${Math.random().toString(32).slice(-4)}`;
}

function calculatePolygonBounds(points) {
    const xs = points.map(p => p.x);
    const ys = points.map(p => p.y);
    const minX = Math.min(...xs);
    const maxX = Math.max(...xs);
    const minY = Math.min(...ys);
    const maxY = Math.max(...ys);
    return {
        width: maxX - minX,
        height: maxY - minY,
        x: minX,
        y: minY
    };
}

// Helper functions for better organization
function calculateScaledPoints(corners, boundingBoxLeft, boundingBoxTop, photoScale) {
    return corners.map(point => ({
        x: (point.x - boundingBoxLeft) * photoScale,
        y: (point.y - boundingBoxTop) * photoScale
    }));
}

function drawPolygon(ctx, points, fillStyle = 'rgba(0, 255, 0, 0.1)', strokeStyle = 'green') {
    if (!points.length || !isAreaVisible) return;

    ctx.beginPath();
    ctx.moveTo(points[0].x, points[0].y);

    points.slice(1).forEach(point => {
        ctx.lineTo(point.x, point.y);
    });

    ctx.closePath();
    ctx.fillStyle = fillStyle;
    ctx.fill();
    ctx.strokeStyle = strokeStyle;
    ctx.lineWidth = 2;
    ctx.stroke();
}

function getSelectionData(selectedElement) {
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

// Add these helper functions
function isPointInWarpedArtwork(x, y, assignedArtworks) {
    if (!M) return null;

    let clickPoint = null;
    let transformedPoint = null;

    try {
        // Create a point matrix for the click coordinates
        clickPoint = cv.matFromArray(1, 1, cv.CV_32FC2, [x, y]);
        transformedPoint = new cv.Mat();

        // Transform the point to warped space
        cv.perspectiveTransform(clickPoint, transformedPoint, M);

        // Get the transformed coordinates
        const tx = transformedPoint.data32F[0];
        const ty = transformedPoint.data32F[1];

        // Find the artwork that contains this point
        const foundArtwork = assignedArtworks.find(artwork => {
            return tx >= artwork.pos.x &&
                tx <= artwork.pos.x + artwork.artworkWidth &&
                ty >= artwork.pos.y &&
                ty <= artwork.pos.y + artwork.artworkHeight;
        });

        return foundArtwork || null;
    } catch (error) {
        console.error('Error in isPointInWarpedArtwork:', error);
        return null;
    } finally {
        // Clean up
        if (clickPoint) {
            clickPoint.delete();
        }
        if (transformedPoint) {
            transformedPoint.delete();
        }
    }
}



Object.entries(canvases).forEach(([surfaceStateId, canvasData]) => {
    const imageCanvas = document.getElementById(canvasData.canvasId);
    if (!imageCanvas) {
        console.warn(`Canvas with ID ${canvasData.canvasId} not found`);
        return;
    }

    const assignedArtworks = canvasData.assignedArtworks;

    const photoId = canvasData.photoId;
    console.log(assignedArtworks,photoId,  "assignedArtworks")
    let artworkLoaded = assignedArtworks.length > 0;
    const ctx = imageCanvas.getContext('2d');
    const surface = canvasData.surface;
    const updateEndpoint = canvasData.updateEndpoint;
    const surfaceData = surface.data;
    const photoScale = mainWidth / surfaceData.bounding_box_width;

    const img = new Image();
    img.src = surface.background_url;


    img.onload = () => {
        imageCanvas.width = mainWidth;
        imageCanvas.height = mainHeight;
        ctx.drawImage(img, 0, 0, mainWidth, mainHeight);

        srcPoints = calculateScaledPoints(
            surfaceData.corners,
            surfaceData.bounding_box_left,
            surfaceData.bounding_box_top,
            photoScale
        );

        drawPolygon(ctx, srcPoints);
        calculatePerspectiveTransform();
        renderAllArtworks();
    };

    registerArtworkSelectionEvent();

    img.onerror = () => {
        console.error(`Failed to load image: ${surface.background_url}`);
    };


    // Modify the existing imageCanvas click listener to this:
    imageCanvas.addEventListener('mousedown', function (evt) {
        if (!artworkLoaded) return;

        const rect = imageCanvas.getBoundingClientRect();
        const x = evt.clientX - rect.left;
        const y = evt.clientY - rect.top;

        // Check if we're clicking on a warped artwork
        const clickedArtwork = isPointInWarpedArtwork(x, y, assignedArtworks);
        if (clickedArtwork) {
            console.log("clicked on warped artwork:", clickedArtwork.id);
            isArtworkDragging = true;
            warpedArtwork = clickedArtwork;
            warpedArtworkPosition = clickedArtwork.pos;

            // Calculate offset between click point and artwork top-left corner
            let clickPoint = cv.matFromArray(1, 1, cv.CV_32FC2, [x, y]);
            let transformedClick = new cv.Mat();

            try {
                cv.perspectiveTransform(clickPoint, transformedClick, M);
                dragOffset = {
                    x: transformedClick.data32F[0] - warpedArtworkPosition.x,
                    y: transformedClick.data32F[1] - warpedArtworkPosition.y
                };
            } finally {
                clickPoint.delete();
                transformedClick.delete();
            }

            lastMousePos = { x, y };
            imageCanvas.style.cursor = 'move';
            return;
        }
    });

    // Modify the mousemove event handler
    imageCanvas.addEventListener('mousemove', function (evt) {
        if (!artworkLoaded) return;

        const rect = imageCanvas.getBoundingClientRect();
        const x = evt.clientX - rect.left;
        const y = evt.clientY - rect.top;

        // Handle artwork dragging
        if (isArtworkDragging && M) {
            if (lastDragOperation) {
                cancelAnimationFrame(lastDragOperation);
                lastDragOperation = null;
            }

            let srcPoint = null;
            let dstPoint = null;

            try {
                srcPoint = cv.matFromArray(1, 1, cv.CV_32FC2, [x, y]);
                dstPoint = new cv.Mat();

                if (!srcPoint.empty() && M && !M.empty()) {
                    cv.perspectiveTransform(srcPoint, dstPoint, M);

                    if (dstPoint && !dstPoint.empty()) {
                        const newX = dstPoint.data32F[0];
                        const newY = dstPoint.data32F[1];

                        if (!isNaN(newX) && !isNaN(newY)) {
                            // Update position accounting for the drag offset
                            warpedArtworkPosition.x = newX - dragOffset.x;
                            warpedArtworkPosition.y = newY - dragOffset.y;

                            assignedArtworks.map(artwork => {
                                if (artwork.id === warpedArtwork.id) {
                                    artwork.pos.x = warpedArtworkPosition.x;
                                    artwork.pos.y = warpedArtworkPosition.y;
                                }
                            });

                            lastDragOperation = requestAnimationFrame(() => {
                                try {
                                    updateTransformedArtwork(warpedArtwork);
                                } catch (error) {
                                    console.error('Error in animation frame:', error);
                                }
                            });
                        }
                    }
                }

                lastMousePos = { x, y };
            } catch (error) {
                console.error('Error during drag operation:', error);
            } finally {
                if (srcPoint && !srcPoint.deleted) srcPoint.delete();
                if (dstPoint && !dstPoint.deleted) dstPoint.delete();
            }
            return;
        }


        // Change cursor style based on hover and drag state
        if (isArtworkDragging) {
            imageCanvas.style.cursor = 'move';
        } else {
            imageCanvas.style.cursor = 'default';
        }

    });

    // Update mouseup event handler
    imageCanvas.addEventListener('mouseup', function () {
        if (lastDragOperation) {
            cancelAnimationFrame(lastDragOperation);
            lastDragOperation = null;
        }

        isArtworkDragging = false;
        if (warpedArtwork) {
            updateTransformedArtwork(warpedArtwork);
            warpedArtwork = null;
        }
        imageCanvas.style.cursor = 'default';
    });

    // // Update mouseleave handler
    // imageCanvas.addEventListener('mouseleave', function () {
    //     if (lastDragOperation) {
    //         cancelAnimationFrame(lastDragOperation);
    //         lastDragOperation = null;
    //     }

    //     if (isArtworkDragging) {
    //         renderAllArtworks();
    //     }

    //     isArtworkDragging = false;
    //     imageCanvas.style.cursor = 'default';
    // });


    // Add this event listener after your other initialization code
    document.getElementById('toggle-area').addEventListener('click', function () {
        isAreaVisible = !isAreaVisible;
        const button = this;

        // Update button text and icon
        if (isAreaVisible) {
            button.innerHTML = '<i class="fal fa-eye"></i> Hide Area';
        } else {
            button.innerHTML = '<i class="fal fa-eye"></i> Show Area';
        }

        // Redraw the canvas to reflect the change
        renderAllArtworks();
    });




    function calculatePerspectiveTransform() {
        // Get real wall dimensions in meters
        const realWidth = calculatePolygonBounds(srcPoints).width;
        const realHeight = calculatePolygonBounds(srcPoints).height;

        // Add padding to dimensions to help with edge artifacts
        const paddingFactor = 1.02; // 2% padding
        const paddedWidth = Math.ceil(realWidth * paddingFactor);
        const paddedHeight = Math.ceil(realHeight * paddingFactor);

        // Clean up any existing matrices
        if (dstMat) dstMat.delete();
        if (Minv) Minv.delete();
        if (M) M.delete();

        // Create new matrices
        let srcMat = cv.imread(imageCanvas);
        dstMat = new cv.Mat();
        let dsize = new cv.Size(paddedWidth, paddedHeight);

        // Add padding to source points to reduce edge artifacts
        const padding = 2; // 2 pixels padding
        let srcTri = cv.matFromArray(4, 1, cv.CV_32FC2, [
            srcPoints[0].x - padding, srcPoints[0].y - padding,
            srcPoints[1].x + padding, srcPoints[1].y - padding,
            srcPoints[2].x + padding, srcPoints[2].y + padding,
            srcPoints[3].x - padding, srcPoints[3].y + padding
        ]);

        let dstTri = cv.matFromArray(4, 1, cv.CV_32FC2, [
            0, 0,
            paddedWidth, 0,
            paddedWidth, paddedHeight,
            0, paddedHeight
        ]);

        // Store matrices globally
        M = cv.getPerspectiveTransform(srcTri, dstTri);
        Minv = cv.getPerspectiveTransform(dstTri, srcTri);
        // Free memory
        srcMat.delete();
        srcTri.delete();
        dstTri.delete();
    }

    function renderAllArtworks() {
        // Clear canvas and draw background
        ctx.globalCompositeOperation = 'source-over';
        ctx.clearRect(0, 0, imageCanvas.width, imageCanvas.height);
        ctx.drawImage(img, 0, 0, imageCanvas.width, imageCanvas.height);
        drawPolygon(ctx, srcPoints);

        // Render each artwork
        assignedArtworks.forEach(artwork => {
            updateTransformedArtwork(artwork);
        });

    }

    function updateTransformedArtwork(imgData) {

        const { id, pos, imgUrl } = imgData;

        try {
            // Create temporary matrices for the transformation
            let artworkPoints = null;
            let transformedArtworkPoints = null;

            // Add padding to artwork corners to reduce edge artifacts
            const padding = 2; // 2 pixels padding

            // First, restore the original background image with a clean state
            ctx.globalCompositeOperation = 'source-over';


            let artworkCanvas = document.createElement('canvas');
            let artworkCtx = artworkCanvas.getContext('2d');
            let artworkImg = new Image();

            artworkImg.src = imgUrl;

            artworkImg.onload = function () {
                // Calculate scale to fit within MAX_ARTWORK_DIMENSION while maintaining aspect ratio
                const scaleW = MAX_ARTWORK_DIMENSION / artworkImg.width;
                const scaleH = MAX_ARTWORK_DIMENSION / artworkImg.height;
                let artworkScale = Math.min(scaleW, scaleH, 1.0); // Don't scale up, only down

                // Set canvas size to scaled dimensions
                artworkCanvas.width = artworkImg.width * artworkScale;
                artworkCanvas.height = artworkImg.height * artworkScale;

                // Clear and draw scaled image
                artworkCtx.clearRect(0, 0, artworkCanvas.width, artworkCanvas.height);
                artworkCtx.save();
                artworkCtx.scale(artworkScale, artworkScale);
                artworkCtx.drawImage(artworkImg, 0, 0);
                artworkCtx.restore();

                const artworkCorners = [
                    { x: pos.x - padding, y: pos.y - padding },
                    { x: pos.x + artworkCanvas.width + padding, y: pos.y - padding },
                    { x: pos.x + artworkCanvas.width + padding, y: pos.y + artworkCanvas.height + padding },
                    { x: pos.x - padding, y: pos.y + artworkCanvas.height + padding }
                ];

                // Create matrices
                artworkPoints = cv.matFromArray(4, 1, cv.CV_32FC2, [
                    artworkCorners[0].x, artworkCorners[0].y,
                    artworkCorners[1].x, artworkCorners[1].y,
                    artworkCorners[2].x, artworkCorners[2].y,
                    artworkCorners[3].x, artworkCorners[3].y
                ]);

                transformedArtworkPoints = new cv.Mat();
                // Transform points
                cv.perspectiveTransform(artworkPoints, transformedArtworkPoints, Minv);

                // Draw the warped artwork
                if (transformedArtworkPoints && transformedArtworkPoints.rows > 0) {
                    const points = transformedArtworkPoints.data32F;

                    // Create matrices for the artwork transformation with padding
                    let artworkSrcPoints = cv.matFromArray(4, 1, cv.CV_32FC2, [
                        -padding, -padding,
                        artworkCanvas.width + padding, -padding,
                        artworkCanvas.width + padding, artworkCanvas.height + padding,
                        -padding, artworkCanvas.height + padding
                    ]);

                    let artworkDstPoints = cv.matFromArray(4, 1, cv.CV_32FC2, [
                        points[0], points[1],
                        points[2], points[3],
                        points[4], points[5],
                        points[6], points[7]
                    ]);

                    // Create transformation matrix for the artwork
                    let artworkTransformMatrix = cv.getPerspectiveTransform(artworkSrcPoints, artworkDstPoints);

                    let artworkDstMat = new cv.Mat();

                    // Create size object for the destination with padding
                    let dsize = new cv.Size(imageCanvas.width + padding * 2, imageCanvas.height + padding * 2);

                    // Create a temporary canvas for the artwork with transparency and padding
                    let tempCanvas = document.createElement('canvas');
                    tempCanvas.width = imageCanvas.width + padding * 2;
                    tempCanvas.height = imageCanvas.height + padding * 2;
                    let tempCtx = tempCanvas.getContext('2d');

                    // Clear temp canvas with transparency
                    tempCtx.clearRect(0, 0, tempCanvas.width, tempCanvas.height);
                    tempCtx.globalAlpha = 1.0;

                    // Draw artwork onto the temp canvas with padding
                    tempCtx.drawImage(artworkCanvas, padding, padding);

                    // Convert the temp canvas to an OpenCV matrix
                    let tempMat = cv.imread(tempCanvas);

                    // Perform the perspective warp with improved settings
                    cv.warpPerspective(
                        tempMat,
                        artworkDstMat,
                        artworkTransformMatrix,
                        dsize,
                        cv.INTER_CUBIC, // Use cubic interpolation
                        cv.BORDER_REPLICATE, // Replicate edge pixels
                        new cv.Scalar(0, 0, 0, 0)
                    );

                    // Create another temporary canvas for the final composition
                    let finalTempCanvas = document.createElement('canvas');
                    finalTempCanvas.width = imageCanvas.width;
                    finalTempCanvas.height = imageCanvas.height;
                    let finalTempCtx = finalTempCanvas.getContext('2d');

                    // Clear the final temp canvas
                    finalTempCtx.clearRect(0, 0, finalTempCanvas.width, finalTempCanvas.height);

                    // Show the warped artwork on the temp canvas
                    cv.imshow(finalTempCanvas, artworkDstMat);

                    // Draw the composite with proper alpha blending
                    ctx.globalCompositeOperation = 'source-over';
                    if (isArtworkDragging) {
                        ctx.clearRect(0, 0, imageCanvas.width, imageCanvas.height);
                        ctx.drawImage(img, 0, 0, imageCanvas.width, imageCanvas.height);
                        drawPolygon(ctx, srcPoints);

                        assignedArtworks.map(artwork => {
                            if (artwork.id !== id) {
                                ctx.drawImage(artwork.finalTempCanvas, -padding, -padding);
                            }
                        });
                    }
                    ctx.drawImage(finalTempCanvas, -padding, -padding);

                    assignedArtworks.map(artwork => {
                        if (artwork.id === id) {
                            artwork.artworkWidth = artworkCanvas.width;
                            artwork.artworkHeight = artworkCanvas.height;
                            artwork.finalTempCanvas = finalTempCanvas;
                        }
                    });

                    // Clean up temporary canvases
                    finalTempCanvas.remove();
                    tempCanvas.remove();

                    // Update stored position for original canvas
                    // artworkPosition = {
                    //     x: transformedArtworkPoints.data32F[0],
                    //     y: transformedArtworkPoints.data32F[1]
                    // };

                    // Clean up OpenCV resources
                    artworkTransformMatrix.delete();
                    tempMat.delete();
                    artworkDstMat.delete();
                }

                // Clean up matrices
                if (artworkPoints) artworkPoints.delete();
                if (transformedArtworkPoints) transformedArtworkPoints.delete();
            }

        } catch (error) {
            console.error('Error in updateTransformedArtwork:', error);
        }
    }

    function registerArtworkSelectionEvent() {
        $('#site__body').on('click', '.artwork-img', (el) => {
            let target = el.currentTarget;
            let imgData = getSelectionData(target);
            artworkLoaded = true;

            const newArtwork = {
                id: getId("artwork"), // Generate a unique UUID
                pos: { x: 0, y: 0 },
                ...imgData,
            };
            assignedArtworks.push(newArtwork);

            updateTransformedArtwork(newArtwork);
        })
    }

    saveAndReturnBtn.addEventListener('click', function () {
        let payload = {
            "_token": document.querySelector('meta[name="csrf-token"]').content,
            "photoId": photoId,
            "assigned_artwork": assignedArtworks,
        };
        payload.assigned_artwork = JSON.stringify(payload.assigned_artwork);

        // Replace fakeFormPost with fetch
        fetch(updateEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': payload._token
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to save changes');
        });
    });

});

