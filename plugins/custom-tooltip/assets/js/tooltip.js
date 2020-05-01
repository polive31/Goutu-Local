/* TOOLTIP.JS

Tooltips have the following structure :
* trigger  : this can be any html element which will cause a tooltip to be displayed
* action : hover, click, [hover intent ?]
* content : contains the tooltip's body

Tooltip class has the following methods :
* For a tooltip instance :
    - toggleVisibility => displays or hide
    - isOpen => check if the tooltip is open
    - updateContent => updates the content of the tooltip
    - updateTrigger => updates the content of the trigger : change an icon, ...

* For all tooltips (static methods) :
    - anyOpen => checks whether any tooltip is opened
    - closeAll => closes all tooltips
*/

jQuery(document).ready(function () {
    console.log('%c In tooltip.js ', "background:#f18b14; color:white");

    Tooltip.initContainer();

    jQuery(Tooltip.containerObj).on('click', '.tooltip-onclick', function(e) {
        console.log('%c Click on tooltip .onclick', "background:#f18b14; color:white");
        console.log('%c Tooltip trigger object is : ', jQuery(this), "background:#f18b14; color:white" );

        e.preventDefault();
        e.stopPropagation();

        var tooltip = new Tooltip( this );
        tooltip.toggleVisibility();

    });

    jQuery(document).click( function(e) {
        //console.log('%c click there : ', e.target );
        //console.log('%c Closest tooltip-content', e.target.closest('.tooltip-content') );
        //console.log('%c Closest tooltip-content visibility ?', jQuery(e.target).closest('.tooltip-content').length );

        if ( jQuery(e.target).closest('.tooltip-content').length == 0 ) {
            //console.log('%c click outside of a tooltip  area');
            Tooltip.closeAll();
        }
    });

});


class Tooltip {
/* See constants under the class definition */

    constructor( trigger ) {
        // Pass the jquery object into the class, then all jQuery methods can be applied to it
        this.trigger = jQuery(trigger);
        console.log('%c In constructor, trigger is ', "background:#f18b14; color:white", this.trigger);

        var contentId = this.trigger.data("tooltip-id");
        console.log('%c In constructor, tooltip container ID is ', "background:#f18b14; color:white", contentId);

        if (contentId) {
            this.content = jQuery('#'+contentId);
        }
        else {
            this.content = this.trigger.siblings('.tooltip-content.click');
        }
        // //console.log('%c In constructor, this.content is ', this.content);
    }

    static initContainer() {
        // //console.log('%c In initContainer function');
        // if (jQuery('.tooltips-container')) {
        // //console.log('%c Initial Tooltips Container value ', Tooltip.containerObj );
        // var containerMaybe = jQuery(Tooltip.containerClass);
        // if (containerMaybe) {
        //     Tooltip.containerObj = containerMaybe;
        //     //console.log('%c Tooltips Container changed to ', Tooltip.containerObj );
        // }
        Tooltip.containerObj.addClass('tooltips-closed');
    };

    toggleVisibility() {
        //console.log('%c In toggle Visibility !, for jQuery object ', this.content );
        // //console.log('%c In toggle Popup !, for object ', this);
        // //console.log('%c The corresponding jQuery object is : ', this.content );
        if (Tooltip.anyOpen() && !this.isOpen()) {
            //console.log('%c Other open tooltip detected, closing');
            Tooltip.closeAll();
        }
        else {
            //console.log('%c OK for toggling the current tooltip');
            this.content.toggle();
            if (this.content.hasClass('modal')) Tooltip.overlayToggle();
            Tooltip.containerObj.toggleClass('tooltip-open tooltips-closed');
        }
    };

    isOpen() {
        //console.log('%c This tooltip is open ?', this.content.is(":visible"));
        return this.content.is(":visible");
    }

    setContent() {

    }

    static closeAll() {
        //console.log( 'In Close All Tooltips' );
        //console.log('%c Following open popups are found : ', Tooltip.containerObj.find('.tooltip-content.click:visible') );
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
        //console.log('%c overlay open');
        Tooltip.overlayObj.removeClass('nodisplay');
        Tooltip.overlayObj.one('transitionend', function () {
            //console.log('%c Entering in transitioned event in overlay Open !');
        });
        setTimeout(function() {
            Tooltip.overlayObj.addClass('on');
        }, 50);
    }

    static overlayClose() {
        //console.log('%c overlay close');
        Tooltip.overlayObj.removeClass('on');
        Tooltip.overlayObj.one('transitionend', function() {
            //console.log('%c Entering in transitioned event, overlay Close !');
            // Check if event is triggered on overlayClose -workaround unwanted trigger bug
            if (!Tooltip.overlayObj.hasClass('on')) {
                //console.log('%c Add class nodisplay');
                Tooltip.overlayObj.addClass('nodisplay');
            }
        });

    }

    static anyOpen() {
        //console.log('%c A tooltip is open ?', Tooltip.containerObj.hasClass('tooltip-open') );
        return Tooltip.containerObj.hasClass('tooltip-open');
    }

}


Tooltip.defaultContainer = "body";
Tooltip.containerObj = jQuery(Tooltip.defaultContainer);
// Class to search for in order to define the container for all tooltips on the page
// Reverts to the one defined above by default
// Tooltip.containerClass = ".tooltips-container";
Tooltip.overlayObj = jQuery('.tooltip-overlay');
