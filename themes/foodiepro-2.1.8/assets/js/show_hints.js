jQuery(document).ready(function()   {

    // jQuery(document).ajaxStop(function () {
        // sidebarHeightAdjustToContent();
    // });


    /*  Minify header on scroll
    -------------------------------------*/
    var hintAlreadyRead="false";
    var didScroll = "no";
    var count = 0;

    setContainerTopMargin( true );
    hintAlreadyRead=foodieproGetCookie('menuHint');
    if (hintAlreadyRead=="" || hintAlreadyRead=="false") {
        foodieproSetCookie('menuHint','true',30);
        // console.log("Set Cookie to true and remove class nodisplay");
        jQuery('.mobile-menu-hint-container').removeClass('nodisplay');
    }
    // else console.log("Cookie=True => do not show hint");

    jQuery(window).scroll(function(event){
        // console.log( "Init Done in scroll function = " + window.initDone );
        if (didScroll == "no" && count > 4) {
            didScroll = "yes";
            // console.log( "SCROLLED !");
        }
    });

    setInterval(function() {
        if (didScroll == "yes" ) {
            // ****** Trigger the resize Header function !
            actionsOnScroll();
            // *******************************************
            didScroll = "last";
        }
        else if (didScroll == "last") {
            // Allows for the css transition to be completed before computing the margin again
            setContainerTopMargin();
            didScroll = "stop";
        }
        else if (didScroll == "no" && count <= 4) {
            count++;
            // console.log( "count = " + count);
        }
    }, 250);

});



function actionsOnScroll() {
    // Hide hint
    jQuery('.mobile-menu-hint-container').addClass('transparent');
}
