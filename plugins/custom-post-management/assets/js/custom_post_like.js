jQuery(document).ready(function(){

    console.log('%c In cpm-like ', 'background: #faf; color: #fff');

    jQuery(document).on('click', '.toolbar-button#like a', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var button = jQuery(this);
        var icon=button.find('i');
        var tooltip = button.parent().find('div.toggle');
        var postId = button.data('post-id');
        var postType = custom_post_like.post_type;

        icon.toggleClass('far fas');
        // icon.toggleClass('fas');

        if ( typeof ga == 'function' ) {
            ga('send', 'event', 'like', 'click', postType, 0);
        }

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
                console.log('%c like post response = ', 'background: #faf; color: #fff', response);
                button.find(".button-caption").html(response.data);
                tooltip.toggle()
            },
            error : function( request, error ) {
                console.log('%c like post failed', 'background: #faf; color: #fff' );
            }
        });

    });

});
