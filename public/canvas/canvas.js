import * as canvas from './artwork_assignment.js';

$(function (){
    $('#site__body').on('click', '.artwork-img',function (){
        let target = $(this).get(0);
        let newSelection = canvas.newArtworkSelection(target);
        canvas.placeSelectedImage(newSelection);
    })
})
