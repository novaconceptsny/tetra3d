class ArtSelection {
    constructor(title, imgUrl, artworkId, topPosition, leftPosition, cropData=null, overrideScale=null) {
        this.title = title;
        this.imgUrl = imgUrl;
        this.artworkId = artworkId;
        this.topPosition = topPosition;
        this.leftPosition = leftPosition;
        console.log("inside overrideScale", overrideScale);
        this.overrideScale = overrideScale;
        this.cropData = cropData;
        if (cropData=="null"){
            this.cropData = null;
        }
    }
    // gets
    getTitle() { return this.title; }
    getImgUrl() { return this.imgUrl }
    getArtworkId() { return this.artworkId; }
    getTopPosition() { return this.topPosition; }
    getLeftPosition() { return this.leftPosition; }
    getCropData() { return this.cropData; }
    // sets
    setTitle(title) { this.title = title; }
    setTopPosition(topPos) { this.topPosition = topPos; }
    setLeftPosition(leftPos) { this.leftPosition = leftPos; }
    setCropData(cropData) { this.cropData = cropData; }
    setImgUrl(imgUrl) { this.imgUrl = imgUrl; }
    setArtworkId(artworkId) { this.artworkId = artworkId; }
    setOverrideScale(overrideScale) { this.overrideScale = overrideScale; }
    toJSON() {
        return {
            title: this.title,
            imgUrl: this.imgUrl,
            artworkId: this.artworkId,
            leftPosition: this.leftPosition,
            topPosition: this.topPosition,
            cropData: this.cropData,
            overrideScale: this.overrideScale
        };
    }
}

export { ArtSelection };
