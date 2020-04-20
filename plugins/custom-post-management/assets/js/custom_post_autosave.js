// var iterations = 1;
var xhr_autosave = null;
var formUpdated = 0;

const _longInterval=30000;

jQuery(document).ready(function () {

    console.log('%c In custom ' + custom_post_autosave.post_type + ' autosave script', 'background: #ff7; color: blue');
    var post_status = jQuery('#submit_post_status').val();

    if (post_status=='restored' || post_status=='auto-draft') {
        var interval = setInterval(function () {
            autoSave();
        }, _longInterval);
    }
    else {
        console.log('%c Post status is %s, autosave disabled', 'background: #ff7; color: blue', post_status);
    }


    var $meta = jQuery('#submission_form_meta_info');
    var $mainContainer = jQuery('#genesis-content');

    var mainPos = $mainContainer.position();
    var mainWidth = $mainContainer.width();
    var headerHeight = jQuery('.site-header').height();
    $meta.css('top', headerHeight * 1.2);
    $meta.css('left', mainPos.left + mainWidth - $meta.width() -10);

    function autoSave() {
        if (typeof formSubmitting!='undefined' && formSubmitting) {
            console.log('Entering autosave but detecting form submission is ongoing : abort autosave and stop periodic triggering');
            clearInterval( interval );
            return;
        }

        tinyMCE.triggerSave();
        console.log('%c ...Autosave triggered !', 'background: #ff7; color: blue');

        // if (typeof iterations!='undefined') {
        //     iterations++;
        //     if (iterations >= 1) {
        //         console.log('%c Max number of autoSave() iterations reached, aborting.', 'background: #ff7; color: blue');
        //         clearInterval(interval);
        //     }
        // }

        // Check whenever there is an ongoing ajax call on this ingredient
        try {
            xhr_autosave.abort();
            console.log("%c Ajax call already ongoing, aborting.", 'background: #ff7; color: blue');
        }
        catch (e) {
            console.log("%c No previous autosave ajax call, ...", 'background: #ff7; color: blue');
        }

        var data = {
            action: custom_post_autosave.post_type+'_autosave',
            post_data: jQuery('#new_post').serialize(),
            security: custom_post_autosave.nonce,
        };

        console.table(data);

        xhr_autosave = jQuery.post(
            custom_post_autosave.ajaxurl,
            data,
            function (response) {
                console.table(response);
                if (response.success) {
                    console.log('Ajax call suceeded ! ' + custom_post_autosave.post_type + ' autosaved to id : ', response.data);
                    $meta.text(response.data.modified);
                    $meta.fadeTo(2000, 1);

                    setTimeout(function () {
                        $meta.fadeTo(2000, 0);
                    }, 10000);
                }
            }
        );

    }

});
