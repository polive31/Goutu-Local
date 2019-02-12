jQuery(document).ready(function() {

    jQuery('.csf-delete-post').removeClass('nodisplay');

    jQuery('.csf-delete-post').on('click', function() {
        // console.log('Click on recipe delete !');
        var button = jQuery(this);
        if(confirm(custom_posts_list.confirm_message + ' ' + button.data('title'))) {
            var data = {
                action: 'user_delete_post',
                security: custom_posts_list.nonce,
                post: button.data('id')
            };

            jQuery.post(
                custom_posts_list.ajaxurl, 
                data,
                function(response){
                    console.log(  response );
                    button.closest('tr.recipe-list-row').remove();
                }
            );
        }
    });    

});