jQuery(document).ready(function()   {

    var hintAlreadyRead="false";
    var didScroll = "no";
    var count = 0;

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
            didScroll = "stop";
        }
        else if (didScroll == "no" && count <= 4) {
            count++;
            // console.log( "count = " + count);
        }
    }, 250);




});

jQuery(window).on("beforeunload", function () {
    console.log( 'window unload detected ! Showing preloader ');
    jQuery(".preloader").show();
});

jQuery(window).load(function () {
    jQuery(".preloader").fadeOut(100);
});


function actionsOnScroll() {
    // Hide hint
    jQuery('.mobile-menu-hint-container').addClass('transparent');
}
