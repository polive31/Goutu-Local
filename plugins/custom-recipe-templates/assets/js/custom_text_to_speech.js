jQuery(document).ready(function () {
    console.log("In custom text to speech script");

    // Prevent context menu to appear on long touch
    jQuery(document).on("click", ".recipe-button#read", function () {
        console.log( "Click on read !");
        var count = 0;
        var voice = recipeRead.voice; 

        // var instructions = jQuery(this).parent('.recipe-top').siblings('.recipe-container#main').find('ul.wpurp-recipe-instruction-container');
        var instructions = jQuery(document).find('ul.wpurp-recipe-instruction-container');
        // console.log ("instructions", instructions.html() );
        var cleantext = instructions.find('#recipe-instruction-text' + count).text();
        // console.log ("cleantext", cleantext );

        if (responsiveVoice.isPlaying()) {
            responsiveVoice.cancel();
        } else {
            responsiveVoice.speak( cleantext, voice);
        }

    });
    
});