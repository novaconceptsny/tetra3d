import * as canvas from './artwork_assignment.js';

$(function (){
    $('.artwork-img').click(function (){
        let target = $(this).get(0);
        let newSelection = canvas.newArtworkSelection(target);
        canvas.placeSelectedImage(newSelection);
    })
})
