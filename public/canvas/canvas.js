import * as canvas from './artwork_assignment.js';

$(function (){
    $('.artwork-img').click(function (){
        let target = $(this).get(0);
        let newSelection = canvas.newArtworkSelection(target);
        try {
            canvas.placeSelectedImage(newSelection);
        } catch (e) {
            console.log(e);
        }
    })
})
