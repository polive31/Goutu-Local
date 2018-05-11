jQuery(document).ready(function(){
    console.log('Social like Post loaded !');
    jQuery(document).on('click', '.social-like-post.logged-in', function(e) {
        console.log('Click on like detected !');

        // console.log( 'ajaxurl = ' + custom_post_like.ajaxurl );
        // console.log( 'ajaxnonce = ' + custom_post_like.nonce );

        e.preventDefault();
        e.stopPropagation();

        var button = jQuery(this);
                var tooltip=button.parent().find('div.toggle');
                
                if(button.hasClass('is-liked')) {
                    // Activate shopping list button
                    button.removeClass('is-liked');
                }
                else {
                    button.addClass('is-liked');
                }
                tooltip.toggle();
                //console.log('Tooltip %0', tooltip);
                
                // Prep ajax call
        var postId = button.data('post-id');
                console.log('post ID :'+ postId);

        var data = {
          action: 'like_post',
          security: custom_post_like.nonce,
          post_id: postId
        };

        jQuery.ajax({
            url : custom_post_like.ajaxurl,
            type : 'post',
            data : data,
            success : function( response ) {
                console.log(response);
                // button.closest( ".button-caption" ).html(response);
                button.find(".button-caption").html(response);
            },
            error : function( request, error ) {
                console.log( 'Failure' );
            }            
        });        

    });

});