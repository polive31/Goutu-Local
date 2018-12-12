jQuery(document).ready(function()   {
    // window.initDone = false;
    var hintAlreadyRead="false";
    setContainerTopMargin( true );
    
    hintAlreadyRead=getCookie('menuHint');
    if (hintAlreadyRead=="" || hintAlreadyRead=="false") {
        setCookie('menuHint','true',30);
        // console.log("Set Cookie to true and remove class nodisplay");
        jQuery('.mobile-menu-hint-container').removeClass('nodisplay');       
    }
    // else {
    //     console.log("Cookie=True => do not show hint");
    
    // }
    // window.initDone = true;
    // console.log( "Init Done = " + window.initDone );
});

jQuery(document).ready(function()   {

    // if ( jQuery( "body" ).hasClass( "home-page" ) ) {
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

    // }
});

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}


function actionsOnScroll() {
    // Resize header
    jQuery('header').addClass('nav-up');
    // Hide hint
    jQuery('.mobile-menu-hint-container').addClass('transparent');
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
