jQuery(document).ready(function() {

    jQuery('.user-submissions-delete-recipe').removeClass('nodisplay');

    jQuery('.user-submissions-delete-recipe').on('click', function() {
        var button = jQuery(this);
        if(confirm(custom_user_submissions_list.confirm_message + ' ' + button.data('title'))) {
            var data = {
                action: 'custom_user_submissions_delete_recipe',
                security: custom_user_submissions_list.nonce,
                recipe: button.data('id')
            };

            jQuery.post(
                custom_user_submissions_list.ajaxurl, 
                data,
                function(response){
                    console.log(  response );
                    button.closest('tr.recipe-list-row').remove();
                });
        }
    });    

});