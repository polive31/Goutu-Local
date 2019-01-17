jQuery(document).ready(function () {
    console.log('In tooltip.js');
    
    Tooltip.initContainer();
    
    jQuery(Tooltip.containerObj).on('click', '.tooltip-onclick', function(e) {
        console.log('Click on tooltip .onclick');
        // console.log('Container Object is : ', containerObj );
        e.preventDefault();
        e.stopPropagation(); 
        // var tooltip = jQuery(this).siblings('.tooltip-content.click');
        var tooltip = new Tooltip( jQuery(this).siblings('.tooltip-content.click') );

        // console.log( tooltip.html() );
        // Tooltip.toggleVisibility(tooltip );
        tooltip.togglePopup();
    }); 
    
    jQuery(document).click( function(e) {
        console.log('click there : ', e.target );
        console.log('Closest tooltip-content', e.target.closest('.tooltip-content') );
        console.log('Closest tooltip-content visibility ?', jQuery(e.target).closest('.tooltip-content').length );
        
        if ( jQuery(e.target).closest('.tooltip-content').length == 0 ) {
            console.log('click outside of a tooltip  area');
            Tooltip.closeAll();            
        }
    });
    
});


class Tooltip {

    constructor( obj ) {
        // Pass the jquery object into the class, then all jQuery methods can be applied to it
        this.obj = obj;
    }

    static initContainer() {
        console.log('In initContainer function');
        // if (jQuery('.tooltips-container')) {
        console.log('Initial Tooltips Container value ', Tooltip.containerObj );
        var containerMaybe = jQuery(Tooltip.containerClass);
        if (containerMaybe) {
            Tooltip.containerObj = containerMaybe;
            console.log('Tooltips Container changed to ', Tooltip.containerObj );
        }
        Tooltip.containerObj.addClass('tooltips-closed');
    };
    
    togglePopup() {
        console.log('In toggle Popup !, for jQuery object ', this.obj );
        // console.log('In toggle Popup !, for object ', this);
        // console.log('The corresponding jQuery object is : ', this.obj );
        if (Tooltip.aTooltipOpen() && !this.isOpen()) {
            console.log('Other open tooltip detected, closing');
            Tooltip.closeAll();
        }
        else {
            console.log('OK for toggling the current tooltip');
            this.obj.toggle();
            if (this.obj.hasClass('modal')) Tooltip.overlayToggle();
            Tooltip.containerObj.toggleClass('tooltip-open tooltips-closed');
        }
    };
    
    isOpen() {
        console.log('This tooltip is open ?', this.obj.is(":visible"));
        return this.obj.is(":visible");
    }
    
    static closeAll() {
        console.log( 'In Close All Tooltips' );
        Tooltip.containerObj.find('.tooltip-content.click:visible').hide();
        Tooltip.containerObj.addClass('tooltips-closed');
        Tooltip.containerObj.removeClass('tooltip-open');
        Tooltip.overlayClose();
    }

    static overlayToggle() {
        if (Tooltip.overlayObj.hasClass('on')) {
            Tooltip.overlayClose();
        } else {
            Tooltip.overlayOpen();
        }
    }
    
    static overlayOpen() {
        console.log('overlay open');
        Tooltip.overlayObj.removeClass('nodisplay');
        Tooltip.overlayObj.one('transitionend', function () {
            console.log('Entering in transitioned event in overlay Open !');
        });
        setTimeout(function() {
            Tooltip.overlayObj.addClass('on');
        }, 50);
    }
    
    static overlayClose() {
        console.log('overlay close');
        Tooltip.overlayObj.removeClass('on');
        Tooltip.overlayObj.one('transitionend', function() {
            console.log('Entering in transitioned event, overlay Close !');
            // Check if event is triggered on overlayClose -workaround unwanted trigger bug
            if (!Tooltip.overlayObj.hasClass('on')) {
                console.log('Add class nodisplay');
                Tooltip.overlayObj.addClass('nodisplay');
            } 
        });
        
    }
    
    static aTooltipOpen() {
        console.log('A tooltip is open ?', Tooltip.containerObj.hasClass('tooltip-open') );
        return Tooltip.containerObj.hasClass('tooltip-open');
    }

    

}

    
Tooltip.defaultContainer = "article";
// Class to search for in order to define the container for all tooltips on the page
// Reverts to the one defined above by default
Tooltip.containerClass = ".tooltips-container";
Tooltip.containerObj = jQuery(Tooltip.defaultContainer);
Tooltip.overlayObj = jQuery('.tooltip-overlay');