var tts = { step: 0, thisBullet: jQuery(''), playing: false, lastStep:0};

jQuery(document).ready(function () {

    tts.lastStep = jQuery('li.wpurp-recipe-instruction').length-1;

    console.log("In custom text to speech script");

    jQuery(document).on("click", ".recipe-button#read", function (e) {
        e.preventDefault();
        e.stopPropagation();
        console.log( "Click on recipe read !");
        tts.openReader();
    });

    jQuery(document).on("click", ".recipe-instruction-bullet", function (e) {
        e.preventDefault();
        e.stopPropagation();
        console.log("Click on instruction bullet !");
        tts.step = jQuery(this).attr("id").match(/\d+$/);
        tts.openReader();
    });    

    jQuery(document).on("click", "#custom-reader-repeat", function (e) {
        e.preventDefault();
        e.stopPropagation();
        console.log("Click on PLAY");
        tts.readStep();
    });   

    jQuery(document).on("click", "#custom-reader-pause, #custom-reader-play", function (e) {
        e.preventDefault();
        e.stopPropagation();
        console.log("Click on PAUSE");
        tts.toggleStep();
    });      
    
    jQuery(document).on("click", "#custom-reader-prev", function (e) {
        e.preventDefault();
        e.stopPropagation();
        console.log("Click on PREV");
        if (tts.step > 0) tts.step--;
        console.log("tts.step = " + tts.step );
        tts.readStep();
    }); 
    
    jQuery(document).on("click", "#custom-reader-next", function (e) {
        e.preventDefault();
        e.stopPropagation();
        console.log("Click on NEXT, lastStep = " + tts.lastStep );
        if (tts.step < tts.lastStep) tts.step++;
        console.log("tts.step = " + tts.step);
        tts.readStep();
    });  
    
    // jQuery(document).on("beforeunload", function() {
    jQuery(window).unload(function () {       
        console.log("Before unload, click on stop");
        jQuery("#custom-reader-stop").click();
    });

    jQuery(document).on("click", "#custom-reader-stop", function (e) {
        e.preventDefault();
        e.stopPropagation();
        console.log("Click on STOP");
        tts.step = 0;
        console.log("tts.step = " + tts.step);
        tts.closeReader();
    });    
    
    jQuery(document).on("click", "#responsive-menu-pro-button", function (e) {
        e.preventDefault();
        e.stopPropagation();
        console.log("Click on Menu Toggle !!!!");
        tts.step = 0;
        console.log("tts.step = " + tts.step);
        tts.closeReader();
    });    
    
});


tts.openReader = function () {
    console.log( "In openReader function");
    var reader = jQuery("#custom-reader-container");
    
    if ( !reader.length ) {
        reader = jQuery('<div class="wrapper"></div>'),
        play = jQuery('<span id="custom-reader-play" class="custom-reader-icon" title="' + recipeRead.title.play + '">' + recipeRead.icon.play + '</span>'),
        pause = jQuery('<span id="custom-reader-pause" class="custom-reader-icon" title="' + recipeRead.title.pause + '">' + recipeRead.icon.pause + '</span>').hide(),
        repeat = jQuery('<span id="custom-reader-repeat" class="custom-reader-icon" title="' + recipeRead.title.repeat + '">' + recipeRead.icon.repeat + '</span>').hide(),
        prev = jQuery('<span id="custom-reader-prev" class="custom-reader-icon" title="' + recipeRead.title.prev + '">' + recipeRead.icon.prev + '</span>'),
        next = jQuery('<span id="custom-reader-next" class="custom-reader-icon" title="' + recipeRead.title.next + '">' + recipeRead.icon.next + '</span>'),
        stop = jQuery('<span id="custom-reader-stop" class="custom-reader-icon" title="' + recipeRead.title.stop + '">' + recipeRead.icon.stop + '</span>');
        
        reader
        .append(prev)
        .append(play)
        .append(pause)
        .append(repeat)
        .append(next)
        .append(stop);
        
        reader = jQuery('<div id="custom-reader-container"></div>').append(reader);

        jQuery('body').append(reader);
    }
    reader.addClass( 'open' );
    tts.readStep();
};

tts.closeReader = function () {
    var reader = jQuery('#custom-reader-container');
    if (reader.length > 0) {
        reader.clearQueue();
        reader.removeClass( 'open' );
    }  
    tts.thisBullet.removeClass('reading read');
    tts.cancel();      
};

tts.readStep = function () {
    console.log("In readStep for step : " + tts.step);
    
    var instructions = jQuery(document).find('ul.wpurp-recipe-instruction-container');
    var thisInstruction = instructions.find('#wpurp_recipe_instruction' + tts.step);
    var thisText = thisInstruction.find('.recipe-instruction-text').text();

    // console.log("thisInstruction : look for " + '#wpurp_recipe_instruction' + tts.step );
    // console.log("thisInstruction.html() = " + thisInstruction.html() );
    // console.log("thisInstruction.height() = " + thisInstruction.height() );
    console.log("thisText = " + thisText );
    
    tts.thisBullet.removeClass('reading read');
    tts.thisBullet = instructions.find('#recipe-instruction-bullet' + tts.step);
    tts.thisBullet.addClass('reading');
    jQuery(window).scrollTop(jQuery('#recipe-instruction-bullet' + tts.step).offset().top - thisInstruction.height() -200 );

    // var selector = 'a#lightbox';
    // var instance = jQuery(selector);

    // instance.imageLightbox({
    //     selector: 'id="imagelightbox"',   // string;
    //     // allowedTypes:   'png|jpg|jpeg|gif',     // string;
    //     allowedTypes: 'png|jpg|jpeg',     // string;
    //     animationSpeed: 250,                    // integer;
    //     preloadNext: true,                   // bool;            silently preload the next image
    //     enableKeyboard: true,                   // bool;            enable keyboard shortcuts (arrows Left/Right and Esc)
    //     quitOnEnd: false,                  // bool;            quit after viewing the last image
    //     quitOnImgClick: false,                  // bool;            quit when the viewed image is clicked
    //     quitOnDocClick: true,                   // bool;            quit when anything but the viewed image is clicked
    //     onStart: function () { overlayOn(); closeButtonOn(instance); arrowsOn(selector); },
    //     onEnd: function () { overlayOff(); captionOff(); closeButtonOff(); arrowsOff(); activityIndicatorOff(); },
    //     // onLoadStart:    function() { captionOff(); activityIndicatorOn(); },
    //     onLoadStart: function () { activityIndicatorOn(); },
    //     // onLoadEnd:      function() { captionOn(); activityIndicatorOff(); jQuery( '.imagelightbox-arrow' ).css( 'display', 'block' ); }
    //     onLoadEnd: function () { activityIndicatorOff(); jQuery('.imagelightbox-arrow').css('display', 'block'); }
    // });
    
    tts.play(thisText);
};


/* BASIC SERVICES
------------------------------------------------------------*/

tts.toggleStep = function () {
    console.log("In readStep for step : " + tts.step);
    tts.toggle();
};

tts.readStepBegins = function() {
    console.log( "Read begins for step " + tts.step );
    jQuery('#custom-reader-pause').show();
    jQuery('#custom-reader-play').hide();
    jQuery('#custom-reader-repeat').hide();
};

tts.readStepEnds = function () {
    console.log( "Read ends for step " + tts.step )
    jQuery('#custom-reader-pause').hide();
    jQuery('#custom-reader-play').hide();
    jQuery('#custom-reader-repeat').show();
    tts.thisBullet.removeClass('reading');
    tts.thisBullet.addClass('read');
};

tts.play = function( text ) {
    var voice = recipeRead.voice;
    console.log( "In play " );
    
    tts.playing=true;
    responsiveVoice.speak(text, voice, { onstart: tts.readStepBegins, onend: tts.readStepEnds });
    console.log( "tts.playing = " + tts.playing );
};

tts.toggle = function () {
    console.log("In toggle ");
    if (tts.playing) {
        console.log("Is playing => pause ");
        jQuery('#custom-reader-pause').hide();
        jQuery('#custom-reader-play').show();        
        tts.playing = false;
        responsiveVoice.pause();
    }
    else {
        console.log("Is not playing => resume ");
        jQuery('#custom-reader-pause').show();
        jQuery('#custom-reader-play').hide();        
        tts.playing = true;
        responsiveVoice.resume();
    }
    console.log( "tts.playing = " + tts.playing );
};
    
tts.cancel = function () {
    console.log("In cancel ");
    tts.playing = false;
    responsiveVoice.cancel();
    console.log( "tts.playing = " + tts.playing );
};