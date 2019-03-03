jQuery(document).ready(function() {

    // console.log( 'In custom posts list. js');

    jQuery('.cpm-delete-post').removeClass('nodisplay');

    jQuery('.cpm-delete-post').on('click', function() {
        // console.log('Click on recipe delete !');
        var button = jQuery(this);

        console.log('Closest row is : ', button.closest('tr.post-list-row') );

        if(confirm(custom_posts_list.confirm_message + ' ' + button.data('title'))) {
            var data = {
                action: 'cpm_delete_post',
                security: custom_posts_list.nonce,
                post: button.data('id')
            };

            jQuery.post(
                custom_posts_list.ajaxurl, 
                data,
                function(response){
                    console.log(  response );
                    button.closest('tr.post-list-row').remove();
                }
            );
        }
    });    

});