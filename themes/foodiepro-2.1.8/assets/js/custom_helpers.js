jQuery(document).ready(function()   {
    // window.initDone = false;
    setContainerTopMargin( true );
    // window.initDone = true;
    // console.log( "Init Done = " + window.initDone );
});

jQuery(document).ready(function()   {

    if ( jQuery( "body" ).hasClass( "home-page" ) ) {
        // console.log( "IS HOME PAGE !");
        var didScroll = "no";
        var count = 0;

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
                resizeHeader();
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

    }
});

function resizeHeader() {
    jQuery('header').addClass('nav-up');
}

function setContainerTopMargin( scrollTop ) {
    var height = jQuery( "header" ).outerHeight();
    var htmlMargin = parseInt(jQuery("html").css("margin-top"));
    topMargin = height - htmlMargin + 10;
    // console.log( "height = " + height )
    // console.log( "htmlMargin = " + htmlMargin )
    // console.log( "topMargin = " + topMargin )
    if ( scrollTop ) {
        jQuery(window).scrollTop(0);
        // console.log( "Scroll Position = " + jQuery(window).scrollTop() );
    }    
    jQuery(".site-container").css("margin-top", topMargin);

}
