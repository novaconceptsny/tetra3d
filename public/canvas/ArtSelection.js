class ArtSelection {
    constructor(imgData, topPosition, leftPosition, cropData = null, overrideScale = null) {
        this.title = imgData['title'] ?? null;
        this.imgUrl = imgData['imgUrl'] ?? null;
        this.artworkId = imgData['artworkId'] ?? null;
        this.scale = imgData['scale'] ?? null;
        this.topPosition = topPosition;
        this.leftPosition = leftPosition;
        this.overrideScale = overrideScale;
        this.cropData = cropData;
        if (cropData === "null") {
            this.cropData = null;
        }
    }

    // gets
    getTitle() {
        return this.title;
    }

    getImgUrl() {
        return this.imgUrl
    }

    getArtworkId() {
        return this.artworkId;
    }

    getTopPosition() {
        return this.topPosition;
    }

    getLeftPosition() {
        return this.leftPosition;
    }

    getCropData() {
        return this.cropData;
    }

    getScale() {
        return this.scale;
    }

    // sets
    setTitle(title) {
        this.title = title;
    }

    setScale(scale) {
        this.scale = scale;
    }

    setTopPosition(topPos) {
        this.topPosition = topPos;
    }

    setLeftPosition(leftPos) {
        this.leftPosition = leftPos;
    }

    setCropData(cropData) {
        this.cropData = cropData;
    }

    setImgUrl(imgUrl) {
        this.imgUrl = imgUrl;
    }

    setArtworkId(artworkId) {
        this.artworkId = artworkId;
    }

    setOverrideScale(overrideScale) {
        this.overrideScale = overrideScale;
    }

    toJSON() {
        return {
            title: this.title,
            imgUrl: this.imgUrl,
            artworkId: this.artworkId,
            scale: this.scale,
            leftPosition: this.leftPosition,
            topPosition: this.topPosition,
            cropData: this.cropData,
            overrideScale: this.overrideScale
        };
    }
}

export default ArtSelection;
