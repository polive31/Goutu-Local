jQuery(document).ready(function () {
    console.log('In tooltip.js');

    jQuery(document).on('click', '.tooltip .tooltip-target', function(e) {
        console.log('Click on click-triggered tooltip detected !');
        e.preventDefault();
        e.stopPropagation();
        
        var tooltip = jQuery(this).siblings('.tooltip-content.click');
        // console.log( tooltip.html() );
        openCloseTooltip( tooltip );
    });
    
});

function openCloseTooltip( tooltip ) {
    
    tooltip.toggle();
    if (tooltip.is(":visible")) {
        tooltip.siblings('.tooltip-content.hover').addClass('nohover');
        tooltip.siblings('.tooltip-content.hover').removeClass('hover');
    }
    else {
        tooltip.siblings('.tooltip-content.nohover').addClass('hover');
        tooltip.siblings('.tooltip-content.nohover').removeClass('nohover');
    }
};