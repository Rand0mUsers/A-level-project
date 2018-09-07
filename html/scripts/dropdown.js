// Code that controls the dropdown menu functionality

// when the document loads, bind a click handler to slide toggle the next element
$(document).ready(function() {
    $('.drop').click(function() {
        $(this).next().slideToggle(500);
    });
});