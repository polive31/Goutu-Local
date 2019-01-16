var postContainer = "article";
var containerObj = jQuery(postContainer);
var overlayObj = jQuery('.tooltip-overlay');


jQuery(document).ready(function () {
    console.log('In tooltip.js');
    
    initContainer();
    
    jQuery(postContainer).on('click', '.tooltip-target', function(e) {
        console.log('Click on tooltip target');
        // console.log('Container Object is : ', containerObj );
        e.preventDefault();
        e.stopPropagation(); 
        var tooltip = jQuery(this).siblings('.tooltip-content.click');
        // console.log( tooltip.html() );
        toggleTooltip(tooltip );
    }); 
    
    jQuery(document).click( function(e) {
        console.log('click there : ', e.target );
        console.log('Closest tooltip-content', e.target.closest('.tooltip-content') );
        console.log('Closest tooltip-content visibility ?', jQuery(e.target).closest('.tooltip-content').length );
        
        if ( jQuery(e.target).closest('.tooltip-content').length == 0 ) {
            console.log('click outside of a tooltip  area');
            closeAllTooltips();            
        }
    });
});

function initContainer() {
    console.log('In initContainer function');
    if (jQuery('.tooltip-container')) {
        containerObj = jQuery('.tooltip-container');
        console.log('Container changed to ', containerObj );
    }
    containerObj.addClass('tooltips-closed');
};

function toggleTooltip(tooltip) {
    console.log( 'In toggle Tooltip' );
    if ( aTooltipOpen() && !thisTooltipOpen(tooltip) ) {
        console.log( 'Other open tooltip detected, closing' );
        closeAllTooltips();
    }
    else {
        console.log( 'Toggle tooltip' );
        tooltip.toggle();
        if (tooltip.hasClass('modal')) overlayToggle();
        containerObj.toggleClass('tooltip-open tooltips-closed');
    }
};

function closeAllTooltips() {
    console.log( 'In Close All Tooltips' );
    containerObj.find('.tooltip-content.click:visible').hide();
    containerObj.addClass('tooltips-closed');
    containerObj.removeClass('tooltip-open');
    overlayClose();
}

function aTooltipOpen() {
    console.log('A tooltip is open ?', containerObj.hasClass('tooltip-open') );
    return containerObj.hasClass('tooltip-open');
}

function thisTooltipOpen(tooltip) {
    console.log('This tooltip is open ?', tooltip.is(":visible") );
    return tooltip.is(":visible");
}

function overlayToggle() {
    if (overlayObj.hasClass('on')) {
        overlayClose();
    } else {
        overlayOpen();
    }
}

function overlayOpen() {
    console.log('overlay open');
    overlayObj.removeClass('nodisplay');
    setTimeout(function() {
        overlayObj.addClass('on');
    }, 100);
}

function overlayClose() {
    console.log('overlay close');
    overlayObj.removeClass('on');
    overlayObj.one('transitionend', function() {
        overlayObj.addClass('nodisplay');
    })
}